<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Branch;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function coursesIndex(Request $request)
    {
        // Get all active courses with relationships
        $query = Course::with(['category', 'branch'])
            ->where('status', 1);

        // Apply filters if provided
        if ($request->has('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->has('branch')) {
            $query->whereHas('branch', function($q) use ($request) {
                $q->where('slug', $request->branch);
            });
        }

        if ($request->has('level')) {
            $query->where('level', $request->level);
        }

        // Sort by price or popularity
        $sort = $request->get('sort', 'price_asc');
        switch ($sort) {
            case 'price_desc':
                $query->orderBy('total_amount', 'desc');
                break;
            case 'popular':
                $query->orderBy('enrollment_count', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('total_amount', 'asc');
        }

        $courses = $query->get();

        // Get all categories for filter
        $categories = CourseCategory::where('status', 1)
            ->withCount(['courses' => function($query) {
                $query->where('status', 1);
            }])
            ->orderBy('name')
            ->get();

        // Get all branches for filter
        $branches = Branch::withCount(['courses' => function($query) {
                $query->where('status', 1);
            }])
            ->orderBy('name')
            ->get();

        // Calculate statistics
        $stats = [
            'total_courses' => $courses->count(),
            'total_branches' => $branches->count(),
            'total_categories' => $categories->count(),
            'average_price' => $courses->avg('total_amount') ?? 0,
        ];

        // Group courses by category for display
        $coursesByCategory = $courses->groupBy(function($course) {
            return $course->category->name ?? 'Uncategorized';
        });

        // Get popular courses (highest enrollment or featured)
        $popularCourses = $courses->where('is_featured', true)
            ->take(6)
            ->values();

        return view('pages.courses', compact(
            'courses',
            'coursesByCategory',
            'popularCourses',
            'categories',
            'branches',
            'stats'
        ));
    }

    public function courseDetail($slug)
    {
        $course = Course::with(['category', 'branch'])
            ->where('slug', $slug)
            ->where('status', 1)
            ->firstOrFail();

        // Get related courses
        $relatedCourses = Course::with(['category', 'branch'])
            ->where('status', 1)
            ->where('category_id', $course->category_id)
            ->where('id', '!=', $course->id)
            ->take(3)
            ->get();

        return view('pages.course-detail', compact('course', 'relatedCourses'));
    }
}
