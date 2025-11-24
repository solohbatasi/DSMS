@extends('layouts.app')
@props(['title' => 'Download Session Tickets', 'subtitle' => 'Download tickets for your practical and theoretical sessions'])

@section('content')
@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Carbon;
@endphp

<div class="min-h-screen bg-gray-50">
    @include('components.dashboard-header')

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Session Tickets</h1>
                    <p class="text-gray-600 mt-1">Download tickets for your practical and theoretical sessions</p>
                </div>
                <a href="{{ route('tickets.history') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Download History
                </a>
            </div>
        </div>

        <!-- Today's Downloads -->
       @if(isset($today_downloads) && $today_downloads->isNotEmpty())
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <span class="text-yellow-800 font-medium">Today's Downloads</span>
            </div>
            <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-2">
@if(isset($today_downloads) && $today_downloads->isNotEmpty())
                <div class="bg-white rounded-lg p-3 border border-yellow-100">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-sm font-medium text-gray-900">{{ $download->efList->ef_no ?? 'N/A' }}</span>
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                {{ $download->session_type === 'practical' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ ucfirst($download->session_type) }}
                            </span>
                        </div>
                        <span class="text-xs text-gray-500">{{ $download->download_time->format('h:i A') }}</span>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Course Selection -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Select Course & Session Type</h3>
            </div>

          @if(isset($ef_lists) && $ef_lists->isNotEmpty())
    <form action="{{ route('tickets.download') }}" method="POST" id="ticketForm">
        @csrf
        <!-- Course Selection -->
        <div class="mb-6">
            <label for="ef_id" class="block text-sm font-medium text-gray-700 mb-2">
                Select Course
            </label>
            <select name="ef_id" id="ef_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required>
                <option value="">Choose a course...</option>
                @foreach($ef_lists as $ef)
                    @php
                        $course = $ef->course;
                        $practical_sessions = $course ? $course->practical_sessions : 0;
                        $theoretical_sessions = $course ? $course->theoretical_sessions : 0;
                        $course_name = $course ? $course->course : 'Course Not Found';
                    @endphp
                    <option value="{{ $ef->id }}"
                            data-practical="{{ $practical_sessions }}"
                            data-theoretical="{{ $theoretical_sessions }}">
                        {{ $ef->ef_no }} - {{ $course_name }}
                    </option>
                @endforeach
            </select>
        </div>

                        <!-- Session Type Selection -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Session Type
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Practical Session -->
                                <label class="relative flex cursor-pointer">
                                    <input type="radio" name="session_type" value="practical" class="sr-only" required>
                                    <div class="flex-1 p-4 border-2 border-gray-200 rounded-lg hover:border-blue-300 transition-colors duration-200 session-option">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">Practical Session</div>
                                                <div class="text-sm text-gray-500 mt-1">
                                                    <span id="practical-sessions">-</span> sessions available
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </label>

                                <!-- Theoretical Session -->
                                <label class="relative flex cursor-pointer">
                                    <input type="radio" name="session_type" value="theoretical" class="sr-only" required>
                                    <div class="flex-1 p-4 border-2 border-gray-200 rounded-lg hover:border-green-300 transition-colors duration-200 session-option">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                                <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">Theoretical Session</div>
                                                <div class="text-sm text-gray-500 mt-1">
                                                    <span id="theoretical-sessions">-</span> sessions available
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Download Button -->
                        <div class="flex justify-end">
                            <button type="submit"
                                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                    id="downloadBtn">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Download Ticket
                            </button>
                        </div>
                    </form>
               @else
    <div class="text-center py-8">
        <div class="h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No Active Courses</h3>
        <p class="text-gray-600 mb-4">You don't have any active courses to download tickets for.</p>

        <!-- Debug information for admin -->
        @if(Auth::user()->role === 'admin')
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-4 text-left">
                <h4 class="font-medium text-yellow-800">Debug Info:</h4>
                <p class="text-sm text-yellow-700">Student ID: {{ $student->id ?? 'N/A' }}</p>
                <p class="text-sm text-yellow-700">User ID: {{ Auth::id() }}</p>
            </div>
        @endif

        <a href="{{ route('dashboard') }}"
           class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
            Return to Dashboard
        </a>
    </div>
@endif
            </div>
        </div>

        <!-- Information -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-sm text-blue-700">
                    <strong>Important:</strong> You can download only one ticket per session type per day.
                    Tickets are valid for the day of download only. Make sure to bring your printed ticket
                    or have it available on your mobile device for verification.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const efSelect = document.getElementById('ef_id');
    const practicalSessions = document.getElementById('practical-sessions');
    const theoreticalSessions = document.getElementById('theoretical-sessions');
    const sessionOptions = document.querySelectorAll('.session-option');
    const downloadBtn = document.getElementById('downloadBtn');

    function updateSessionInfo() {
        const selectedOption = efSelect.selectedOptions[0];
        if (selectedOption && selectedOption.value) {
            const practical = selectedOption.dataset.practical;
            const theoretical = selectedOption.dataset.theoretical;

            practicalSessions.textContent = practical;
            theoreticalSessions.textContent = theoretical;

            // Enable download button when course is selected
            downloadBtn.disabled = false;
        } else {
            practicalSessions.textContent = '-';
            theoreticalSessions.textContent = '-';
            downloadBtn.disabled = true;
        }
    }

    efSelect.addEventListener('change', updateSessionInfo);

    // Style session type selection
    sessionOptions.forEach(option => {
        const radio = option.querySelector('input[type="radio"]');
        const label = option.closest('label');

        label.addEventListener('click', function() {
            // Remove selected styles from all options
            sessionOptions.forEach(opt => {
                opt.classList.remove('border-blue-500', 'border-green-500', 'bg-blue-50', 'bg-green-50');
            });

            // Add selected style to clicked option
            if (radio.value === 'practical') {
                option.classList.add('border-blue-500', 'bg-blue-50');
            } else {
                option.classList.add('border-green-500', 'bg-green-50');
            }
        });
    });

    // Initialize
    updateSessionInfo();
});
</script>

<style>
.session-option {
    transition: all 0.2s ease-in-out;
}

.session-option:hover {
    transform: translateY(-1px);
}
</style>
@endsection
