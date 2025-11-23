<?php

namespace App\Http\Controllers;

use App\Models\CourseRating;
use App\Models\ServiceFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function courseRatingForm()
    {
        $enrolled_courses = Auth::user()->enrolledCourses()->with('instructors')->get();
        return view('feedback.course-rating', compact('enrolled_courses'));
    }

    public function storeCourseRating(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'instructor_id' => 'required|exists:users,id',
            'instructor_effectiveness' => 'required|integer|between:1,5',
            'content_quality' => 'required|integer|between:1,5',
            'resources_quality' => 'required|integer|between:1,5',
            'what_you_liked' => 'required|string|max:1000',
            'improvement_suggestions' => 'nullable|string|max:1000',
            'instructor_feedback' => 'nullable|string|max:1000',
            'would_recommend' => 'required|boolean',
            'is_anonymous' => 'boolean'
        ]);

        $validated['student_id'] = Auth::id();

        CourseRating::create($validated);

        return redirect()->route('feedback.thank-you')
            ->with('success', 'Thank you for your valuable feedback!');
    }

    public function servicesFeedbackForm()
    {
        return view('feedback.school-services');
    }

    public function storeServicesFeedback(Request $request)
    {
        $validated = $request->validate([
            'registration_rating' => 'required|integer|between:1,5',
            'payment_rating' => 'required|integer|between:1,5',
            'support_rating' => 'required|integer|between:1,5',
            'classroom_rating' => 'required|integer|between:1,5',
            'library_rating' => 'required|integer|between:1,5',
            'computer_lab_rating' => 'required|integer|between:1,5',
            'counseling_rating' => 'required|integer|between:1,5',
            'tutoring_rating' => 'required|integer|between:1,5',
            'overall_rating' => 'required|integer|between:1,5',
            'positive_experiences' => 'required|string|max:1000',
            'improvement_areas' => 'required|string|max:1000',
            'additional_suggestions' => 'nullable|string|max:1000',
            'contact_permission' => 'boolean'
        ]);

        $validated['student_id'] = Auth::id();

        ServiceFeedback::create($validated);

        return redirect()->route('feedback.thank-you')
            ->with('success', 'Thank you for helping us improve our services!');
    }

    public function thankYou()
    {
        return view('feedback.thank-you');
    }
}
