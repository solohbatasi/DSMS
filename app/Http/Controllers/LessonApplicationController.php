<?php

namespace App\Http\Controllers;

use App\Models\LessonApplication;
use App\Models\Course;
use App\Models\Student;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LessonApplicationController extends Controller
{
    public function create(Request $request)
    {
        $branches = Branch::where('status', 1)->get();
        $selectedCourse = $request->query('course');
        $selectedBranch = $request->query('branch');

        // Get courses based on selected branch
        if ($selectedBranch) {
            $courses = Course::where('status', 1)
                ->where('branch_id', $selectedBranch)
                ->get();
        } else {
            $courses = Course::where('status', 1)->get();
        }

        return view('book-lessons', compact('courses', 'selectedCourse', 'branches', 'selectedBranch'));
    }

    // Store new application
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Student information
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'id_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',

            // Branch and Course information
            'branch_id' => 'required|exists:branches,id',
            'course_id' => 'required|exists:courses,id',
            'preferred_start_date' => 'required|date|after:today',
            'preferred_schedule' => 'required|in:weekday_morning,weekday_afternoon,weekday_evening,weekend_morning,weekend_afternoon,flexible',
            'special_requirements' => 'nullable|string|max:1000',

            // Terms acceptance
            'terms_accepted' => 'required|accepted',
        ]);

        DB::beginTransaction();

        try {
            // Check if student already exists
            $student = null;
            if ($request->id_number) {
                $student = Student::where('id_no', $request->id_number)->first();
            }

            // Create application
            $application = LessonApplication::create([
                'application_number' => LessonApplication::generateApplicationNumber(),
                'student_id' => $student?->id,
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'id_number' => $validated['id_number'],
                'address' => $validated['address'],
                'branch_id' => $validated['branch_id'],
                'course_id' => $validated['course_id'],
                'preferred_start_date' => $validated['preferred_start_date'],
                'preferred_schedule' => $validated['preferred_schedule'],
                'special_requirements' => $validated['special_requirements'],
                'status' => 'pending',
            ]);

            DB::commit();

            return redirect()->route('application.success', $application->application_number)
                ->with('success', 'Your application has been submitted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Application submission error: ' . $e->getMessage());

            return back()->with('error', 'Sorry, there was an error submitting your application. Please try again.')
                ->withInput();
        }
    }

    // Show success page
    public function success($applicationNumber)
    {
        $application = LessonApplication::where('application_number', $applicationNumber)->firstOrFail();

        return view('application-success', compact('application'));
    }

    // Admin methods with branch filtering
    public function index(Request $request)
    {
        $user = Auth::user();

        // Base query with branch filtering
        $query = LessonApplication::with(['course', 'student', 'course.branch']);

        // Apply branch filtering based on user role and branch selection
        if ($user->isAdmin()) {
            // Admin: Check if viewing a specific branch or all branches
            $selectedBranchId = session('admin_selected_branch');

            if ($selectedBranchId) {
                // Admin is viewing a specific branch
                $query->where('branch_id', $selectedBranchId);
            }
            // If $selectedBranchId is null/empty, admin is viewing all branches (no additional filter)
        } else {
            // Non-admin users: only show applications from their assigned branch
            $query->where('branch_id', $user->branch_id);
        }

        // Additional branch filter from request (for admin branch switcher UI)
        if ($user->isAdmin() && $request->has('branch_id')) {
            $branchId = $request->branch_id;
            if ($branchId) {
                $query->where('branch_id', $branchId);
            }
        }

        // Filter by status if provided
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('application_number', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('id_number', 'like', "%{$search}%")
                  ->orWhereHas('course', function($q) use ($search) {
                      $q->where('course', 'like', "%{$search}%");
                  });
            });
        }

        $applications = $query->latest()->paginate(20);

        // Counts with branch filtering
        $countQuery = LessonApplication::query();

        // Apply the same branch filtering to counts
        if ($user->isAdmin()) {
            $selectedBranchId = session('admin_selected_branch');
            if ($selectedBranchId) {
                $countQuery->where('branch_id', $selectedBranchId);
            }
        } else {
            $countQuery->where('branch_id', $user->branch_id);
        }

        $stats = [
            'pending' => $countQuery->where('status', 'pending')->count(),
            'under_review' => $countQuery->where('status', 'under_review')->count(),
            'approved' => $countQuery->where('status', 'approved')->count(),
            'rejected' => $countQuery->where('status', 'rejected')->count(),
            'enrolled' => $countQuery->where('status', 'enrolled')->count(),
            'total' => $countQuery->count(),
        ];

        // Get branches for admin filter
        $branches = $user->isAdmin() ? Branch::active()->get() : collect();

        // Get selected branch info for display
        $selectedBranch = null;
        if ($user->isAdmin()) {
            $selectedBranchId = session('admin_selected_branch');
            $selectedBranch = $selectedBranchId ? Branch::find($selectedBranchId) : null;
        }

        return view('admin.applications.index', [
            'applications' => $applications,
            'stats' => $stats,
            'branches' => $branches,
            'selectedBranch' => $request->branch_id ?: ($selectedBranch ? $selectedBranch->id : null),
            'viewingBranch' => $selectedBranch
        ]);
    }

    public function show(LessonApplication $application)
    {
        $user = Auth::user();

        // Check if user can access this application
        if (!$user->isAdmin() && $application->branch_id != $user->branch_id) {
            abort(403, 'You do not have permission to access this application.');
        }

        $application->load(['course', 'student', 'reviewer', 'course.branch']);

        return view('admin.applications.show', compact('application'));
    }

    public function updateStatus(Request $request, LessonApplication $application)
    {
        $user = Auth::user();

        // Check if user can update this application
        if (!$user->isAdmin() && $application->branch_id != $user->branch_id) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to update this application.'
                ], 403);
            }
            abort(403, 'You do not have permission to update this application.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,under_review,approved,rejected,enrolled',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $application->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Application status updated successfully.',
                'status' => $application->status
            ]);
        }

        return back()->with('success', 'Application status updated successfully.');
    }

    public function destroy(LessonApplication $application)
    {
        $user = Auth::user();

        // Check if user can delete this application
        if (!$user->isAdmin() && $application->branch_id != $user->branch_id) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to delete this application.'
                ], 403);
            }
            abort(403, 'You do not have permission to delete this application.');
        }

        $application->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Application deleted successfully.'
            ]);
        }

        return redirect()->route('admin.applications.index')
            ->with('success', 'Application deleted successfully.');
    }

    // Search applications (for AJAX search)
    public function search(Request $request)
    {
        $user = Auth::user();
        $query = $request->get('q');

        // Start the query with branch filtering
        $searchQuery = LessonApplication::with(['course', 'student', 'course.branch']);

        // Apply branch filtering based on user role and branch selection
        if ($user->isAdmin()) {
            $selectedBranchId = session('admin_selected_branch');
            if ($selectedBranchId) {
                $searchQuery->where('branch_id', $selectedBranchId);
            }
        } else {
            $searchQuery->where('branch_id', $user->branch_id);
        }

        // Only apply search filters if a query is provided
        if (!empty($query)) {
            $searchQuery->where(function($q) use ($query) {
                $q->where('application_number', 'like', "%$query%")
                  ->orWhere('full_name', 'like', "%$query%")
                  ->orWhere('email', 'like', "%$query%")
                  ->orWhere('phone', 'like', "%$query%")
                  ->orWhere('id_number', 'like', "%$query%")
                  ->orWhereHas('course', function($cq) use ($query) {
                      $cq->where('course', 'like', "%$query%");
                  });
            });
        }

        // Apply status filter if provided
        if ($request->has('status') && $request->status != 'all') {
            $searchQuery->where('status', $request->status);
        }

        // Apply sorting
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'oldest':
                    $searchQuery->orderBy('created_at', 'asc');
                    break;
                case 'name_asc':
                    $searchQuery->orderBy('full_name', 'asc');
                    break;
                case 'name_desc':
                    $searchQuery->orderBy('full_name', 'desc');
                    break;
                default:
                    $searchQuery->orderBy('created_at', 'desc');
            }
        } else {
            $searchQuery->orderBy('created_at', 'desc');
        }

        // Limit results
        $searchQuery->limit(20);

        $applications = $searchQuery->get()
            ->map(function($app) use ($user) {
                // Additional security check for admin viewing specific branch
                if ($user->isAdmin()) {
                    $selectedBranchId = session('admin_selected_branch');
                    if ($selectedBranchId && $app->branch_id != $selectedBranchId) {
                        return null; // Skip applications from other branches when admin is viewing a specific branch
                    }
                } elseif (!$user->isAdmin() && $app->branch_id != $user->branch_id) {
                    return null; // Skip applications from other branches for non-admin users
                }

                return [
                    'id' => $app->id,
                    'application_number' => $app->application_number,
                    'full_name' => $app->full_name,
                    'email' => $app->email,
                    'phone' => $app->phone,
                    'course_name' => $app->course->course ?? '-',
                    'branch_name' => $app->course->branch->name ?? 'N/A',
                    'status' => $app->status,
                    'created_at' => $app->created_at->format('M d, Y'),
                    'can_edit' => $user->isAdmin() || $app->branch_id == $user->branch_id,
                    'can_delete' => $user->isAdmin() || $app->branch_id == $user->branch_id
                ];
            })
            ->filter() // Remove null entries (applications from other branches)
            ->values(); // Reindex array

        return response()->json([
            'success' => true,
            'applications' => $applications,
            'count' => $applications->count(),
            'branch_context' => $user->isAdmin() ? session('admin_selected_branch_name', 'All Branches') : ($user->branch->name ?? 'N/A')
        ]);
    }
}
