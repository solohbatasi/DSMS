<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Base query with eager loading and branch filtering
        $coursesQuery = Course::with(['category', 'branch']);

        // Apply branch filtering
        if ($user->isAdmin()) {
            $selectedBranchId = session('admin_selected_branch');
            if ($selectedBranchId) {
                $coursesQuery->where('branch_id', $selectedBranchId);
            }
        } else {
            $coursesQuery->where('branch_id', $user->branch_id);
        }

        // Filter by category if specified
        if ($request->has('category_id')) {
            $coursesQuery->where('category_id', $request->category_id);
        }

        // Get counts with branch filtering
        $countQuery = Course::query();

        if ($user->isAdmin()) {
            $selectedBranchId = session('admin_selected_branch');
            if ($selectedBranchId) {
                $countQuery->where('branch_id', $selectedBranchId);
            }
        } else {
            $countQuery->where('branch_id', $user->branch_id);
        }

        $totalCourses = $countQuery->count();
        $activeCourses = $countQuery->clone()->where('status', 1)->count();
        $inactiveCourses = $countQuery->clone()->where('status', 0)->count();

        // Popular courses (highest fee active courses)
        $popularCourses = $coursesQuery->clone()
            ->where('status', 1)
            ->orderBy('total_amount', 'desc')
            ->take(5)
            ->get();

        $courses = $coursesQuery->orderBy('course')->paginate(10);

        // Get categories for dropdown (filtered by branch)
        $categoriesQuery = CourseCategory::where('status', 1);

        $categories = $categoriesQuery->orderBy('name')->get();

        // Get branches for admin filter
        $branches = $user->isAdmin() ? Branch::active()->get() : collect();

        // Get selected branch info
        $selectedBranch = null;
        if ($user->isAdmin()) {
            $selectedBranchId = session('admin_selected_branch');
            $selectedBranch = $selectedBranchId ? Branch::find($selectedBranchId) : null;
        }

        return view('courses.index', [
            'courses' => $courses,
            'totalCourses' => $totalCourses,
            'activeCourses' => $activeCourses,
            'inactiveCourses' => $inactiveCourses,
            'popularCourses' => $popularCourses,
            'categories' => $categories,
            'branches' => $branches,
            'selectedBranch' => $request->branch_id ?: ($selectedBranch ? $selectedBranch->id : null),
            'viewingBranch' => $selectedBranch,
            'selectedCategory' => $request->category_id
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        try {
            $validated = $request->validate([
                'course' => 'required|string|max:255|unique:courses,course',
                'category_id' => 'required|exists:course_categories,id',
                'description' => 'nullable|string',
                'training_duration' => 'required|string|max:255',
                'practical_sessions' => 'required|integer|min:0',
                'theoretical_sessions' => 'required|integer|min:0',
                'total_amount' => 'required|numeric|min:0',
                'status' => 'required|in:0,1',
                'branch_id' => $user->isAdmin() ? 'required|exists:branches,id' : 'nullable',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            // Set branch for non-admin users
            if (!$user->isAdmin()) {
                $validated['branch_id'] = $user->branch_id;
            } else {
                // Admin can use selected branch if not specified
                $selectedBranchId = session('admin_selected_branch');
                if ($selectedBranchId && !$request->has('branch_id')) {
                    $validated['branch_id'] = $selectedBranchId;
                }
            }

            // Verify category belongs to same branch


            // Validate session totals match duration
            preg_match('/(\d+)/', $request->training_duration, $matches);
            $duration = $matches[1] ?? 0;
            $totalSessions = $request->practical_sessions + $request->theoretical_sessions;

            if ($duration > 0 && $totalSessions != $duration) {
                return response()->json([
                    'success' => false,
                    'errors' => [
                        'practical_sessions' => ["Total sessions ($totalSessions) must match training duration ($duration sessions)"]
                    ]
                ], 422);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('course-images', 'public');
            }

            $course = Course::create([
                'course' => $request->course,
                'category_id' => $request->category_id,
                'description' => $request->description,
                'training_duration' => $request->training_duration,
                'practical_sessions' => $request->practical_sessions,
                'theoretical_sessions' => $request->theoretical_sessions,
                'total_amount' => $request->total_amount,
                'status' => $request->status,
                'branch_id' => $validated['branch_id'],
                'image' => $imagePath
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Course created successfully.',
                'course' => $course->load('category')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Course store error: ' . $e->getMessage());
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
        $course = Course::with(['category'])->findOrFail($id);

        // Check if user has permission to edit this course
        if (!$user->isAdmin() && $course->branch_id != $user->branch_id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit this course.'
            ], 403);
        }

        // Get categories for the same branch
        $categories = CourseCategory::where('status', 1)
            ->where('branch_id', $course->branch_id)
            ->orderBy('name')
            ->get();

        return response()->json([
            'course' => $course,
            'categories' => $categories
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $course = Course::findOrFail($id);

        // Check if user has permission to update this course
        if (!$user->isAdmin() && $course->branch_id != $user->branch_id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update this course.'
            ], 403);
        }

        try {
            $validated = $request->validate([
                'course' => 'required|string|max:255|unique:courses,course,' . $course->id,
                'category_id' => 'required|exists:course_categories,id',
                'description' => 'nullable|string',
                'training_duration' => 'required|string|max:255',
                'practical_sessions' => 'required|integer|min:0',
                'theoretical_sessions' => 'required|integer|min:0',
                'total_amount' => 'required|numeric|min:0',
                'status' => 'required|in:0,1',
                'branch_id' => $user->isAdmin() ? 'required|exists:branches,id' : 'nullable',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096'
            ]);

            // Non-admin users cannot change branch
            if (!$user->isAdmin()) {
                $validated['branch_id'] = $course->branch_id;
            }

            // Verify category belongs to same branch


            // Validate session totals match duration
            preg_match('/(\d+)/', $request->training_duration, $matches);
            $duration = $matches[1] ?? 0;
            $totalSessions = $request->practical_sessions + $request->theoretical_sessions;

            if ($duration > 0 && $totalSessions != $duration) {
                return response()->json([
                    'success' => false,
                    'errors' => [
                        'practical_sessions' => ["Total sessions ($totalSessions) must match training duration ($duration sessions)"]
                    ]
                ], 422);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $updateData = [
                'course' => $request->course,
                'category_id' => $request->category_id,
                'description' => $request->description,
                'training_duration' => $request->training_duration,
                'practical_sessions' => $request->practical_sessions,
                'theoretical_sessions' => $request->theoretical_sessions,
                'total_amount' => $request->total_amount,
                'status' => $request->status,
            ];

            // Only admin can change branch
            if ($user->isAdmin()) {
                $updateData['branch_id'] = $request->branch_id;
            }

            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($course->image) {
                    \Storage::disk('public')->delete($course->image);
                }
                $updateData['image'] = $request->file('image')->store('course-images', 'public');
            }

            $course->update($updateData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Course updated successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Course update error: ' . $e->getMessage());
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
        $course = Course::findOrFail($id);

        // Check if user has permission to delete this course
        if (!$user->isAdmin() && $course->branch_id != $user->branch_id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete this course.'
            ], 403);
        }

        // Check if course has students enrolled
        if ($course->studentEfLists()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete course. There are students enrolled in this course.'
            ], 422);
        }

        // Delete course image if exists
        if ($course->image) {
            \Storage::disk('public')->delete($course->image);
        }

        $course->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Search courses
     */
    public function search(Request $request)
    {
        try {
            $user = Auth::user();
            $query = $request->get('q', '');
            $status = $request->get('status', 'all');
            $sort = $request->get('sort', 'newest');

            // Start with base query
            $coursesQuery = Course::with(['category', 'branch']);

            // Apply branch filtering
            if ($user->isAdmin()) {
                $selectedBranchId = session('admin_selected_branch');
                if ($selectedBranchId) {
                    $coursesQuery->where('branch_id', $selectedBranchId);
                }
            } else {
                $coursesQuery->where('branch_id', $user->branch_id);
            }

            // Apply search filter
            if (!empty($query)) {
                $coursesQuery->where(function ($q) use ($query) {
                    $q->where('course', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('training_duration', 'like', "%{$query}%")
                    ->orWhereHas('category', function ($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%");
                    });
                });
            }

            // Apply status filter
            if ($status !== 'all') {
                $coursesQuery->where('status', $status === 'active' ? 1 : 0);
            }

            // Apply sorting
            switch ($sort) {
                case 'oldest':
                    $coursesQuery->orderBy('created_at', 'asc');
                    break;
                case 'fee_high':
                    $coursesQuery->orderBy('total_amount', 'desc');
                    break;
                case 'fee_low':
                    $coursesQuery->orderBy('total_amount', 'asc');
                    break;
                case 'name_asc':
                    $coursesQuery->orderBy('course', 'asc');
                    break;
                case 'name_desc':
                    $coursesQuery->orderBy('course', 'desc');
                    break;
                default: // newest
                    $coursesQuery->orderBy('created_at', 'desc');
                    break;
            }

            $courses = $coursesQuery->get()
                ->map(function($course) use ($user) {
                    // Additional security check
                    if ($user->isAdmin()) {
                        $selectedBranchId = session('admin_selected_branch');
                        if ($selectedBranchId && $course->branch_id != $selectedBranchId) {
                            return null;
                        }
                    } elseif (!$user->isAdmin() && $course->branch_id != $user->branch_id) {
                        return null;
                    }

                    $totalSessions = $course->practical_sessions + $course->theoretical_sessions;
                    $practicalPercent = $totalSessions > 0 ? round(($course->practical_sessions / $totalSessions) * 100) : 0;
                    $theoreticalPercent = $totalSessions > 0 ? round(($course->theoretical_sessions / $totalSessions) * 100) : 0;

                    return [
                        'id' => $course->id,
                        'course_name' => $course->course,
                        'category_name' => $course->category->name ?? 'N/A',
                        'description' => $course->description ? substr($course->description, 0, 50) . '...' : 'No description',
                        'duration' => $course->training_duration,
                        'practical_sessions' => $course->practical_sessions,
                        'theoretical_sessions' => $course->theoretical_sessions,
                        'total_sessions' => $totalSessions,
                        'practical_percent' => $practicalPercent,
                        'theoretical_percent' => $theoreticalPercent,
                        'fee' => number_format($course->total_amount, 2),
                        'status' => $course->status == 1 ? 'Active' : 'Inactive',
                        'branch_name' => $course->branch->name ?? 'N/A',
                        'image' => $course->image,
                        'can_edit' => $user->isAdmin() || $course->branch_id == $user->branch_id,
                        'can_delete' => $user->isAdmin() || $course->branch_id == $user->branch_id
                    ];
                })
                ->filter() // Remove null entries
                ->values(); // Reindex array

            return response()->json([
                'success' => true,
                'courses' => $courses,
                'count' => $courses->count()
            ]);

        } catch (\Exception $e) {
            \Log::error('Course search error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Search failed: ' . $e->getMessage(),
                'courses' => []
            ], 500);
        }
    }
}
