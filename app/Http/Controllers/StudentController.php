<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Payment;
use App\Models\Course;
use App\Models\Student_ef_list;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $totalStudents =  Student_ef_list::count();

        $activeStudents =  Student_ef_list::where('type', 3)->count();

        $alumniStudents = Student_ef_list::where('type', 4)->count();

        $suspendedStudents =  Student_ef_list::where('type', 2)->count(); // Change 2 if your suspended type is different
        $courses = Course::all();
        $students = Student_ef_list::with([
            'student',
            'course',
            'payments'
        ])
        ->select('*')
        ->paginate(10);

        return view('students.index', [
            'students' => $students,
            'courses' => $courses,
            'totalStudents' => $totalStudents,
            'activeStudents' => $activeStudents,
            'alumniStudents' => $alumniStudents,
            'suspendedStudents' => $suspendedStudents
        ]);

    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_no' => 'required|unique:students,id_no',
                'name' => 'required|string',
                'contact' => 'nullable|string',
                'email' => 'nullable|unique:users,email',
                'address' => 'nullable|string',

                'ef_no' => 'required|unique:student_ef_lists,ef_no',
                'course_id' => 'required|exists:courses,id',
                'total_fee' => 'required|numeric',
                'total_class' => 'required|integer|min:1',
                'type' => '3',
            ]);
        }
        catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $student = Student::create([
                'id_no' => $request->id_no,
                'name' => $request->name,
                'contact' => $request->contact,
                'address' => $request->address,
                'email' => $request->email,
            ]);

            $ef = Student_ef_list::create([
                'student_id' => $student->id,
                'ef_no' => $request->ef_no,
                'course_id' => $request->course_id,
                'total_fee' => $request->total_fee,
                'total_class' => $request->total_class,
                'type' => $request->type,
                'status' => 'active',
            ]);

            // Sync to users table
            $password = $request->id_no ?? 'Password123';
            User::create([
                'name' => $student->name,
                'email' => $student->email ?? $student->id_no . '@example.com', // fallback email if null
                'password' => Hash::make($password),
                'role' => 'student',
            ]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Student enrolled successfully.'
            ]);

        } catch (\Exception $e) {

            \DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Try again.',
                'debug' => $e->getMessage() // remove in production
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $ef = Student_ef_list::with('student')->findOrFail($id);

        return response()->json([
            'ef' => $ef,
            'student' => $ef->student,
              'course' => $ef->course
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $ef = Student_ef_list::findOrFail($id);
        $student = $ef->student;

        try {
            // VALIDATION
            $validated = $request->validate([
                'id_no' => 'required|unique:students,id_no,' . $student->id,
                'name' => 'required|string',
                'contact' => 'nullable|string',
                'email' => 'nullable|unique:users,email,' . $student->id . ',id',
                'address' => 'nullable|string',

                'ef_no' => 'required|unique:student_ef_lists,ef_no,' . $ef->id,
                'course_id' => 'required|exists:courses,id',
                'total_fee' => 'required|numeric',
                'total_class' => 'required|integer|min:1',
            ]);
        }
        catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // UPDATE STUDENT
            $student->update([
                'id_no' => $request->id_no,
                'name' => $request->name,
                'contact' => $request->contact,
                'email' => $request->email,
                'address' => $request->address,
            ]);

            // UPDATE EF LIST
            $ef->update([
                'ef_no' => $request->ef_no,
                'course_id' => $request->course_id,
                'total_fee' => $request->total_fee,
                'total_class' => $request->total_class,
            ]);

            // UPDATE USER ACCOUNT
            $user = User::where('email', $student->email)
                        ->orWhere('name', $student->name)
                        ->first();

            if ($user) {
                $user->update([
                    'name' => $student->name,
                    'email' => $student->email ?? ($student->id_no . '@example.com')
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Student updated successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'debug' => $e->getMessage() // Remove later
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $ef = Student_ef_list::findOrFail($id);
        $ef->student->delete();
        $ef->delete();

        return response()->json(['success' => true]);
    }
  public function search(Request $request)
{
    $query = $request->get('q');

    $students = Student_ef_list::with(['student', 'course', 'payments'])
        ->whereHas('student', function($q) use ($query) {
            $q->where('name', 'like', "%$query%")
              ->orWhere('id_no', 'like', "%$query%")
              ->orWhere('contact', 'like', "%$query%")
              ->orWhere('email', 'like', "%$query%");
        })
        ->limit(20)
        ->get()
        ->map(function($ef) {
            $totalPaid = $ef->payments->sum('amount');
            $balance = $ef->total_fee - $totalPaid;

            return [
                'id' => $ef->id,
                'student_name' => $ef->student->name,
                'course_name' => $ef->course->course ?? '-',
                'ef_no' => $ef->ef_no,
                'total_fee' => number_format($ef->total_fee, 2),
                'total_paid' => number_format($totalPaid, 2),
                'balance' => number_format($balance, 2)
            ];
        });

    return response()->json([
        'success' => true,
        'students' => $students
    ]);
}


}
