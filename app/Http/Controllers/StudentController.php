<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Payment;
use App\Models\Course;
use App\Models\Student_ef_list;
use App\Models\Branch;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Base query with branch filtering
        $query = Student_ef_list::with(['student', 'course', 'payments']);

        // Apply branch filtering based on user role and branch selection
        if ($user->isAdmin()) {
            // Admin: Check if viewing a specific branch or all branches
            $selectedBranchId = session('admin_selected_branch');

            if ($selectedBranchId) {
                // Admin is viewing a specific branch
                $query->where('branch_id', $selectedBranchId);

                // Also filter by branch_id in students table for consistency
                $query->whereHas('student', function ($q) use ($selectedBranchId) {
                    $q->where('branch_id', $selectedBranchId);
                });
            }
            // If $selectedBranchId is null/empty, admin is viewing all branches (no additional filter)
        } else {
            // Non-admin users: only show students from their assigned branch
            $query->where('branch_id', $user->branch_id);

            // Also filter by branch_id in students table for consistency
            $query->whereHas('student', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            });
        }

        // Additional branch filter from request (for admin branch switcher UI)
        if ($user->isAdmin() && $request->has('branch_id')) {
            $branchId = $request->branch_id;
            if ($branchId) {
                $query->where('branch_id', $branchId);
                $query->whereHas('student', function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                });
            }
        }

        // Counts with branch filtering
        $countQuery = Student_ef_list::query();

        // Apply the same branch filtering to counts
        if ($user->isAdmin()) {
            $selectedBranchId = session('admin_selected_branch');
            if ($selectedBranchId) {
                $countQuery->where('branch_id', $selectedBranchId);
            }
        } else {
            $countQuery->where('branch_id', $user->branch_id);
        }

        $totalStudents = $countQuery->count();
        $activeStudents = $countQuery->where('type', 3)->count();
        $alumniStudents = $countQuery->where('type', 4)->count();
        $suspendedStudents = $countQuery->where('type', 2)->count();

        // Get courses available for user's branch/viewing context
        $coursesQuery = Course::query();

        if ($user->isAdmin()) {
            $selectedBranchId = session('admin_selected_branch');
            if ($selectedBranchId) {
                $coursesQuery->where('branch_id', $selectedBranchId);
            }
            // If no branch selected, show all courses (including those without branch assignment)
        } else {
            $coursesQuery->where('branch_id', $user->branch_id);
        }

        $courses = $coursesQuery->get();

        // Get branches for admin filter
        $branches = $user->isAdmin() ? Branch::active()->get() : collect();

        $students = $query->latest()->paginate(10);

        // Get selected branch info for display
        $selectedBranch = null;
        if ($user->isAdmin()) {
            $selectedBranchId = session('admin_selected_branch');
            $selectedBranch = $selectedBranchId ? Branch::find($selectedBranchId) : null;
        }

        return view('students.index', [
            'students' => $students,
            'courses' => $courses,
            'branches' => $branches,
            'totalStudents' => $totalStudents,
            'activeStudents' => $activeStudents,
            'alumniStudents' => $alumniStudents,
            'suspendedStudents' => $suspendedStudents,
            'selectedBranch' => $request->branch_id ?: ($selectedBranch ? $selectedBranch->id : null),
            'viewingBranch' => $selectedBranch
        ]);
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
                'branch_id' => 'required|exists:branches,id',
                'ef_no' => 'required|unique:student_ef_lists,ef_no',
                'course_id' => 'required|exists:courses,id',
                'total_fee' => 'required|numeric',
                'total_class' => 'required|integer|min:1',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        }

        // Check if user can assign to this branch
        $user = Auth::user();
        if (!$user->isAdmin() && $request->branch_id != $user->branch_id) {
            return response()->json([
                'success' => false,
                'message' => 'You can only add students to your own branch.'
            ], 403);
        }

        try {
            DB::beginTransaction();

           // Create user first
            $password = 'password';

            // Generate email if not provided
            if (!$request->has('email') || empty($request->email)) {
                // First, convert to lowercase
                $lowercaseName = Str::lower($request->name);

                // Remove all spaces
                $noSpaces = str_replace(' ', '', $lowercaseName);

                // Remove any non-alphanumeric characters (keep letters and numbers only)
                $clean = preg_replace('/[^a-z0-9]/', '', $noSpaces);

                // Debug: Check what's happening
                // \Log::info('Email generation', [
                //     'original' => $request->name,
                //     'lowercase' => $lowercaseName,
                //     'no_spaces' => $noSpaces,
                //     'clean' => $clean,
                // ]);

                // If result is empty or too short, use a default
                if (strlen($clean) < 3) {
                    $clean = 'student' . rand(100, 999);
                }

                $email = $clean . '@gmail.com';

                // Make email unique
                $counter = 1;
                $originalEmail = $email;
                while (User::where('email', $email)->exists()) {
                    $email = $clean . $counter . '@gmail.com';
                    $counter++;

                    // Safety limit
                    if ($counter > 100) {
                        $email = 'student_' . time() . '@gmail.com';
                        break;
                    }
                }
            } else {
                // If email is provided, clean it
                $email = Str::lower(trim($request->email));
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'student',
                'branch_id' => $request->branch_id,
                'email_verified_at' => now(),
            ]);

            $user->assignRole('student');

            // Create student linked to user
            $student = Student::create([
                'user_id' => $user->id,
                'id_no' => $request->id_no,
                'name' => $request->name,
                'contact' => $request->contact,
                'address' => $request->address,
                'email' => $request->email,
                'branch_id' => $request->branch_id,
            ]);

            // Calculate remaining practical and theoretical sessions
            $practicalSessions = $course->practical_sessions ?? 0;
            $theoreticalSessions = $course->theoretical_sessions ?? 0;

            // If course doesn't have sessions defined, use total_class
            if ($practicalSessions == 0 && $theoreticalSessions == 0) {
                // You might want to split total_class into practical and theoretical
                // For example: 70% practical, 30% theoretical
                $practicalSessions = (int) ceil($request->total_class * 0.7);
                $theoreticalSessions = $request->total_class - $practicalSessions;
            }

            // Create EF list
            $ef = Student_ef_list::create([
                'student_id' => $student->id,
                'ef_no' => $request->ef_no,
                'course_id' => $request->course_id,
                'total_fee' => $request->total_fee,
                'balance' => $request->total_fee,
                'total_class' => $request->total_class,
                // Practical sessions
                'attended_practical' => 0,
                'remaining_practical' => $practicalSessions,

                // Theoretical sessions
                'attended_theoretical' => 0,
                'remaining_theoretical' => $theoreticalSessions,
                'type' => $request->type,
                'branch_id' => $request->branch_id,
                'status' => 'active',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Student enrolled successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Try again.',
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = Auth::user();
        $ef = Student_ef_list::with('student', 'course')->findOrFail($id);

        // Check if user can access this student
        if (!$user->isAdmin() && $ef->branch_id != $user->branch_id) {
            abort(403, 'You do not have permission to access this student.');
        }

        // Get branches for admin
        $branches = $user->isAdmin() ? Branch::active()->get() : null;

        return response()->json([
            'ef' => $ef,
            'student' => $ef->student,
            'course' => $ef->course,
            'branches' => $branches
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $ef = Student_ef_list::findOrFail($id);
        $student = $ef->student;
        $user = Auth::user();

        // Check if user can access this student
        if (!$user->isAdmin() && $ef->branch_id != $user->branch_id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update this student.'
            ], 403);
        }

        $validationRules = [
            'id_no' => 'required|unique:students,id_no,' . $student->id,
            'name' => 'required|string',
            'contact' => 'nullable|string',
            'email' => 'nullable|unique:users,email,' . ($student->user_id ?? 'NULL') . ',id',
            'address' => 'nullable|string',
            'ef_no' => 'required|unique:student_ef_lists,ef_no,' . $ef->id,
            'course_id' => 'required|exists:courses,id',
            'total_fee' => 'required|numeric',
            'total_class' => 'required|integer|min:1',
        ];

        // Only admin can change branch
        if ($user->isAdmin()) {
            $validationRules['branch_id'] = 'required|exists:branches,id';
        }

        try {
            $validated = $request->validate($validationRules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Update student
            $updateData = [
                'id_no' => $request->id_no,
                'name' => $request->name,
                'contact' => $request->contact,
                'address' => $request->address,
                'email' => $request->email,
            ];

            // Only admin can change branch
            if ($user->isAdmin() && $request->has('branch_id')) {
                $updateData['branch_id'] = $request->branch_id;
            }

            $student->update($updateData);

            // Update EF list
            $efUpdateData = [
                'ef_no' => $request->ef_no,
                'course_id' => $request->course_id,
                'total_fee' => $request->total_fee,
                'total_class' => $request->total_class,
            ];

            // Only admin can change branch in EF list
            if ($user->isAdmin() && $request->has('branch_id')) {
                $efUpdateData['branch_id'] = $request->branch_id;
            }

            $ef->update($efUpdateData);

            // Update linked user account
            if ($student->user_id) {
                $userAccount = User::find($student->user_id);
                $userUpdateData = [
                    'name' => $student->name,
                    'email' => $student->email ?? ($student->id_no . '@example.com')
                ];

                // Only admin can change user's branch
                if ($user->isAdmin() && $request->has('branch_id')) {
                    $userUpdateData['branch_id'] = $request->branch_id;
                }

                $userAccount->update($userUpdateData);
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
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $ef = Student_ef_list::findOrFail($id);

        // Check if user can delete this student
        if (!$user->isAdmin() && $ef->branch_id != $user->branch_id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete this student.'
            ], 403);
        }

        $ef->student->delete();
        $ef->delete();

        return response()->json(['success' => true]);
    }

    public function search(Request $request)
    {
        $user = Auth::user();
        $query = $request->get('q');

        // Start the query with branch filtering
        $searchQuery = Student_ef_list::with(['student', 'course', 'payments', 'student.branch']);

        // Apply branch filtering based on user role and branch selection
        if ($user->isAdmin()) {
            // Admin: Check if viewing a specific branch or all branches
            $selectedBranchId = session('admin_selected_branch');

            if ($selectedBranchId) {
                // Admin is viewing a specific branch
                $searchQuery->where('branch_id', $selectedBranchId);
            }
            // If $selectedBranchId is null/empty, admin is viewing all branches (no additional filter)
        } else {
            // Non-admin users: only show students from their assigned branch
            $searchQuery->where('branch_id', $user->branch_id);
        }

        // Only apply search filters if a query is provided
        if (!empty($query)) {
            $searchQuery->where(function($q) use ($query) {
                // Search in student_ef_lists table
                $q->where('ef_no', 'like', "%$query%")
                ->orWhereHas('student', function($sq) use ($query) {
                    // Search in students table
                    $sq->where('name', 'like', "%$query%")
                        ->orWhere('id_no', 'like', "%$query%")
                        ->orWhere('contact', 'like', "%$query%")
                        ->orWhere('email', 'like', "%$query%");
                })
                ->orWhereHas('course', function($cq) use ($query) {
                    // Search in courses table
                    $cq->where('course', 'like', "%$query%");
                });
            });
        }

        // Apply additional filters if provided
        if ($request->has('status') && $request->status != 'all') {
            // Map status values to type values if needed
            $statusMap = [
                'active' => 3,
                'completed' => 4,
                'dropped' => 2,
            ];

            if (isset($statusMap[$request->status])) {
                $searchQuery->where('type', $statusMap[$request->status]);
            }
        }

        // Apply sorting
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'oldest':
                    $searchQuery->orderBy('created_at', 'asc');
                    break;
                case 'fee_high':
                    $searchQuery->orderBy('total_fee', 'desc');
                    break;
                case 'fee_low':
                    $searchQuery->orderBy('total_fee', 'asc');
                    break;
                default:
                    $searchQuery->orderBy('created_at', 'desc');
            }
        } else {
            $searchQuery->orderBy('created_at', 'desc');
        }

        // Limit results
        $searchQuery->limit(20);

        $students = $searchQuery->get()
            ->map(function($ef) use ($user) {
                $totalPaid = $ef->payments->sum('amount');
                $balance = $ef->total_fee - $totalPaid;

                // Additional security check for admin viewing specific branch
                if ($user->isAdmin()) {
                    $selectedBranchId = session('admin_selected_branch');
                    if ($selectedBranchId && $ef->branch_id != $selectedBranchId) {
                        return null; // Skip students from other branches when admin is viewing a specific branch
                    }
                } elseif (!$user->isAdmin() && $ef->branch_id != $user->branch_id) {
                    return null; // Skip students from other branches for non-admin users
                }

                return [
                    'id' => $ef->id,
                    'student_name' => $ef->student->name ?? 'Unknown',
                    'course_name' => $ef->course->course ?? '-',
                    'ef_no' => $ef->ef_no,
                    'total_fee' => number_format($ef->total_fee, 2),
                    'total_paid' => number_format($totalPaid, 2),
                    'balance' => number_format($balance, 2),
                    'branch_name' => $ef->student->branch->name ?? 'N/A',
                    'can_edit' => $user->isAdmin() || $ef->branch_id == $user->branch_id,
                    'can_delete' => $user->isAdmin() || $ef->branch_id == $user->branch_id
                ];
            })
            ->filter() // Remove null entries (students from other branches)
            ->values(); // Reindex array

        return response()->json([
            'success' => true,
            'students' => $students,
            'count' => $students->count(),
            'branch_context' => $user->isAdmin() ? session('admin_selected_branch_name', 'All Branches') : ($user->branch->name ?? 'N/A')
        ]);
    }

    /**
     * Get courses for a specific branch
     */
    public function getBranchCourses($branchId)
    {
        $user = Auth::user();

        // Check if user can access this branch's courses
        if (!$user->isAdmin() && $branchId != $user->branch_id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to access this branch.'
            ], 403);
        }

        $courses = Course::where('branch_id', $branchId)
            ->where('status', 1)
            ->get()
            ->map(function($course) {
                return [
                    'id' => $course->id,
                    'course' => $course->course,
                    'total_amount' => $course->total_amount,
                    'practical_sessions' => $course->practical_sessions,
                    'theoretical_sessions' => $course->theoretical_sessions
                ];
            });

        return response()->json([
            'success' => true,
            'courses' => $courses
        ]);
    }
}
