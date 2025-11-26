<?php

namespace App\Http\Controllers;

use App\Models\LessonApplication;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LessonApplicationController extends Controller
{
    // Show booking form
    public function create(Request $request)
    {
        $courses = Course::where('status', 1)->get();
        $selectedCourse = $request->query('course');

        return view('book-lessons', compact('courses', 'selectedCourse'));
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

            // Course information
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
                'course_id' => $validated['course_id'],
                'preferred_start_date' => $validated['preferred_start_date'],
                'preferred_schedule' => $validated['preferred_schedule'],
                'special_requirements' => $validated['special_requirements'],
                'status' => 'pending',
            ]);

            DB::commit();

            // Send confirmation email (you can implement this later)
            // Mail::to($application->email)->send(new ApplicationReceived($application));

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

    // Admin methods
    public function index()
    {
        $applications = LessonApplication::with(['course', 'student'])
            ->latest()
            ->paginate(20);

        $stats = [
            'pending' => LessonApplication::pending()->count(),
            'under_review' => LessonApplication::underReview()->count(),
            'approved' => LessonApplication::approved()->count(),
        ];

        return view('admin.applications.index', compact('applications', 'stats'));
    }

    public function show(LessonApplication $application)
    {
        $application->load(['course', 'student', 'reviewer']);

        return view('admin.applications.show', compact('application'));
    }

    public function updateStatus(Request $request, LessonApplication $application)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,under_review,approved,rejected,enrolled',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $application->update($validated);

        return back()->with('success', 'Application status updated successfully.');
    }
}
