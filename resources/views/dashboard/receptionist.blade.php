@extends('layouts.app')
@props(['title' => 'Receptionist Dashboard', 'subtitle' => 'Manage students, enrollments, and payments'])

@section('content')
@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Carbon;
@endphp

<div class="min-h-screen bg-gray-50">
    @include('components.dashboard-header')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-teal-600 to-cyan-700 rounded-2xl shadow-lg p-6 mb-8 text-white">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl font-bold mb-2">Welcome back, {{ $user->name }}!</h1>
                    <p class="text-teal-100 opacity-90">Here's your administrative overview and quick actions</p>
                    <div class="flex flex-wrap gap-4 mt-4">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span class="text-sm">Receptionist</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.567-3 3.5S10.343 15 12 15s3-1.567 3-3.5S13.657 8 12 8z"></path>
                            </svg>
                            <span class="text-sm">{{ $total_students }} Students</span>
                        </div>
                    </div>
                </div>
                <div class="mt-4 md:mt-0">
                    <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2">
                        <p class="text-sm opacity-90">Today's Activity</p>
                        <p class="font-semibold">{{ $new_students_today }} New Students</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 rounded-xl shadow-sm border border-blue-400 overflow-hidden transition-all duration-200 hover:shadow-md hover:scale-[1.02] cursor-pointer">
                <a href="https://lim110.truehost.cloud:2096/webmaillogout.cgi" target="_blank" class="block">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="h-12 w-12 rounded-lg bg-white/20 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <dt class="text-sm font-medium text-white/90 truncate">Email Access</dt>
                                <dd class="text-2xl font-semibold text-white">Webmail</dd>
                            </div>
                        </div>
                    </div>
                    <div class="bg-blue-800/40 px-5 py-3">
                        <div class="text-sm">
                            <span class="font-medium text-white hover:text-white/90 transition-colors duration-200 flex items-center">
                                Open Webmail Portal
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </span>
                        </div>
                    </div>
                </a>
            </div>
            <!-- New Students Today -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-200 hover:shadow-md">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-lg bg-blue-50 flex items-center justify-center">
                                <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <dt class="text-sm font-medium text-gray-500 truncate">New Students Today</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $new_students_today }}</dd>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm text-gray-600">
                        <span class="font-medium text-blue-600">{{ $total_students }}</span>
                        <span class="ml-1">total students</span>
                    </div>
                </div>
            </div>

            <!-- Payments Today -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-200 hover:shadow-md">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-lg bg-green-50 flex items-center justify-center">
                                <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.567-3 3.5S10.343 15 12 15s3-1.567 3-3.5S13.657 8 12 8z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <dt class="text-sm font-medium text-gray-500 truncate">Payments Today</dt>
                            <dd class="text-2xl font-semibold text-gray-900">KES {{ number_format($payments_today, 2) }}</dd>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm text-gray-600">
                        <span class="font-medium text-green-600">{{ $new_ef_lists_today }}</span>
                        <span class="ml-1">new enrollments</span>
                    </div>
                </div>
            </div>

            <!-- Pending Verifications -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-200 hover:shadow-md">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-lg bg-amber-50 flex items-center justify-center">
                                <svg class="h-6 w-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <dt class="text-sm font-medium text-gray-500 truncate">Pending Verifications</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $pending_verifications }}</dd>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <span class="font-medium text-amber-600">Action required</span>
                    </div>
                </div>
            </div>


        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Recent Activity -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Analytics Section -->


                <!-- Pending Verifications -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Pending EF List Verifications</h3>
                        <span class="bg-amber-100 text-amber-800 text-sm font-medium px-2.5 py-0.5 rounded-full">
                            {{ $pending_verifications }} pending
                        </span>
                    </div>
                    <div class="p-6">
                        @if($pending_ef_lists->count() > 0)
                            <div class="space-y-4">
                                @foreach($pending_ef_lists as $ef_list)
                                <div class="border border-amber-200 rounded-lg p-4 bg-amber-50 hover:bg-amber-100 transition-colors duration-150">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex-1">
                                            <h4 class="text-lg font-semibold text-gray-900 mb-1">{{ $ef_list->student->name }}</h4>
                                            <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                                                    </svg>
                                                    EF No: {{ $ef_list->ef_no }}
                                                </span>
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                    </svg>
                                                    {{ $ef_list->course->course }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4 text-right">
                                            <div class="text-2xl font-bold text-gray-900">
                                                KES {{ number_format($ef_list->total_fee, 2) }}
                                            </div>
                                            <div class="text-sm text-gray-500">Total Fee</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between pt-3 border-t border-amber-200">
                                        <div class="text-sm text-gray-600">
                                            Created: {{ $ef_list->created_at->format('M d, Y h:i A') }}
                                        </div>
                                        <div class="flex space-x-2">
                                            <button onclick="verifyEFList({{ $ef_list->id }}, 'verified')" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors duration-200">
                                                Verify
                                            </button>
                                            <button onclick="verifyEFList({{ $ef_list->id }}, 'active')" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">
                                                Activate
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="mt-4 text-center">
                                <a href="{{ route('receptionist.ef-lists') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500 transition-colors duration-200">
                                    View all pending verifications →
                                </a>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="h-16 w-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">All Caught Up!</h3>
                                <p class="text-gray-600">No pending EF list verifications at the moment.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recent Students & Payments -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Recent Students -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-900">Recent Student Registrations</h3>
                        </div>
                        <div class="p-6">
                            @if($recent_students->count() > 0)
                                <div class="space-y-3">
                                    @foreach($recent_students as $student)
                                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center">
                                                <span class="text-sm font-bold text-blue-700">
                                                    {{ strtoupper(substr($student->name, 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $student->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $student->id_no }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs text-gray-500">{{ $student->created_at->format('M d') }}</p>
                                            <p class="text-xs text-gray-400">{{ $student->created_at->format('h:i A') }}</p>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @can('view students')
                                <div class="mt-4 text-center">
                                    <a href="{{ route('students.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500 transition-colors duration-200">
                                        View all students →
                                    </a>
                                </div>
                                @endcan
                            @else
                                <div class="text-center py-4">
                                    <p class="text-gray-600 text-sm">No student registrations yet</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Recent Payments -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-900">Recent Payments</h3>
                        </div>
                        <div class="p-6">
                            @if($recent_payments->count() > 0)
                                <div class="space-y-3">
                                    @foreach($recent_payments as $payment)
                                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $payment->efList->student->name ?? 'N/A' }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ $payment->efList->course->course ?? 'N/A' }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-semibold text-green-600">
                                                KES {{ number_format($payment->amount, 2) }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('M d') : 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="mt-4 text-center">
                                    @can('view payments')
                                    <a href="{{ route('payments.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500 transition-colors duration-200">
                                        View all payments →
                                    </a>
                                    @endcan
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <p class="text-gray-600 text-sm">No payments recorded yet</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                   <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Monthly Enrollment Trends -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-900">Monthly Enrollment Trends</h3>
                            <p class="text-sm text-gray-600">Last 6 months performance</p>
                        </div>
                        <div class="p-6">
                            <div class="h-80">
                                <canvas id="enrollmentTrendChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Course Distribution -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-900">Course Enrollment Distribution</h3>
                            <p class="text-sm text-gray-600">Last 3 months</p>
                        </div>
                        <div class="p-6">
                            <div class="h-80">
                                <canvas id="courseDistributionChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Weekly Trends -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">Weekly Enrollment Trends</h3>
                        <p class="text-sm text-gray-600">Last 8 weeks</p>
                    </div>
                    <div class="p-6">
                        <div class="h-64">
                            <canvas id="weeklyTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Quick Actions & Stats -->
            <div class="space-y-8">
                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-3">
                            @can('view students')
                            <a href="{{ route('students.index') }}" class="w-full inline-flex items-center justify-center px-4 py-3 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                </svg>
                                Register New Student
                            </a>
                            @endcan

                            @can('view payments')
                            <a href="{{ route('payments.index') }}" class="w-full inline-flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                                Record Payment
                            </a>
                            @endcan
                            <a href="#" class="w-full inline-flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Generate Reports
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Expiring EF Lists -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">Follow-up Required</h3>
                    </div>
                    <div class="p-6">
                        @if($expiring_ef_lists->count() > 0)
                            <div class="space-y-3">
                                @foreach($expiring_ef_lists as $ef_list)
                                <div class="border border-orange-200 rounded-lg p-3 bg-orange-50">
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-sm font-medium text-gray-900">{{ $ef_list->student->name }}</p>
                                        <span class="bg-orange-100 text-orange-800 text-xs font-medium px-2 py-0.5 rounded">
                                            KES {{ number_format($ef_list->balance, 2) }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-600 mb-2">{{ $ef_list->course->course }}</p>
                                    <div class="flex items-center justify-between text-xs text-gray-500">
                                        <span>EF: {{ $ef_list->ef_no }}</span>
                                        <span>{{ $ef_list->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div class="h-12 w-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-600 text-sm">No follow-ups required</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- System Statistics -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">System Statistics</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Total Students</span>
                                <span class="text-lg font-semibold text-blue-600">{{ $total_students }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Active Courses</span>
                                <span class="text-lg font-semibold text-purple-600">{{ $total_courses }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Active Enrollments</span>
                                <span class="text-lg font-semibold text-green-600">{{ $total_active_ef_lists }}</span>
                            </div>
                            <div class="pt-4 border-t border-gray-200">
                                <div class="text-center">
                                    <p class="text-sm text-gray-600">Today's Revenue</p>
                                    <p class="text-2xl font-bold text-teal-600">KES {{ number_format($payments_today, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Verification Modal -->
<div id="verificationModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4" id="modalTitle">Verify EF List</h3>
                <p class="text-sm text-gray-600 mb-4" id="modalMessage">Are you sure you want to verify this EF list?</p>

                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                    <button type="button" id="confirmVerify" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm">
                        Confirm
                    </button>
                    <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentEFListId = null;
let currentAction = null;

function verifyEFList(efListId, action) {
    currentEFListId = efListId;
    currentAction = action;

    const modal = document.getElementById('verificationModal');
    const title = document.getElementById('modalTitle');
    const message = document.getElementById('modalMessage');

    if (action === 'verified') {
        title.textContent = 'Verify EF List';
        message.textContent = 'Are you sure you want to mark this EF list as verified? This will allow the student to proceed with payments.';
    } else {
        title.textContent = 'Activate EF List';
        message.textContent = 'Are you sure you want to activate this EF list? The student will be able to download session tickets.';
    }

    modal.classList.remove('hidden');
}

function closeModal() {
    const modal = document.getElementById('verificationModal');
    modal.classList.add('hidden');
    currentEFListId = null;
    currentAction = null;
}

document.getElementById('confirmVerify').addEventListener('click', async function() {
    if (!currentEFListId || !currentAction) return;

    try {
        const response = await fetch('{{ route("receptionist.verify-ef-list") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                ef_list_id: currentEFListId,
                action: currentAction
            })
        });

        const result = await response.json();

        if (response.ok) {
            // Reload the page to reflect changes
            window.location.reload();
        } else {
            alert('Error: ' + (result.message || 'Failed to verify EF list'));
        }
    } catch (error) {
        alert('Network error. Please try again.');
    }

    closeModal();
});

// Close modal when clicking outside
document.getElementById('verificationModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Enrollment Trend Chart (Line Chart)
    const trendCtx = document.getElementById('enrollmentTrendChart').getContext('2d');
    const enrollmentTrendChart = new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: @json($chart_months),
            datasets: [
                {
                    label: 'Enrollments',
                    data: @json($chart_enrollments),
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Revenue (KES)',
                    data: @json($chart_revenue),
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Number of Enrollments'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Revenue (KES)'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.dataset.label === 'Revenue (KES)') {
                                label += 'KES ' + context.parsed.y.toLocaleString();
                            } else {
                                label += context.parsed.y;
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });

    // Course Distribution Chart (Doughnut Chart)
    const courseCtx = document.getElementById('courseDistributionChart').getContext('2d');
    const courseDistributionChart = new Chart(courseCtx, {
        type: 'doughnut',
        data: {
            labels: @json($chart_course_labels),
            datasets: [{
                data: @json($chart_course_data),
                backgroundColor: @json($chart_course_colors),
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        boxWidth: 12,
                        padding: 15
                    }
                }
            },
            cutout: '60%'
        }
    });

    // Weekly Trends Chart (Bar Chart)
    const weeklyCtx = document.getElementById('weeklyTrendChart').getContext('2d');
    const weeklyTrendChart = new Chart(weeklyCtx, {
        type: 'bar',
        data: {
            labels: @json($chart_weeks),
            datasets: [{
                label: 'Enrollments',
                data: @json($chart_weekly_enrollments),
                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                borderColor: '#3B82F6',
                borderWidth: 1,
                borderRadius: 4,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Enrollments'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Weeks'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>
@endsection
