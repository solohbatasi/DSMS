<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseLesson;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LearningController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $canManage = $user->can('manage learning content');

        $courses = Course::query()
            ->with('category')
            ->withCount(['lessons as published_lessons_count' => fn ($query) => $query->where('is_published', true)])
            ->where('status', 1)
            ->when(!$user->isAdmin() && !$user->hasRole('developer'), fn ($query) => $query->where('branch_id', $user->branch_id))
            ->when(!$canManage, fn ($query) => $query->whereHas('lessons', fn ($lessons) => $lessons->where('is_published', true)))
            ->orderBy('course')
            ->get();

        $manageableCourses = $canManage
            ? Course::query()
                ->when(!$user->isAdmin() && !$user->hasRole('developer'), fn ($query) => $query->where('branch_id', $user->branch_id))
                ->orderBy('course')
                ->get()
            : collect();

        return view('learning.index', compact('courses', 'manageableCourses', 'canManage'));
    }

    public function show(Course $course): View
    {
        $this->ensureCourseBranchAccess($course);
        $canManage = auth()->user()->can('manage learning content');

        abort_unless($course->status === 1 || $canManage, 404);

        $lessons = $course->lessons()
            ->when(!$canManage, fn ($query) => $query->where('is_published', true))
            ->with('uploader')
            ->get();

        return view('learning.course', compact('course', 'lessons', 'canManage'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureCanManage();

        $validated = $request->validate([
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'section' => ['nullable', 'string', 'max:150'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'type' => ['required', 'in:video,pdf,document'],
            'file' => ['required', 'file', 'max:102400', 'mimes:mp4,webm,mov,pdf,doc,docx,ppt,pptx,xls,xlsx,txt'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $course = Course::findOrFail($validated['course_id']);
        $this->ensureCourseBranchAccess($course);

        $file = $request->file('file');
        $allowedExtensions = [
            'video' => ['mp4', 'webm', 'mov'],
            'pdf' => ['pdf'],
            'document' => ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'txt'],
        ];

        if (!in_array(strtolower($file->getClientOriginalExtension()), $allowedExtensions[$validated['type']], true)) {
            return back()->withErrors(['file' => 'The uploaded file extension does not match the selected material type.'])->withInput();
        }

        $path = $file->store("learning-materials/{$course->id}", 'local');

        CourseLesson::create([
            'course_id' => $course->id,
            'uploaded_by' => $request->user()->id,
            'section' => $validated['section'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
            'file_size' => $file->getSize(),
            'is_published' => $request->boolean('is_published', true),
        ]);

        return redirect()->route('learning.courses.show', $course)->with('success', 'Learning material uploaded.');
    }

    public function file(CourseLesson $lesson)
    {
        $this->ensureCourseBranchAccess($lesson->course);
        $canManage = auth()->user()->can('manage learning content');

        abort_unless($lesson->course->status === 1, 404);
        abort_unless($lesson->is_published || $canManage, 404);
        abort_unless(Storage::disk('local')->exists($lesson->file_path), 404);

        $path = Storage::disk('local')->path($lesson->file_path);
        $headers = [
            'Content-Type' => $lesson->mime_type,
            'X-Content-Type-Options' => 'nosniff',
        ];

        if (in_array($lesson->type, ['video', 'pdf'], true)) {
            return response()->file($path, $headers);
        }

        return response()->download($path, $lesson->original_filename, $headers);
    }

    public function destroy(CourseLesson $lesson): RedirectResponse
    {
        $this->ensureCanManage();
        $this->ensureCourseBranchAccess($lesson->course);

        $course = $lesson->course;
        Storage::disk('local')->delete($lesson->file_path);
        $lesson->delete();

        return redirect()->route('learning.courses.show', $course)->with('success', 'Learning material removed.');
    }

    private function ensureCanManage(): void
    {
        abort_unless(auth()->user()->can('manage learning content'), 403);
    }

    private function ensureCourseBranchAccess(Course $course): void
    {
        $user = auth()->user();

        abort_unless(
            $user->isAdmin() || $user->hasRole('developer') || $user->branch_id == $course->branch_id,
            403
        );
    }
}
