<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class PageController extends Controller
{
      public function coursesIndex()
    {
        $courses = Course::where('status', 1)
            ->orderBy('total_amount', 'asc')
            ->get();

        // Course statistics for the page
        $stats = [
            'total_courses' => $courses->count(),
            'total_students' => 5000, // You can replace with actual count
            'success_rate' => 95,
            'certified_instructors' => 25
        ];

        return view('pages.courses', compact('courses', 'stats'));
    }
}
