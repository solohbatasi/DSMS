@extends('layouts.app')
@props(['title' => 'Manage Student Tickets', 'subtitle' => 'Download tickets for student sessions'])

@section('content')
@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Carbon;
@endphp

<div class="min-h-screen bg-gray-50">
    @include('components.dashboard-header')
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">


        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Create New Ticket -->
            <div class="lg:col-span-2">
                <!-- Course Selection -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">Create New Ticket</h3>
                    </div>

                    @if($ef_lists->isNotEmpty())
                    <form action="{{ route('admin.students.tickets.download', ['student' => $student->id]) }}" method="POST" id="ticketForm" class="p-6">
                        @csrf

                        <!-- Date Selection -->
                        <div class="mb-6">
                            <label for="session_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Session Date
                            </label>
                            <div class="relative">
                                <input type="date"
                                       name="session_date"
                                       id="session_date"
                                       value="{{ $today }}"
                                       max="{{ $today }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       required>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="mt-1 text-sm text-gray-500" id="dateHelpText">
                                Select the date when the session occurred. Cannot be in the future.
                            </p>
                        </div>

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
                                        $course_name = $course ? $course->course : 'Course Not Found';

                                        // Calculate remaining sessions
                                        $remaining_practical = $ef->remaining_practical;
                                        $remaining_theoretical = $ef->remaining_theoretical;
                                        $enrollment_date = $ef->created_at->format('Y-m-d');
                                    @endphp
                                    <option value="{{ $ef->id }}"
                                            data-practical="{{ $remaining_practical }}"
                                            data-theoretical="{{ $remaining_theoretical }}"
                                            data-enrollment="{{ $enrollment_date }}">
                                        {{ $ef->ef_no }} - {{ $course_name }}
                                        (Prac: {{ $remaining_practical }}, Theo: {{ $remaining_theoretical }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-sm text-gray-500" id="courseHelpText"></p>
                        </div>

                        <!-- Hidden course_id field -->
                        <input type="hidden" name="course_id" id="course_id" value="">

                        <!-- Session Type Selection -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Session Type
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Practical Session -->
                                <label class="relative flex cursor-pointer">
                                    <input type="radio" name="session_type" value="practical" class="sr-only" required>

                                    <div class="flex-1 p-4 border-2 border-gray-200 rounded-lg hover:border-blue-300 transition-colors duration-200 session-option relative">

                                        <!-- ✅ Check Icon (Top Left) -->
                                        <div class="check-icon absolute top-2 left-2 hidden">
                                            <div class="w-5 h-5 bg-blue-600 rounded flex items-center justify-center">
                                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </div>
                                        </div>

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
                                                    <span id="practical-attended">-</span> attended |
                                                    <span id="practical-remaining">-</span> remaining
                                                </div>
                                                <!-- Optional: Show progress bar -->
                                                <div class="mt-2 w-full bg-gray-200 rounded-full h-1.5">
                                                    <div id="practical-progress" class="bg-blue-600 h-1.5 rounded-full" style="width: 0%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </label>

                                <!-- Theoretical Session -->
                                <label class="relative flex cursor-pointer">
                                    <input type="radio" name="session_type" value="theoretical" class="sr-only" required>

                                    <div class="flex-1 p-4 border-2 border-gray-200 rounded-lg hover:border-green-300 transition-colors duration-200 session-option relative">

                                        <!-- ✅ Check Icon (Top Left) -->
                                        <div class="check-icon absolute top-2 left-2 hidden">
                                            <div class="w-5 h-5 bg-green-600 rounded flex items-center justify-center">
                                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </div>
                                        </div>

                                        <div class="flex items-center">
                                            <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                                <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">Theoretical Session</div>
                                                <div class="text-sm text-gray-500 mt-1">
                                                    <span id="theoretical-attended">-</span> attended |
                                                    <span id="theoretical-remaining">-</span> remaining
                                                </div>
                                                <!-- Optional: Show progress bar -->
                                                <div class="mt-2 w-full bg-gray-200 rounded-full h-1.5">
                                                    <div id="theoretical-progress" class="bg-green-600 h-1.5 rounded-full" style="width: 0%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Previous Classes Modal -->
                        <div id="previousClassesModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                <!-- Background overlay -->
                                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closePreviousClassesModal()"></div>

                                <!-- Modal panel -->
                                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                    <form action="{{ route('admin.students.tickets.add-previous', ['student' => $student->id]) }}" method="POST" id="previousClassesForm">
                                        @csrf
                                        <input type="hidden" name="ef_id" id="modal_ef_id">
                                        <input type="hidden" name="session_type" id="modal_session_type">
                                        <input type="hidden" name="course_id" id="modal_course_id">

                                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                            <div class="sm:flex sm:items-start">
                                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                </div>
                                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                                        Record Previous Classes
                                                    </h3>
                                                    <div class="mt-2">
                                                        <p class="text-sm text-gray-500" id="modalDescription">
                                                            Add attendance for classes that were conducted but not recorded.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Course and Session Info -->
                                            <div class="mt-4 bg-gray-50 p-4 rounded-lg">
                                                <div class="grid grid-cols-2 gap-4 text-sm">
                                                    <div>
                                                        <span class="text-gray-500">Course:</span>
                                                        <span class="font-medium text-gray-900 block" id="modal_course_name"></span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-500">Session Type:</span>
                                                        <span class="font-medium text-gray-900 block" id="modal_session_type_display"></span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-500">Current Attended:</span>
                                                        <span class="font-medium text-gray-900 block" id="modal_current_attended">0</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-500">Remaining:</span>
                                                        <span class="font-medium text-gray-900 block" id="modal_current_remaining">0</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Number of Classes Input -->
                                            <div class="mt-4">
                                                <label for="previous_classes_count" class="block text-sm font-medium text-gray-700">
                                                    Number of Previous Classes to Add
                                                </label>
                                                <div class="mt-1">
                                                    <input type="number"
                                                        name="previous_classes_count"
                                                        id="previous_classes_count"
                                                        min="1"
                                                        max="100"
                                                        required
                                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                                </div>
                                                <p class="mt-1 text-xs text-gray-500" id="maxClassesHint">
                                                    Maximum: <span id="maxClassesValue">0</span> classes (based on remaining sessions)
                                                </p>
                                            </div>

                                            <!-- Reason/Notes -->
                                            <div class="mt-4">
                                                <label for="notes" class="block text-sm font-medium text-gray-700">
                                                    Reason/Notes (Optional)
                                                </label>
                                                <textarea name="notes"
                                                        id="notes"
                                                        rows="2"
                                                        placeholder="e.g., Missed recordings from previous sessions"
                                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                                            </div>
                                        </div>

                                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                            <button type="submit"
                                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                                Add Classes
                                            </button>
                                            <button type="button"
                                                    onclick="closePreviousClassesModal()"
                                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons Row -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <!-- Previous Classes Button (Left Side) -->
                            <div id="previousClassesSection" style="display: none;">
                                <button type="button"
                                        onclick="openPreviousClassesModal()"
                                        class="inline-flex items-center px-4 py-3 bg-green-700 text-white border border-gray-300 shadow-sm text-base font-medium rounded-lg  hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-5 h-5 mr-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Record Previous Classes
                                </button>
                            </div>

                            <!-- Download Button (Right Side) -->
                            <div class="flex justify-end flex-1">
                                <button type="submit"
                                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                        id="downloadBtn">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    Create Ticket
                                </button>
                            </div>
                        </div>

                        <!-- Help text for previous classes (appears below buttons when button is visible) -->
                        <div id="previousClassesHelp" class="mt-2 text-xs text-gray-500" style="display: none;">
                            <span>Use this to add attendance for classes that were conducted but not recorded in the system.</span>
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
                        <p class="text-gray-600 mb-4">This student doesn't have any active courses.</p>
                    </div>
                    @endif
                </div>

                <!-- Information -->
                <div class="mt-6 bg-purple-50 border border-purple-200 rounded-xl p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-purple-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="text-sm text-purple-700">
                            <strong>Admin Mode:</strong> You can create tickets for past sessions.
                            The date must be today or in the past, but not before the student's enrollment date.
                            Only one ticket per session type per day is allowed. Session counts will be deducted from the student's allocation.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Recent Tickets -->
            <div class="lg:col-span-1">
                <!-- Recent Tickets Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden sticky top-8">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Recent Tickets</h3>
                            <span class="text-sm text-gray-500">Last 30 Days</span>

                        </div>
                               <p class="text-gray-600 mt-1">
                            Student: <span class="font-semibold">{{ $student->name }}</span>
                            | ID: {{ $student->id_no }}
                        </p>
                        <p class="text-gray-500 text-sm mt-1">
                            Reg Number: {{ $student->efLists->first()->ef_no ?? 'N/A' }}
                        </p>
                    </div>



                    @if($recent_tickets->isNotEmpty())
                    <div class="max-h-[600px] overflow-y-auto">
                        <div class="divide-y divide-gray-200">
                            @foreach($recent_tickets as $ticket)
                            <div class="px-6 py-4 hover:bg-gray-50">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <span class="font-medium text-gray-900 text-sm">{{ $ticket->efList->ef_no ?? 'N/A' }}</span>
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                {{ $ticket->session_type === 'practical' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                                {{ ucfirst($ticket->session_type) }}
                                            </span>
                                        </div>
                                        <span class="text-xs text-gray-500">
                                            {{ $ticket->download_time->format('M d') }}
                                        </span>
                                    </div>

                                    <div class="space-y-1">
                                        <p class="text-sm text-gray-600">
                                            {{ Str::limit($ticket->efList->course->course ?? 'N/A', 40) }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            Session: {{ Carbon::parse($ticket->session_date)->format('M d, Y') ?? 'N/A' }}
                                        </p>
                                        @if($ticket->downloaded_by_role && $ticket->downloaded_by_role !== 'student')
                                        <div class="flex items-center text-xs">
                                            <span class="text-gray-500">By: </span>
                                            <span class="ml-1 bg-purple-100 text-purple-800 px-2 py-0.5 rounded">
                                                {{ $ticket->downloaded_by_role }}
                                            </span>
                                        </div>
                                        @endif
                                    </div>

                                    <div class="flex items-center justify-between pt-2">
                                        <span class="text-xs text-gray-400">
                                            {{ $ticket->download_time->format('h:i A') }}
                                        </span>
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('admin.tickets.preview', ['ticket' => $ticket->id]) }}"
                                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                Preview
                                            </a>
                                            <a href="{{ route('admin.tickets.redownload', ['ticket' => $ticket->id]) }}"
                                               class="text-green-600 hover:text-green-800 text-sm font-medium">
                                                Redownload
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @else
                    <div class="p-8 text-center">
                        <svg class="h-12 w-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-gray-500">No tickets downloaded yet for this student.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sessionDateInput = document.getElementById('session_date');
    const efSelect = document.getElementById('ef_id');
    const courseIdInput = document.getElementById('course_id');
    const practicalAttended = document.getElementById('practical-attended');
    const practicalRemaining = document.getElementById('practical-remaining');
    const theoreticalAttended = document.getElementById('theoretical-attended');
    const theoreticalRemaining = document.getElementById('theoretical-remaining');
    const practicalProgress = document.getElementById('practical-progress');
    const theoreticalProgress = document.getElementById('theoretical-progress');
    const sessionOptions = document.querySelectorAll('.session-option');
    const downloadBtn = document.getElementById('downloadBtn');
    const courseHelpText = document.getElementById('courseHelpText');
    const dateHelpText = document.getElementById('dateHelpText');

    // Store session data for each EF
    const efData = {
        @foreach($ef_lists as $ef)
            '{{ $ef->id }}': {
                practical_attended: '{{ $ef->attended_practical }}',
                practical_remaining: '{{ $ef->remaining_practical }}',
                theoretical_attended: '{{ $ef->attended_theoretical }}',
                theoretical_remaining: '{{ $ef->remaining_theoretical }}',
                total_practical: '{{ $ef->total_class ?? ($ef->course->practical_sessions ?? 0) }}',
                total_theoretical: '{{ $ef->course->theoretical_sessions ?? 0 }}',
                course_id: '{{ $ef->course_id }}',
                enrollment_date: '{{ $ef->created_at->format("Y-m-d") }}',
                ef_no: '{{ $ef->ef_no }}'
            },
        @endforeach
    };

    function selectSession(type) {
        sessionOptions.forEach(option => {
            const radio = option.parentElement.querySelector('input[type="radio"]');
            const checkIcon = option.querySelector('.check-icon');

            // Reset all
            option.classList.remove('border-blue-500', 'border-green-500', 'bg-blue-50', 'bg-green-50');
            if (checkIcon) checkIcon.classList.add('hidden');

            if (radio.value === type) {
                radio.checked = true;

                if (type === 'practical') {
                    option.classList.add('border-blue-500', 'bg-blue-50');
                } else {
                    option.classList.add('border-green-500', 'bg-green-50');
                }

                if (checkIcon) checkIcon.classList.remove('hidden');
            }
        });
    }

    function calculateProgress(attended, total) {
        if (total == 0) return 0;
        return Math.min((attended / total) * 100, 100);
    }

    // Function to open modal with current selection
    window.openPreviousClassesModal = function() {
        const efSelect = document.getElementById('ef_id');
        const selectedEfId = efSelect.value;
        const sessionType = document.querySelector('input[name="session_type"]:checked')?.value;

        if (!selectedEfId || !sessionType) {
            alert('Please select both a course and session type first.');
            return;
        }

        const efData = window.efData || {};
        const data = efData[selectedEfId];

        if (!data) return;

        // Set modal fields
        document.getElementById('modal_ef_id').value = selectedEfId;
        document.getElementById('modal_session_type').value = sessionType;
        document.getElementById('modal_course_id').value = data.course_id;

        // Get course name from select option
        const selectedOption = efSelect.options[efSelect.selectedIndex];
        const courseName = selectedOption.text.split(' - ')[1]?.split(' (')[0] || 'Unknown Course';
        document.getElementById('modal_course_name').textContent = courseName;

        // Set session type display
        const sessionDisplay = sessionType === 'practical' ? 'Practical' : 'Theoretical';
        document.getElementById('modal_session_type_display').textContent = sessionDisplay;

        // Set current counts
        const attended = sessionType === 'practical' ? data.practical_attended : data.theoretical_attended;
        const remaining = sessionType === 'practical' ? data.practical_remaining : data.theoretical_remaining;

        document.getElementById('modal_current_attended').textContent = attended;
        document.getElementById('modal_current_remaining').textContent = remaining;

        // Set max classes hint
        const maxClasses = parseInt(remaining);
        document.getElementById('maxClassesValue').textContent = maxClasses;
        const countInput = document.getElementById('previous_classes_count');
        countInput.max = maxClasses;
        countInput.value = ''; // Clear previous value

        // Show modal
        document.getElementById('previousClassesModal').classList.remove('hidden');
    }

    // Function to close modal
    window.closePreviousClassesModal = function() {
        document.getElementById('previousClassesModal').classList.add('hidden');
        document.getElementById('previousClassesForm').reset();
    }

    function updateSessionInfo() {
    const selectedValue = efSelect.value;
    const sessionDate = sessionDateInput.value;

    // Check if a session type is selected OR if we should auto-select practical
    let selectedSessionType = document.querySelector('input[name="session_type"]:checked')?.value;

    // If no session type is selected but we have a selected course, we should show the button
    // because practical will be auto-selected
    const hasValidSelection = selectedValue && sessionDate;

    if (selectedValue && efData[selectedValue] && sessionDate) {
        const data = efData[selectedValue];
        const enrollmentDate = data.enrollment_date;

        // Parse values as integers
        const pracAttended = parseInt(data.practical_attended) || 0;
        const pracRemaining = parseInt(data.practical_remaining) || 0;
        const theoAttended = parseInt(data.theoretical_attended) || 0;
        const theoRemaining = parseInt(data.theoretical_remaining) || 0;
        const totalPrac = parseInt(data.total_practical) || 0;
        const totalTheo = parseInt(data.total_theoretical) || 0;

        // Calculate total sessions (attended + remaining)
        const pracTotal = pracAttended + pracRemaining;
        const theoTotal = theoAttended + theoRemaining;

        // Update text displays
        practicalAttended.textContent = pracAttended;
        practicalRemaining.textContent = pracRemaining;
        theoreticalAttended.textContent = theoAttended;
        theoreticalRemaining.textContent = theoRemaining;

        // Update progress bars
        if (practicalProgress) {
            const pracProgress = calculateProgress(pracAttended, totalPrac);
            practicalProgress.style.width = pracProgress + '%';
        }

        if (theoreticalProgress) {
            const theoProgress = calculateProgress(theoAttended, totalTheo);
            theoreticalProgress.style.width = theoProgress + '%';
        }

        // Check if session date is before enrollment
        if (sessionDate < enrollmentDate) {
            courseHelpText.innerHTML = `<span class="text-red-600">Warning: Selected date is before enrollment (${enrollmentDate}). Choose a date on or after enrollment.</span>`;
            downloadBtn.disabled = true;
            downloadBtn.title = "Session date cannot be before enrollment";
        } else {
            // Check if there are remaining sessions
            const remainingPrac = parseInt(data.practical_remaining);
            const remainingTheo = parseInt(data.theoretical_remaining);

            // Disable/enable based on remaining sessions
            const radioPractical = document.querySelector('input[value="practical"]');
            const radioTheoretical = document.querySelector('input[value="theoretical"]');

            if (remainingPrac <= 0) {
                radioPractical.disabled = true;
                radioPractical.closest('label').classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                radioPractical.disabled = false;
                radioPractical.closest('label').classList.remove('opacity-50', 'cursor-not-allowed');
            }

            if (remainingTheo <= 0) {
                radioTheoretical.disabled = true;
                radioTheoretical.closest('label').classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                radioTheoretical.disabled = false;
                radioTheoretical.closest('label').classList.remove('opacity-50', 'cursor-not-allowed');
            }

            courseHelpText.innerHTML = `Enrollment date: ${enrollmentDate} | Total Practical: ${totalPrac} | Total Theoretical: ${totalTheo}`;
            downloadBtn.disabled = false;
            downloadBtn.title = "";
        }

        // Set course_id
        if (data.course_id) {
            courseIdInput.value = data.course_id;
        }

        // Auto-select practical by default if no session type is selected
        if (!selectedSessionType) {
            const practicalRadio = document.querySelector('input[value="practical"]');
            if (practicalRadio && !practicalRadio.disabled) {
                selectSession('practical');
                selectedSessionType = 'practical'; // Update for button visibility
            } else if (radioTheoretical && !radioTheoretical.disabled) {
                selectSession('theoretical');
                selectedSessionType = 'theoretical'; // Update for button visibility
            }
        }

        // Show previous classes button if we have a selected course AND (selected session type OR auto-selected practical)
        const previousClassesSection = document.getElementById('previousClassesSection');

        // Important: Show the button if we have a selected course, regardless of whether session type is explicitly selected
        // Since we either have a selected session type OR we auto-selected practical above
        if (selectedValue) {
            previousClassesSection.style.display = 'block';
        } else {
            previousClassesSection.style.display = 'none';
        }

    } else {
        practicalAttended.textContent = '-';
        practicalRemaining.textContent = '-';
        theoreticalAttended.textContent = '-';
        theoreticalRemaining.textContent = '-';

        if (practicalProgress) practicalProgress.style.width = '0%';
        if (theoreticalProgress) theoreticalProgress.style.width = '0%';

        courseIdInput.value = '';
        courseHelpText.textContent = '';
        downloadBtn.disabled = true;

        // Enable all radios but reset
        document.querySelectorAll('input[name="session_type"]').forEach(radio => {
            radio.disabled = false;
                radio.closest('label').classList.remove('opacity-50', 'cursor-not-allowed');
            });

            // Hide previous classes button
            document.getElementById('previousClassesSection').style.display = 'none';
        }
    }

    function updateDateValidation() {
        const selectedValue = efSelect.value;
        const sessionDate = sessionDateInput.value;
        const today = new Date().toISOString().split('T')[0];

        if (sessionDate > today) {
            dateHelpText.innerHTML = `<span class="text-red-600">Cannot select future dates. Today is ${today}.</span>`;
            downloadBtn.disabled = true;
            downloadBtn.title = "Cannot create tickets for future dates";
        } else if (selectedValue && efData[selectedValue]) {
            const enrollmentDate = efData[selectedValue].enrollment_date;
            if (sessionDate < enrollmentDate) {
                dateHelpText.innerHTML = `<span class="text-red-600">Selected date is before enrollment (${enrollmentDate}).</span>`;
                downloadBtn.disabled = true;
                downloadBtn.title = "Session date cannot be before enrollment";
            } else {
                dateHelpText.textContent = "Select the date when the session occurred. Cannot be in the future.";
                updateSessionInfo();
            }
        } else {
            dateHelpText.textContent = "Select the date when the session occurred. Cannot be in the future.";
            updateSessionInfo();
        }
    }

    // Event listeners
    sessionDateInput.addEventListener('change', updateDateValidation);
    efSelect.addEventListener('change', updateSessionInfo);

    // Style session type selection
    sessionOptions.forEach(option => {
        const radio = option.parentElement.querySelector('input[type="radio"]');
        const label = option.closest('label');
        const checkIcon = option.querySelector('.check-icon');

        label.addEventListener('click', function (e) {
            if (radio.disabled) {
                e.preventDefault();
                return;
            }

            // Reset all options
            sessionOptions.forEach(opt => {
                opt.classList.remove('border-blue-500', 'border-green-500', 'bg-blue-50', 'bg-green-50');
                const icon = opt.querySelector('.check-icon');
                if (icon) icon.classList.add('hidden');
            });

            // Style selected
            if (radio.value === 'practical') {
                option.classList.add('border-blue-500', 'bg-blue-50');
            } else {
                option.classList.add('border-green-500', 'bg-green-50');
            }

            // ✅ Show check icon
            if (checkIcon) {
                checkIcon.classList.remove('hidden');
            }
        });
    });

    // Initialize
    updateDateValidation();
    updateSessionInfo();
});
// Make efData globally accessible
window.efData = {
    @foreach($ef_lists as $ef)
        '{{ $ef->id }}': {
            practical_attended: {{ $ef->attended_practical ?? 0 }},
            practical_remaining: {{ $ef->remaining_practical ?? 0 }},
            theoretical_attended: {{ $ef->attended_theoretical ?? 0 }},
            theoretical_remaining: {{ $ef->remaining_theoretical ?? 0 }},
            total_practical: {{ $ef->total_class ?? ($ef->course->practical_sessions ?? 0) }},
            total_theoretical: {{ $ef->course->theoretical_sessions ?? 0 }},
            course_id: '{{ $ef->course_id }}',
            enrollment_date: '{{ $ef->created_at->format("Y-m-d") }}',
            ef_no: '{{ $ef->ef_no }}'
        },
    @endforeach
};
</script>

<style>
.session-option {
    transition: all 0.2s ease-in-out;
}

.session-option:hover {
    transform: translateY(-1px);
}

/* Hide the native calendar icon in Edge */
input[type="date"]::-webkit-calendar-picker-indicator {
    opacity: 0;
    cursor: pointer;
    height: auto;
    width: auto;
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background: transparent;
}

/* Custom scrollbar for recent tickets */
.max-h-\[600px\] {
    max-height: 600px;
}

.max-h-\[600px\]::-webkit-scrollbar {
    width: 6px;
}

.max-h-\[600px\]::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.max-h-\[600px\]::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.max-h-\[600px\]::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}
</style>
@endsection
