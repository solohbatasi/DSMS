@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
    <div class="mb-8">
        <p class="text-sm font-semibold uppercase tracking-wide text-blue-700">Learning</p>
        <h1 class="mt-1 text-3xl font-bold text-gray-900">Course library</h1>
        <p class="mt-2 text-gray-600">Videos, notes, and other learning materials for your courses.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">{{ session('success') }}</div>
    @endif

    @if($canManage)
        <section class="mb-10 rounded-xl border border-blue-100 bg-blue-50 p-5 sm:p-6">
            <h2 class="text-lg font-semibold text-gray-900">Manage course materials</h2>
            <p class="mt-1 text-sm text-gray-600">Choose a course to upload and organize its learning materials.</p>
            @if($manageableCourses->isNotEmpty())
                <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($manageableCourses as $course)
                        <a href="{{ route('learning.courses.show', $course) }}" class="flex items-center justify-between rounded-lg border border-blue-100 bg-white p-4 text-gray-800 hover:border-blue-400 hover:shadow-sm">
                            <span class="font-medium">{{ $course->course }}</span>
                            <i class="fas fa-arrow-right text-blue-600"></i>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="mt-4 rounded-lg bg-white p-4 text-sm text-gray-600">There are no courses available for your branch yet.</p>
            @endif
        </section>
    @endif

    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($courses as $course)
            <a href="{{ route('learning.courses.show', $course) }}" class="group overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-36 items-center justify-center bg-gradient-to-br from-blue-700 to-indigo-800 text-white">
                    <i class="fas fa-book-open text-5xl opacity-90"></i>
                </div>
                <div class="p-5">
                    @if($course->category)
                        <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">{{ $course->category->name }}</p>
                    @endif
                    <h2 class="mt-1 text-xl font-semibold text-gray-900 group-hover:text-blue-700">{{ $course->course }}</h2>
                    <p class="mt-2 line-clamp-2 text-sm text-gray-600">{{ $course->description ?: 'Open this course to view its learning materials.' }}</p>
                    <div class="mt-4 flex items-center justify-between text-sm text-gray-500">
                        <span>{{ $course->published_lessons_count }} {{ \Illuminate\Support\Str::plural('lesson', $course->published_lessons_count) }}</span>
                        <span class="font-medium text-blue-700">Open course <i class="fas fa-arrow-right ml-1"></i></span>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full rounded-xl border border-dashed border-gray-300 bg-white p-10 text-center">
                <i class="fas fa-book-open text-3xl text-gray-400"></i>
                <h2 class="mt-3 text-lg font-semibold text-gray-800">No published learning materials yet</h2>
                <p class="mt-1 text-gray-600">Course content will appear here once it has been published.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
