@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
    <a href="{{ route('learning.index') }}" class="text-sm font-medium text-blue-700 hover:text-blue-900"><i class="fas fa-arrow-left mr-1"></i> Learning library</a>

    <div class="mt-4 mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
        <div>
            @if($course->category)
                <p class="text-sm font-semibold uppercase tracking-wide text-blue-700">{{ $course->category->name }}</p>
            @endif
            <h1 class="mt-1 text-3xl font-bold text-gray-900">{{ $course->course }}</h1>
            @if($course->description)
                <p class="mt-2 max-w-3xl text-gray-600">{{ $course->description }}</p>
            @endif
        </div>
        <span class="shrink-0 rounded-full bg-blue-50 px-3 py-1 text-sm font-medium text-blue-800">{{ $lessons->count() }} {{ Str::plural('lesson', $lessons->count()) }}</span>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">{{ session('success') }}</div>
    @endif

    @if($canManage)
        <section class="mb-10 rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-lg font-semibold text-gray-900">Upload learning material</h2>
            <p class="mt-1 text-sm text-gray-600">Upload video, PDF, or document files up to 100 MB.</p>
            <form method="POST" action="{{ route('learning.lessons.store') }}" enctype="multipart/form-data" class="mt-5 grid gap-4 sm:grid-cols-2">
                @csrf
                <input type="hidden" name="course_id" value="{{ $course->id }}">
                <div>
                    <label for="lesson_title" class="mb-1 block text-sm font-medium text-gray-700">Title</label>
                    <input id="lesson_title" name="title" value="{{ old('title') }}" required maxlength="255" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="lesson_section" class="mb-1 block text-sm font-medium text-gray-700">Section <span class="font-normal text-gray-500">(optional)</span></label>
                    <input id="lesson_section" name="section" value="{{ old('section') }}" maxlength="150" placeholder="e.g. Road safety basics" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    @error('section')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="lesson_type" class="mb-1 block text-sm font-medium text-gray-700">Material type</label>
                    <select id="lesson_type" name="type" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="video" @selected(old('type') === 'video')>Video (MP4, WebM, MOV)</option>
                        <option value="pdf" @selected(old('type') === 'pdf')>PDF</option>
                        <option value="document" @selected(old('type') === 'document')>Document (Word, PowerPoint, Excel, text)</option>
                    </select>
                    @error('type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="lesson_file" class="mb-1 block text-sm font-medium text-gray-700">File</label>
                    <input id="lesson_file" name="file" type="file" required accept="video/mp4,video/webm,video/quicktime,.pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt" class="w-full rounded-lg border border-gray-300 p-2 text-sm">
                    @error('file')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="lesson_description" class="mb-1 block text-sm font-medium text-gray-700">Description <span class="font-normal text-gray-500">(optional)</span></label>
                    <textarea id="lesson_description" name="description" rows="3" maxlength="5000" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                    @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="flex items-center justify-between gap-3 sm:col-span-2">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', '1')) class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"> Publish for logged-in users</label>
                    <button type="submit" class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-800"><i class="fas fa-upload mr-2"></i>Upload material</button>
                </div>
            </form>
        </section>
    @endif

    @if($lessons->isEmpty())
        <div class="rounded-xl border border-dashed border-gray-300 bg-white p-10 text-center">
            <i class="fas fa-photo-video text-3xl text-gray-400"></i>
            <h2 class="mt-3 text-lg font-semibold text-gray-800">No learning materials here yet</h2>
            <p class="mt-1 text-gray-600">Check back later for videos, notes, and course resources.</p>
        </div>
    @else
        @foreach($lessons->groupBy(fn ($lesson) => $lesson->section ?: 'Course Materials') as $section => $sectionLessons)
            <section class="mb-8">
                <h2 class="mb-3 text-xl font-semibold text-gray-900">{{ $section }}</h2>
                <div class="space-y-4">
                    @foreach($sectionLessons as $lesson)
                        <article class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                            <div class="flex flex-col justify-between gap-3 p-5 sm:flex-row sm:items-start">
                                <div class="flex items-start gap-3">
                                    <span class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-700">
                                        <i class="fas {{ $lesson->type === 'video' ? 'fa-play' : ($lesson->type === 'pdf' ? 'fa-file-pdf' : 'fa-file-alt') }}"></i>
                                    </span>
                                    <div>
                                        <h3 class="font-semibold text-gray-900">{{ $lesson->title }}</h3>
                                        @if($lesson->description)<p class="mt-1 whitespace-pre-line text-sm text-gray-600">{{ $lesson->description }}</p>@endif
                                        <p class="mt-2 text-xs text-gray-500">{{ $lesson->original_filename }} · {{ number_format($lesson->file_size / 1048576, 1) }} MB</p>
                                    </div>
                                </div>
                                <div class="flex shrink-0 items-center gap-3">
                                    @if(!$lesson->is_published)<span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-800">Draft</span>@endif
                                    @if($lesson->type === 'document')
                                        <a href="{{ route('learning.lessons.file', $lesson) }}" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-medium text-white hover:bg-blue-800"><i class="fas fa-download mr-1"></i>Download</a>
                                    @else
                                        <a href="{{ route('learning.lessons.file', $lesson) }}" target="_blank" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-medium text-white hover:bg-blue-800">{{ $lesson->type === 'video' ? 'Open video' : 'Open PDF' }}</a>
                                    @endif
                                    @if($canManage)
                                        <form method="POST" action="{{ route('learning.lessons.destroy', $lesson) }}" onsubmit="return confirm('Remove this learning material?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" aria-label="Remove {{ $lesson->title }}" class="rounded-lg border border-red-200 px-3 py-2 text-red-700 hover:bg-red-50"><i class="fas fa-trash"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                            @if($lesson->type === 'video' && ($lesson->is_published || $canManage))
                                <div class="border-t border-gray-100 bg-gray-950 p-2 sm:p-4">
                                    <video controls preload="metadata" class="mx-auto max-h-[32rem] w-full rounded-lg">
                                        <source src="{{ route('learning.lessons.file', $lesson) }}" type="{{ $lesson->mime_type }}">
                                        Your browser does not support video playback.
                                    </video>
                                </div>
                            @elseif($lesson->type === 'pdf' && ($lesson->is_published || $canManage))
                                <iframe src="{{ route('learning.lessons.file', $lesson) }}" title="{{ $lesson->title }}" class="h-[32rem] w-full border-t border-gray-100"></iframe>
                            @endif
                        </article>
                    @endforeach
                </div>
            </section>
        @endforeach
    @endif
</div>
@endsection
