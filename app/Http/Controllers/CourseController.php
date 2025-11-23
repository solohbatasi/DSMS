<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $totalCourses = Course::count();
        $activeCourses = Course::where('status', 1)->count();
        $inactiveCourses = Course::where('status', 0)->count();
        $popularCourses = Course::where('status', 1)
            ->orderBy('total_amount', 'desc')
            ->take(5)
            ->get();

        $courses = Course::paginate(10);

        return view('courses.index', [
            'courses' => $courses,
            'totalCourses' => $totalCourses,
            'activeCourses' => $activeCourses,
            'inactiveCourses' => $inactiveCourses,
            'popularCourses' => $popularCourses
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'course' => 'required|string|max:255|unique:courses,course',
                'description' => 'nullable|string',
                'training_duration' => 'required|string|max:255',
                'practical_sessions' => 'required|integer|min:0',
                'theoretical_sessions' => 'required|integer|min:0',
                'total_amount' => 'required|numeric|min:0',
                'status' => 'required|in:0,1',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

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
                'description' => $request->description,
                'training_duration' => $request->training_duration,
                'practical_sessions' => $request->practical_sessions, // ADD THIS
                'theoretical_sessions' => $request->theoretical_sessions, // ADD THIS
                'total_amount' => $request->total_amount,
                'status' => $request->status,
                'image' => $imagePath
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Course created successfully.'
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
        $course = Course::findOrFail($id);

        return response()->json([
            'course' => $course
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        try {
            $validated = $request->validate([
                'course' => 'required|string|max:255|unique:courses,course,' . $course->id,
                'description' => 'nullable|string',
                'training_duration' => 'required|string|max:255',
                'practical_sessions' => 'required|integer|min:0',
                'theoretical_sessions' => 'required|integer|min:0',
                'total_amount' => 'required|numeric|min:0',
                'status' => 'required|in:0,1',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

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
                'description' => $request->description,
                'training_duration' => $request->training_duration,
                'practical_sessions' => $request->practical_sessions, // ADD THIS
                'theoretical_sessions' => $request->theoretical_sessions, // ADD THIS
                'total_amount' => $request->total_amount,
                'status' => $request->status,
            ];

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
        $course = Course::findOrFail($id);

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
            $query = $request->get('q', '');
            $status = $request->get('status', 'all');
            $sort = $request->get('sort', 'newest');

            // Start with base query
            $coursesQuery = Course::query();

            // Apply search filter
            if (!empty($query)) {
                $coursesQuery->where(function ($q) use ($query) {
                    $q->where('course', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('training_duration', 'like', "%{$query}%");
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
                ->map(function($course) {
                    $totalSessions = $course->practical_sessions + $course->theoretical_sessions;
                    $practicalPercent = $totalSessions > 0 ? round(($course->practical_sessions / $totalSessions) * 100) : 0;
                    $theoreticalPercent = $totalSessions > 0 ? round(($course->theoretical_sessions / $totalSessions) * 100) : 0;

                    return [
                        'id' => $course->id,
                        'course_name' => $course->course,
                        'description' => $course->description ? substr($course->description, 0, 50) . '...' : 'No description',
                        'duration' => $course->training_duration,
                        'practical_sessions' => $course->practical_sessions,
                        'theoretical_sessions' => $course->theoretical_sessions,
                        'total_sessions' => $totalSessions,
                        'practical_percent' => $practicalPercent,
                        'theoretical_percent' => $theoreticalPercent,
                        'fee' => number_format($course->total_amount, 2),
                        'status' => $course->status == 1 ? 'Active' : 'Inactive',
                        'students_count' => $course->studentEfLists()->count(),
                        'image' => $course->image
                    ];
                });

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
