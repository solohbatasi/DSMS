@extends('layouts.app')
@props(['title' => 'Download Session Ticket', 'subtitle' => 'Download your practical or theoretical session ticket'])

@section('content')
@php
    use Illuminate\Support\Carbon;
@endphp

<div class="min-h-screen bg-gray-50">
    @include('components.dashboard-header')

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Session Ticket Download</h1>
                    <p class="text-gray-600 mt-2">Download your daily session tickets for practical or theoretical classes</p>
                </div>
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm font-medium text-blue-800">Limit: 1 ticket per session type per day</span>
                    </div>
                </div>
            </div>
        </div>

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-red-800 font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-green-800 font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Ticket Download Form -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Download New Ticket</h3>

                @if($active_ef_lists->count() > 0)
                    <form action="{{ route('student.tickets.download') }}" method="POST" id="ticketForm">
                        @csrf

                        <!-- Course Selection -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select Course</label>
                            <select name="ef_id" id="ef_id" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Choose a course...</option>
                                @foreach($active_ef_lists as $ef_list)
                                    <option value="{{ $ef_list->id }}" data-course-id="{{ $ef_list->course_id }}">
                                        {{ $ef_list->ef_no }} - {{ $ef_list->course->course ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="course_id" id="course_id">
                        </div>

                        <!-- Session Type Selection -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Session Type</label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="relative flex cursor-pointer">
                                    <input type="radio" name="session_type" value="practical" class="sr-only" required>
                                    <div class="flex items-center justify-center w-full p-4 border-2 border-gray-200 rounded-lg hover:border-blue-300 transition-colors duration-200 session-type-card">
                                        <div class="text-center">
                                            <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                            </div>
                                            <span class="block text-sm font-medium text-gray-900">Practical</span>
                                            <span class="block text-xs text-gray-500 mt-1">Hands-on training</span>
                                        </div>
                                    </div>
                                </label>
                                <label class="relative flex cursor-pointer">
                                    <input type="radio" name="session_type" value="theoretical" class="sr-only" required>
                                    <div class="flex items-center justify-center w-full p-4 border-2 border-gray-200 rounded-lg hover:border-blue-300 transition-colors duration-200 session-type-card">
                                        <div class="text-center">
                                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                </svg>
                                            </div>
                                            <span class="block text-sm font-medium text-gray-900">Theoretical</span>
                                            <span class="block text-xs text-gray-500 mt-1">Classroom learning</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Session Info (Dynamic) -->
                        <div id="sessionInfo" class="bg-gray-50 rounded-lg p-4 mb-6 hidden">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Session Information</h4>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Available Practical:</span>
                                    <span id="practicalSessions" class="font-medium ml-1">-</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Available Theoretical:</span>
                                    <span id="theoreticalSessions" class="font-medium ml-1">-</span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Download New Ticket
                        </button>
                    </form>
                @else
                    <div class="text-center py-8">
                        <div class="h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Active Courses</h3>
                        <p class="text-gray-600 mb-4">You need to have active courses to download session tickets.</p>
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            Back to Dashboard
                        </a>
                    </div>
                @endif
            </div>

            <!-- Today's Downloads & Redownload Section -->
            <div class="space-y-8">
                <!-- Today's Downloads -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Today's Downloads</h3>

                    @if($today_downloads->count() > 0)
                        <div class="space-y-4">
                            @foreach($today_downloads as $download)
                            @php
                                $ef_list = $download->efList;
                                $course = $ef_list->course ?? null;
                            @endphp
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors duration-200">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center
                                            {{ $download->session_type === 'practical' ? 'bg-orange-100' : 'bg-blue-100' }}">
                                            <span class="text-sm font-medium {{ $download->session_type === 'practical' ? 'text-orange-600' : 'text-blue-600' }}">
                                                {{ strtoupper(substr($download->session_type, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $course->course ?? 'N/A' }}
                                            </p>
                                            <p class="text-xs text-gray-500 capitalize">
                                                {{ $download->session_type }} Session
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600">
                                            {{ Carbon::parse($download->download_time)->format('h:i A') }}
                                        </p>
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                            {{ $download->session_type === 'practical' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ ucfirst($download->session_type) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Redownload Button -->
                                <div class="flex space-x-2">
                                    <form action="{{ route('student.tickets.redownload', $download->id) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full bg-green-600 text-white py-2 px-3 rounded text-sm font-medium hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200 flex items-center justify-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                            </svg>
                                            Redownload
                                        </button>
                                    </form>
                                    <a href="{{ route('student.tickets.preview', $download->id) }}" target="_blank" class="flex-1 bg-gray-600 text-white py-2 px-3 rounded text-sm font-medium hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200 flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Preview
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <p class="text-gray-600">No tickets downloaded today</p>
                        </div>
                    @endif
                </div>

                <!-- Instructions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">How it works</h3>
                    <div class="space-y-3 text-sm text-gray-600">
                        <div class="flex items-start">
                            <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                <span class="text-xs font-medium text-blue-600">1</span>
                            </div>
                            <p>Download 1 ticket per session type per day</p>
                        </div>
                        <div class="flex items-start">
                            <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                <span class="text-xs font-medium text-blue-600">2</span>
                            </div>
                            <p>Use <strong>Redownload</strong> if you lost your ticket</p>
                        </div>
                        <div class="flex items-start">
                            <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                <span class="text-xs font-medium text-blue-600">3</span>
                            </div>
                            <p>Present ticket with QR code at session venue</p>
                        </div>
                        <div class="flex items-start">
                            <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                <span class="text-xs font-medium text-blue-600">4</span>
                            </div>
                            <p>Tickets are thermal printer compatible</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const efSelect = document.getElementById('ef_id');
    const courseIdInput = document.getElementById('course_id');
    const sessionInfo = document.getElementById('sessionInfo');
    const practicalSessions = document.getElementById('practicalSessions');
    const theoreticalSessions = document.getElementById('theoreticalSessions');
    const sessionTypeCards = document.querySelectorAll('.session-type-card');

    // Course data storage
    let coursesData = {!! $active_ef_lists->mapWithKeys(function($item) {
        return [$item->id => [
            'course_id' => $item->course_id,
            'practical_sessions' => $item->course->practical_sessions ?? 0,
            'theoretical_sessions' => $item->course->theoretical_sessions ?? 0
        ]];
    })->toJson() !!};

    // Update course ID and session info when EF list is selected
    efSelect.addEventListener('change', function() {
        const selectedEfId = this.value;

        if (selectedEfId && coursesData[selectedEfId]) {
            const course = coursesData[selectedEfId];
            courseIdInput.value = course.course_id;

            // Show session info
            practicalSessions.textContent = course.practical_sessions;
            theoreticalSessions.textContent = course.theoretical_sessions;
            sessionInfo.classList.remove('hidden');
        } else {
            courseIdInput.value = '';
            sessionInfo.classList.add('hidden');
        }
    });

    // Add visual feedback for session type selection
    sessionTypeCards.forEach(card => {
        const radio = card.querySelector('input[type="radio"]');

        card.addEventListener('click', function() {
            sessionTypeCards.forEach(c => c.classList.remove('border-blue-500', 'bg-blue-50'));
            this.classList.add('border-blue-500', 'bg-blue-50');
        });

        // Update initial state
        if (radio.checked) {
            card.classList.add('border-blue-500', 'bg-blue-50');
        }
    });

    // Form submission validation
    document.getElementById('ticketForm').addEventListener('submit', function(e) {
        const selectedEf = efSelect.value;
        const selectedSessionType = document.querySelector('input[name="session_type"]:checked');

        if (!selectedEf) {
            e.preventDefault();
            alert('Please select a course.');
            return;
        }

        if (!selectedSessionType) {
            e.preventDefault();
            alert('Please select a session type.');
            return;
        }
    });
});
</script>

<style>
.session-type-card {
    transition: all 0.2s ease-in-out;
}

.session-type-card:hover {
    transform: translateY(-2px);
}

input[type="radio"]:checked + .session-type-card {
    border-color: #3b82f6;
    background-color: #eff6ff;
}
</style>
@endsection
