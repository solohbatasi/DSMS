@extends('layouts.app')
@props(['title' => 'Student Dashboard', 'subtitle' => 'Manage your courses and payments'])

@section('content')
@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Carbon;
@endphp

<div class="min-h-screen bg-gray-50">
    <!-- Enhanced Header -->
    @include('components.dashboard-header')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(!$student)
            <!-- No Student Profile Found -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
                <div class="max-w-md mx-auto">
                    <div class="h-16 w-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Profile Setup Required</h3>
                    <p class="text-gray-600 mb-6">Your student profile needs to be completed. Please contact administration to set up your student record.</p>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <p class="text-sm text-yellow-800">Student ID: <span class="font-mono">{{ Auth::user()->email }}</span></p>
                    </div>
                </div>
            </div>
        @else
            <!-- Welcome Section -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl shadow-lg p-6 mb-8 text-white">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold mb-2">Welcome back, {{ $student->name ?? ' '}}! </h1>
                        <p class="text-blue-100 opacity-90">Here's your academic and financial overview</p>
                        <div class="flex flex-wrap gap-4 mt-4">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="text-sm">ID: {{ $student->id_no ?? ' '}}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-sm">{{ $student->email ?? ' '}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2">
                            <p class="text-sm opacity-90">Current Status</p>
                            <p class="font-semibold">
                                @if($active_courses->count() > 0)
                                     Active Student
                                @else
                                     Enrollment Pending
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Fees -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-200 hover:shadow-md">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="h-12 w-12 rounded-lg bg-blue-50 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.567-3 3.5S10.343 15 12 15s3-1.567 3-3.5S13.657 8 12 8z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Fees</dt>
                                <dd class="text-2xl font-semibold text-gray-900">KES {{ number_format($total_fee, 2) }}</dd>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Amount Paid -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-200 hover:shadow-md">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="h-12 w-12 rounded-lg bg-green-50 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <dt class="text-sm font-medium text-gray-500 truncate">Amount Paid</dt>
                                <dd class="text-2xl font-semibold text-gray-900">KES {{ number_format($total_paid, 2) }}</dd>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            <span class="font-medium text-green-600">{{ number_format($payment_progress, 1) }}%</span>
                            <span class="text-gray-500 ml-1">paid</span>
                        </div>
                    </div>
                </div>

                <!-- Balance Due -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-200 hover:shadow-md">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="h-12 w-12 rounded-lg bg-amber-50 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <dt class="text-sm font-medium text-gray-500 truncate">Balance Due</dt>
                                <dd class="text-2xl font-semibold text-gray-900">KES {{ number_format($total_balance, 2) }}</dd>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            <span class="font-medium text-amber-600">{{ number_format(100 - $payment_progress, 1) }}%</span>
                            <span class="text-gray-500 ml-1">remaining</span>
                        </div>
                    </div>
                </div>

                <!-- Active Courses -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-200 hover:shadow-md">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="h-12 w-12 rounded-lg bg-purple-50 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <dt class="text-sm font-medium text-gray-500 truncate">Active Courses</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ $active_courses->count() }}</dd>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Courses & Progress -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- My Courses Section -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-900">My Courses & Payment Progress</h3>
                        </div>
                        <div class="p-6">
                            @if($courses_with_progress->count() > 0)
                                <div class="space-y-6">
                                    @foreach($courses_with_progress as $course_progress)
                                    @php
                                        $ef_list = $course_progress['ef_list'];
                                        $course = $ef_list->course;
                                    @endphp
                                    <div class="border border-gray-200 rounded-lg p-5 hover:border-blue-300 transition-colors duration-200">
                                        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                                            <div class="flex-1">
                                                <h4 class="text-lg font-semibold text-gray-900 mb-1">{{ $course->course ?? 'Course Not Found' }}</h4>
                                                <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                                                        </svg>
                                                        EF No: {{ $ef_list->ef_no ?? ' ' }}
                                                    </span>
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        Status:
                                                        <span class="ml-1 font-medium
                                                            {{ $ef_list->status === 'active' ? 'text-green-600' :
                                                               ($ef_list->status === 'verified' ? 'text-blue-600' : 'text-yellow-600') }}">
                                                            {{ ucfirst($ef_list->status) }}
                                                        </span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="mt-3 md:mt-0 text-right">
                                                <div class="text-2xl font-bold text-gray-900">
                                                    KES {{ number_format($ef_list->total_fee, 2) }}
                                                </div>
                                                <div class="text-sm text-gray-500">Total Fee</div>
                                            </div>
                                        </div>

                                        <!-- Progress Bar -->
                                        <div class="mb-3">
                                            <div class="flex justify-between text-sm text-gray-600 mb-2">
                                                <span>Payment Progress</span>
                                                <span>{{ number_format($course_progress['progress'], 1) }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                <div class="bg-green-600 h-2.5 rounded-full transition-all duration-500"
                                                     style="width: {{ $course_progress['progress'] }}%"></div>
                                            </div>
                                        </div>

                                        <!-- Financial Breakdown -->
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                            <div class="text-center p-3 bg-green-50 rounded-lg">
                                                <div class="text-green-700 font-semibold">KES {{ number_format($ef_list->paid_amount, 2) }}</div>
                                                <div class="text-green-600 text-xs">Paid Amount</div>
                                            </div>
                                            <div class="text-center p-3 bg-amber-50 rounded-lg">
                                                <div class="text-amber-700 font-semibold">KES {{ number_format($ef_list->balance, 2) }}</div>
                                                <div class="text-amber-600 text-xs">Balance Due</div>
                                            </div>
                                            <div class="text-center p-3 bg-blue-50 rounded-lg">
                                                <div class="text-blue-700 font-semibold">{{ $course_progress['payment_status'] }}</div>
                                                <div class="text-blue-600 text-xs">Payment Status</div>
                                            </div>
                                        </div>

                                        <!-- Course Details -->
                                        @if($course)
                                        <div class="mt-4 pt-4 border-t border-gray-200">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Duration: {{ $course->training_duration }}
                                                </div>
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                                    </svg>
                                                    Sessions: {{ $course->total_sessions ?? ($course->practical_sessions + $course->theoretical_sessions) }}
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <div class="h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Courses Enrolled</h3>
                                    <p class="text-gray-600 mb-4">You haven't been enrolled in any courses yet.</p>
                                    <button class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Browse Courses
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column - Profile & Recent Activity -->
                <div class="space-y-8">
                    <!-- Student Profile Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-900">Student Profile</h3>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center space-x-4 mb-6">
                                <div class="h-16 w-16 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center border-4 border-white shadow-sm">
                                    <span class="text-xl font-bold text-blue-700">
                                        {{ strtoupper(substr($student->name, 0, 1)) }}
                                    </span>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">{{ $student->name }}</h4>
                                    <p class="text-sm text-gray-600">{{ $student->id_no }}</p>
                                </div>
                            </div>

                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Email:</span>
                                    <span class="text-gray-900 font-medium">{{ $student->email }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Contact:</span>
                                    <span class="text-gray-900 font-medium">{{ $student->contact ?? 'Not provided' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Address:</span>
                                    <span class="text-gray-900 font-medium">{{ $student->address ?? 'Not provided' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Member since:</span>
                                    <span class="text-gray-900 font-medium">{{ \Carbon\Carbon::parse($student->created_at)->format('M d, Y') }}</span>
                                </div>
                            </div>

                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <button class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Update Profile
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Payments -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-900">Recent Payments</h3>
                        </div>
                        <div class="p-6">
                            @if($recent_payments->count() > 0)
                                <div class="space-y-4">
                                    @foreach($recent_payments as $payment)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-150">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $payment->efList->course->course ?? 'N/A' }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') : 'Date N/A' }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-semibold text-green-600">
                                                KES {{ number_format($payment->amount, 2) }}
                                            </p>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                {{ $payment->payment_status === 'completed' ? 'bg-green-100 text-green-800' :
                                                   ($payment->payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                                   'bg-red-100 text-red-800') }}">
                                                {{ ucfirst($payment->payment_status) }}
                                            </span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="mt-4 text-center">
                                    <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-500 transition-colors duration-200">
                                        View all payments →
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <div class="h-12 w-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.567-3 3.5S10.343 15 12 15s3-1.567 3-3.5S13.657 8 12 8z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-gray-600 text-sm">No payment history found</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 gap-3">
                                <button class="w-full inline-flex items-center justify-center px-4 py-3 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                    Make Payment
                                </button>
                                <button class="w-full inline-flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Download Invoice
                                </button>
                                <button class="w-full inline-flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                    </svg>
                                    Contact Support
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Payment Modal (You can expand this functionality later) -->
<div id="paymentModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <!-- Payment modal content would go here -->
</div>

<style>
    @keyframes fade-in-up {
        from {
            opacity: 0;
            transform: translateY(1rem);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .animate-fade-in-up {
        animation: fade-in-up 0.3s ease-out;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add any interactive functionality here
    console.log('Student dashboard loaded successfully');

    // Example: Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
</script>
@endsection
