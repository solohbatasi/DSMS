<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Student_ef_list;
use App\Models\Course;
use App\Models\Payment;
use App\Models\User;

class ReceptionistController extends Controller
{
    public function index()
    {
        return view('dashboard.receptionist');
    }

    public function verifyEFList(Request $request)
    {
        $request->validate([
            'ef_list_id' => 'required|exists:student_ef_lists,id',
            'action' => 'required|in:verified,active'
        ]);

        try {
            $ef_list = Student_ef_list::findOrFail($request->ef_list_id);
            $ef_list->status = $request->action;
            $ef_list->save();

            // Log the action
            \Log::info("EF List {$ef_list->ef_no} {$request->action} by receptionist: " . Auth::user()->name);

            return response()->json([
                'success' => true,
                'message' => "EF list successfully {$request->action}",
                'ef_list' => $ef_list
            ]);

        } catch (\Exception $e) {
            \Log::error('EF List verification error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to verify EF list. Please try again.'
            ], 500);
        }
    }

    public function students()
    {
        $students = Student::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('receptionist.students.index', compact('students'));
    }

    public function createStudent()
    {
        return view('receptionist.students.create');
    }

    public function storeStudent(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'id_no' => 'required|string|max:255|unique:students',
            'email' => 'required|email|unique:students',
            'contact' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        try {
            $student = Student::create($request->all());

            return redirect()->route('receptionist.students')
                ->with('success', 'Student registered successfully!');

        } catch (\Exception $e) {
            \Log::error('Student registration error: ' . $e->getMessage());

            return back()->with('error', 'Failed to register student. Please try again.');
        }
    }

    public function efLists()
    {
        $ef_lists = Student_ef_list::with(['student', 'course'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $courses = Course::where('status', 1)->get();

        return view('receptionist.ef-lists.index', compact('ef_lists', 'courses'));
    }

    public function createEFList()
    {
        $students = Student::all();
        $courses = Course::where('status', 1)->get();

        return view('receptionist.ef-lists.create', compact('students', 'courses'));
    }

    public function storeEFList(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'total_fee' => 'required|numeric|min:0',
            'total_class' => 'required|integer|min:1',
            'type' => 'required|in:3,4',
        ]);

        try {
            // Generate unique EF number
            $ef_no = 'EF' . date('Ymd') . str_pad(Student_ef_list::count() + 1, 4, '0', STR_PAD_LEFT);

            $ef_list = Student_ef_list::create([
                'student_id' => $request->student_id,
                'course_id' => $request->course_id,
                'ef_no' => $ef_no,
                'total_fee' => $request->total_fee,
                'total_class' => $request->total_class,
                'type' => $request->type,
                'status' => 'pending',
                'paid_amount' => 0,
                'balance' => $request->total_fee,
            ]);

            return redirect()->route('receptionist.ef-lists')
                ->with('success', "EF list created successfully! EF Number: {$ef_no}");

        } catch (\Exception $e) {
            \Log::error('EF List creation error: ' . $e->getMessage());

            return back()->with('error', 'Failed to create EF list. Please try again.');
        }
    }

    public function payments()
    {
        $payments = Payment::with(['efList.student', 'efList.course'])
            ->orderBy('payment_date', 'desc')
            ->paginate(20);

        $ef_lists = Student_ef_list::where('status', 'active')->get();

        return view('receptionist.payments.index', compact('payments', 'ef_lists'));
    }

    public function createPayment()
    {
        $ef_lists = Student_ef_list::where('status', 'active')->get();

        return view('receptionist.payments.create', compact('ef_lists'));
    }

    public function storePayment(Request $request)
    {
        $request->validate([
            'ef_list_id' => 'required|exists:student_ef_lists,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        try {
            $ef_list = Student_ef_list::findOrFail($request->ef_list_id);

            // Create payment record
            $payment = Payment::create([
                'ef_list_id' => $request->ef_list_id,
                'amount' => $request->amount,
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
                'payment_status' => 'completed',
            ]);

            // Update EF list balances
            $ef_list->paid_amount += $request->amount;
            $ef_list->balance = $ef_list->total_fee - $ef_list->paid_amount;
            $ef_list->last_payment_date = $request->payment_date;
            $ef_list->save();

            return redirect()->route('receptionist.payments')
                ->with('success', 'Payment recorded successfully!');

        } catch (\Exception $e) {
            \Log::error('Payment recording error: ' . $e->getMessage());

            return back()->with('error', 'Failed to record payment. Please try again.');
        }
    }

    public function reports()
    {
        // Generate various reports
        $monthly_revenue = Payment::select(
                DB::raw('MONTH(payment_date) as month'),
                DB::raw('YEAR(payment_date) as year'),
                DB::raw('SUM(amount) as total')
            )
            ->where('payment_status', 'completed')
            ->whereYear('payment_date', date('Y'))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $course_enrollments = Student_ef_list::select('course_id')
            ->selectRaw('COUNT(*) as count')
            ->with('course')
            ->groupBy('course_id')
            ->get();

        $payment_methods = Payment::select('payment_method')
            ->selectRaw('COUNT(*) as count, SUM(amount) as total')
            ->where('payment_status', 'completed')
            ->groupBy('payment_method')
            ->get();

        return view('receptionist.reports.index', compact(
            'monthly_revenue',
            'course_enrollments',
            'payment_methods'
        ));
    }
}
