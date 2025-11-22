@extends('layouts.app')
@props(['title' => 'Dashboard', 'subtitle' => 'Manage all activities in the system from a central position'])

@section('content')
@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Auth;
@endphp

<div class="min-h-screen bg-gray-50">
    <!-- Enhanced Header -->

    @include('components.dashboard-header')



    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Enhanced Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

            <!-- Total Customers -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-200 hover:shadow-md">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-lg bg-blue-50 flex items-center justify-center">
                                <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5V8H2v12h5m10 0a2 2 0 01-2 2H9a2 2 0 01-2-2m10 0v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Customers</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $total_customers ?? ''}}</dd>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="#"
                            class="font-medium text-blue-600 hover:text-blue-700 transition-colors duration-200 flex items-center">
                            View all customers
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Active Customers -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-200 hover:shadow-md">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-lg bg-green-50 flex items-center justify-center">
                                <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <dt class="text-sm font-medium text-gray-500 truncate">Active Customers</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $active_customers ?? '' }}</dd>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="#"
                            class="font-medium text-green-600 hover:text-green-700 transition-colors duration-200 flex items-center">
                            Manage customers
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Suspended Customers -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-200 hover:shadow-md">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-lg bg-red-50 flex items-center justify-center">
                                <svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <dt class="text-sm font-medium text-gray-500 truncate">Suspended Customers</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $suspended_customers ?? '' }}</dd>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="#"
                            class="font-medium text-red-600 hover:text-red-700 transition-colors duration-200 flex items-center">
                            Review suspended accounts
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Total Bills -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-200 hover:shadow-md">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-lg bg-yellow-50 flex items-center justify-center">
                                <svg class="h-6 w-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 7h18M5 7V5a2 2 0 012-2h10a2 2 0 012 2v2m-2 4v9a2 2 0 01-2 2H9a2 2 0 01-2-2v-9m10 0H7" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Bills</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $total_bills ?? ''}}</dd>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('bills.index') }}"
                            class="font-medium text-yellow-600 hover:text-yellow-700 transition-colors duration-200 flex items-center">
                            View all bills
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Unpaid Bills -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-200 hover:shadow-md">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-lg bg-orange-50 flex items-center justify-center">
                                <svg class="h-6 w-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <dt class="text-sm font-medium text-gray-500 truncate">Unpaid Bills</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $unpaid_bills ?? ''}}</dd>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('bills.index') }}"
                            class="font-medium text-orange-600 hover:text-orange-700 transition-colors duration-200 flex items-center">
                            Review unpaid bills
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Total Payments -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-200 hover:shadow-md">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-lg bg-purple-50 flex items-center justify-center">
                                <svg class="h-6 w-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 1.567-3 3.5S10.343 15 12 15s3-1.567 3-3.5S13.657 8 12 8z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Payments</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $total_payments ?? '' }}</dd>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('payments.index') }}"
                            class="font-medium text-purple-600 hover:text-purple-700 transition-colors duration-200 flex items-center">
                            View payment history
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Revenue Collected -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-200 hover:shadow-md col-span-1 md:col-span-2 lg:col-span-4">
                <div class="p-6 flex items-center justify-between">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Total Revenue Collected</dt>
                        <dd class="text-3xl font-bold text-gray-900 mt-1">KES 0</dd>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="h-16 w-16 rounded-xl bg-teal-50 flex items-center justify-center">
                            <svg class="h-10 w-10 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v8m4-4H8m12 4V8a4 4 0 00-4-4H8a4 4 0 00-4 4v8a4 4 0 004 4h8a4 4 0 004-4z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-10">

            <!-- Monthly Billing vs Collections -->
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-700">
                    Monthly Billing vs Collections
                </h3>
                <canvas id="billingCollectionsChart" height="120"></canvas>
            </div>

            <!-- Bill Status Distribution -->
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-700">
                    Bill Status Distribution
                </h3>
                <canvas id="billStatusChart" height="120"></canvas>
            </div>

        </div>


        <!-- Users Table Section -->
        @can('view users')
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Users</h3>
                    <div class="flex space-x-3">
                        <div class="relative">
                            <input type="text" placeholder="Search users..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <button class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            Filter
                        </button>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @if($recent_users->count() > 0)
                        @foreach($recent_users as $user)
                        @php
                        $roleName = $user->roles->first()?->name ?? 'attendee';
                        $status = $user->email_verified_at ? 'Verified' : 'Pending';
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($user->avatar)
                                    <img src="{{ Storage::url($user->avatar) }}"
                                        alt="{{ $user->name }}"
                                        class="h-10 w-10 rounded-full object-cover border border-gray-200">
                                    @else
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center border border-gray-200">
                                        <span class="text-sm font-semibold text-blue-700">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </span>
                                    </div>
                                    @endif
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    {{ $roleName === 'admin' ? 'bg-red-100 text-red-800' :
                       ($roleName === 'organizer' ? 'bg-blue-100 text-blue-800' :
                       ($roleName === 'vendor' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800')) }}">
                                    {{ ucfirst($roleName) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $status === 'Verified' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-start space-x-2">
                                    <!-- Edit Button -->
                                     @can('edit users')
                                    <button type="button"
                                        class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 edit-user-btn"
                                        data-user-id="{{ $user->id }}"
                                        data-user-name="{{ $user->name }}"
                                        data-user-email="{{ $user->email }}"
                                        data-user-role="{{ $roleName }}">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </button>
                                    @endcan
                                    <!-- Permissions Button -->
                                     @can('edit permissions')
                                    <button type="button"
                                        class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200 roles-permissions-btn"
                                        data-user-id="{{ $user->id }}"
                                        data-user-name="{{ $user->name }}"
                                        data-user-role="{{ $roleName }}">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                        Permissions
                                    </button>
                                    @endcan
                                    @can('delete users')
                                    <!-- Delete Button -->
                                    <button type="button"
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-lg text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200 delete-user-btn"
                                        data-user-id="{{ $user->id }}"
                                        data-user-name="{{ $user->name }}">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No users found</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by creating a new user.</p>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        Showing <span class="font-medium">{{ $recent_users->firstItem() }}</span>
                        to <span class="font-medium">{{ $recent_users->lastItem() }}</span>
                        of <span class="font-medium">{{ $recent_users->total() }}</span> results
                    </div>
                    <div class="flex space-x-2">
                        <!-- Previous Button -->
                        @if ($recent_users->onFirstPage())
                        <span class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-400 bg-white cursor-not-allowed">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Previous
                        </span>
                        @else
                        <a href="{{ $recent_users->previousPageUrl() }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Previous
                        </a>
                        @endif

                        <!-- Next Button -->
                        @if ($recent_users->hasMorePages())
                        <a href="{{ $recent_users->nextPageUrl() }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            Next
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                        @else
                        <span class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-400 bg-white cursor-not-allowed">
                            Next
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </span>
                        @endif
                    </div>
                </div>

                <!-- Page Numbers -->
                <div class="mt-2 flex justify-center">
                    <div class="flex space-x-1">
                        @foreach ($recent_users->getUrlRange(1, $recent_users->lastPage()) as $page => $url)
                        @if ($page == $recent_users->currentPage())
                        <span class="px-3 py-1 text-sm font-medium text-blue-600 bg-blue-100 rounded-lg">{{ $page }}</span>
                        @else
                        <a href="{{ $url }}" class="px-3 py-1 text-sm font-medium text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100">{{ $page }}</a>
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endcan

        @can('view applications')
        <!-- Pending Approvals Table Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Pending Approvals</h3>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-800">
                        {{ $pending_approvals }} items need approval
                    </span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted By</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Submitted</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        <!-- Sample approval items - you would replace with actual data -->
                        @foreach ($pending_approval_items as $item)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $item->first_name }} {{ $item->last_name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Plot: {{ $item->plot_number }}, House: {{ $item->house_number }}
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Water Application
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    {{ $item->email }}<br>
                                    <span class="text-gray-500 text-sm">{{ $item->phone }}</span>
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $item->created_at->format('M d, Y') }}
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                        Pending Review
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end space-x-2">
                                    <button class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-lg text-xs font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Approve
                                    </button>
                                    <button class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Review
                                    </button>
                                    <button class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-lg text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Reject
                                    </button>
                                </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        Showing <span class="font-medium">1</span> to <span class="font-medium">2</span> of <span class="font-medium">{{ $pending_approvals }}</span> pending items
                    </div>
                    <a href="" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors duration-200">
                        Review All Pending Items
                    </a>
                </div>
            </div>
        </div>
        @endcan

    </div>
</div>

<!-- Enhanced Roles & Permissions Modal -->
<div id="rolesPermissionsModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white" id="modal-title">
                                Manage Roles & Permissions
                            </h3>
                            <p class="text-blue-100 text-sm">Configure user access and capabilities</p>
                        </div>
                    </div>
                    <button type="button" id="closeRolesPermissionsModal" class="text-white hover:text-blue-200 transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Content -->
            <div class="bg-white px-6 py-6 max-h-96 overflow-y-auto">
                <form id="rolesPermissionsForm">
                    @csrf
                    <input type="hidden" id="rp_user_id" name="user_id">

                    <div class="space-y-6">
                        <!-- User Info -->
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                            <div class="flex items-center space-x-4">
                                <div id="rp_user_avatar" class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center border-2 border-white shadow-sm">
                                    <!-- Avatar will be dynamically set -->
                                </div>
                                <div>
                                    <h4 id="rp_user_name" class="text-lg font-semibold text-gray-900"></h4>
                                    <p id="rp_user_email" class="text-sm text-gray-600"></p>
                                    <p class="text-xs text-gray-500">User ID: <span id="rp_user_id_display"></span></p>
                                </div>
                            </div>
                        </div>

                        <!-- Role Selection -->
                        <div>
                            <label for="rp_role" class="block text-sm font-semibold text-gray-900 mb-3">User Role</label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <label class="relative flex cursor-pointer">
                                    <input type="radio" name="role" value="attendee" class="sr-only peer">
                                    <div class="w-full p-3 border-2 border-gray-200 rounded-xl text-center transition-all duration-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:ring-2 peer-checked:ring-blue-200 hover:border-gray-300">
                                        <div class="w-8 h-8 mx-auto mb-2 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                        <span class="block text-sm font-medium text-gray-900">Attendee</span>
                                        <span class="block text-xs text-gray-500 mt-1">Event Participant</span>
                                    </div>
                                </label>

                                <label class="relative flex cursor-pointer">
                                    <input type="radio" name="role" value="organizer" class="sr-only peer">
                                    <div class="w-full p-3 border-2 border-gray-200 rounded-xl text-center transition-all duration-200 peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:ring-2 peer-checked:ring-green-200 hover:border-gray-300">
                                        <div class="w-8 h-8 mx-auto mb-2 bg-green-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <span class="block text-sm font-medium text-gray-900">Organizer</span>
                                        <span class="block text-xs text-gray-500 mt-1">Event Creator</span>
                                    </div>
                                </label>

                                <label class="relative flex cursor-pointer">
                                    <input type="radio" name="role" value="vendor" class="sr-only peer">
                                    <div class="w-full p-3 border-2 border-gray-200 rounded-xl text-center transition-all duration-200 peer-checked:border-purple-500 peer-checked:bg-purple-50 peer-checked:ring-2 peer-checked:ring-purple-200 hover:border-gray-300">
                                        <div class="w-8 h-8 mx-auto mb-2 bg-purple-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                            </svg>
                                        </div>
                                        <span class="block text-sm font-medium text-gray-900">Vendor</span>
                                        <span class="block text-xs text-gray-500 mt-1">Service Provider</span>
                                    </div>
                                </label>

                                <label class="relative flex cursor-pointer">
                                    <input type="radio" name="role" value="admin" class="sr-only peer">
                                    <div class="w-full p-3 border-2 border-gray-200 rounded-xl text-center transition-all duration-200 peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:ring-2 peer-checked:ring-red-200 hover:border-gray-300">
                                        <div class="w-8 h-8 mx-auto mb-2 bg-red-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                            </svg>
                                        </div>
                                        <span class="block text-sm font-medium text-gray-900">Admin</span>
                                        <span class="block text-xs text-gray-500 mt-1">System Administrator</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Permissions Sections -->
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <label class="block text-sm font-semibold text-gray-900">Advanced Permissions</label>
                                <div class="flex items-center space-x-2">
                                    <button type="button" id="selectAllPermissions" class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                                        Select All
                                    </button>
                                    <span class="text-gray-300">|</span>
                                    <button type="button" id="deselectAllPermissions" class="text-xs text-gray-600 hover:text-gray-700 font-medium">
                                        Deselect All
                                    </button>
                                </div>
                            </div>

                            <div id="permissionsSections" class="space-y-4">
                                <!-- Loading state -->
                                <div class="text-center py-8">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                                    <p class="mt-3 text-sm text-gray-500">Loading permissions configuration...</p>
                                </div>
                            </div>
                        </div>

                        <!-- Warning Note -->
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-amber-800">
                                        Important Note
                                    </h3>
                                    <div class="mt-1 text-sm text-amber-700">
                                        <p>Changing roles will automatically assign default permissions. You can override them by selecting specific permissions above.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        <span id="selectedPermissionsCount">0</span> permissions selected
                    </div>
                    <div class="flex space-x-3">
                        <button type="button" id="closeRolesPermissionsModalBtn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="button" id="updateRolesPermissionsBtn" class="px-6 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg shadow-sm hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Update Permissions</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Edit User Modal -->
<div id="editUserModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white" id="modal-title">
                                Edit User
                            </h3>
                            <p class="text-blue-100 text-sm">Update user information</p>
                        </div>
                    </div>
                    <button type="button" id="closeEditModal" class="text-white hover:text-blue-200 transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Content -->
            <div class="bg-white px-6 py-6">
                <form id="editUserForm">
                    @csrf
                    <input type="hidden" id="edit_user_id" name="user_id">

                    <div class="space-y-5">
                        <!-- User Info Preview -->
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                            <div class="flex items-center space-x-4">
                                <div id="edit_user_avatar" class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center border-2 border-white shadow-sm">
                                    <!-- Avatar will be dynamically set -->
                                </div>
                                <div>
                                    <h4 id="edit_user_name_preview" class="text-lg font-semibold text-gray-900"></h4>
                                    <p id="edit_user_email_preview" class="text-sm text-gray-600"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Name Field -->
                        <div>
                            <label for="edit_name" class="block text-sm font-semibold text-gray-900 mb-2">Full Name</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <input type="text" name="name" id="edit_name" class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors duration-200" placeholder="Enter full name">
                            </div>
                        </div>

                        <!-- Email Field -->
                        <div>
                            <label for="edit_email" class="block text-sm font-semibold text-gray-900 mb-2">Email Address</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <input type="email" name="email" id="edit_email" class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors duration-200" placeholder="Enter email address">
                            </div>
                        </div>

                        <!-- Role Field -->
                        <div>
                            <label for="edit_role" class="block text-sm font-semibold text-gray-900 mb-3">User Role</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="relative flex cursor-pointer">
                                    <input type="radio" name="role" value="attendee" class="sr-only peer">
                                    <div class="w-full p-3 border-2 border-gray-200 rounded-xl text-center transition-all duration-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:ring-2 peer-checked:ring-blue-200 hover:border-gray-300">
                                        <div class="w-8 h-8 mx-auto mb-2 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                        <span class="block text-sm font-medium text-gray-900">Attendee</span>
                                    </div>
                                </label>

                                <label class="relative flex cursor-pointer">
                                    <input type="radio" name="role" value="organizer" class="sr-only peer">
                                    <div class="w-full p-3 border-2 border-gray-200 rounded-xl text-center transition-all duration-200 peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:ring-2 peer-checked:ring-green-200 hover:border-gray-300">
                                        <div class="w-8 h-8 mx-auto mb-2 bg-green-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <span class="block text-sm font-medium text-gray-900">Organizer</span>
                                    </div>
                                </label>

                                <label class="relative flex cursor-pointer">
                                    <input type="radio" name="role" value="vendor" class="sr-only peer">
                                    <div class="w-full p-3 border-2 border-gray-200 rounded-xl text-center transition-all duration-200 peer-checked:border-purple-500 peer-checked:bg-purple-50 peer-checked:ring-2 peer-checked:ring-purple-200 hover:border-gray-300">
                                        <div class="w-8 h-8 mx-auto mb-2 bg-purple-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                            </svg>
                                        </div>
                                        <span class="block text-sm font-medium text-gray-900">Vendor</span>
                                    </div>
                                </label>

                                <label class="relative flex cursor-pointer">
                                    <input type="radio" name="role" value="admin" class="sr-only peer">
                                    <div class="w-full p-3 border-2 border-gray-200 rounded-xl text-center transition-all duration-200 peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:ring-2 peer-checked:ring-red-200 hover:border-gray-300">
                                        <div class="w-8 h-8 mx-auto mb-2 bg-red-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                            </svg>
                                        </div>
                                        <span class="block text-sm font-medium text-gray-900">Admin</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-end space-x-3">
                    <button type="button" id="closeEditModalBtn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="button" id="updateUserBtn" class="px-6 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg shadow-sm hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Update User</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Delete User Modal -->
<div id="deleteUserModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
            <!-- Header -->
            <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white" id="modal-title">
                                Delete User
                            </h3>
                            <p class="text-red-100 text-sm">Remove user from system</p>
                        </div>
                    </div>
                    <button type="button" id="closeDeleteModal" class="text-white hover:text-red-200 transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Content -->
            <div class="bg-white px-6 py-6">
                <div class="flex items-center space-x-4 mb-6">
                    <div id="delete_user_avatar" class="h-16 w-16 rounded-full bg-gradient-to-br from-red-100 to-red-200 flex items-center justify-center border-2 border-white shadow-sm">
                        <!-- Avatar will be dynamically set -->
                    </div>
                    <div>
                        <h4 id="delete_user_name" class="text-lg font-semibold text-gray-900"></h4>
                        <p id="delete_user_email" class="text-sm text-gray-600"></p>
                        <p class="text-xs text-gray-500">User ID: <span id="delete_user_id_display"></span></p>
                    </div>
                </div>

                <!-- Warning Message -->
                <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                This action cannot be undone
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p>This will permanently delete the user account and all associated data. The user will no longer be able to access the system.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Confirmation Checkbox -->
                <div class="mt-4">
                    <label class="flex items-center space-x-3">
                        <input type="checkbox" id="deleteConfirmation" class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300 rounded transition-colors duration-200">
                        <span class="text-sm text-gray-700">
                            I understand that this action cannot be reversed
                        </span>
                    </label>
                </div>

                <form id="deleteUserForm" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" id="delete_user_id" name="user_id">
                </form>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        Proceed with caution
                    </div>
                    <div class="flex space-x-3">
                        <button type="button" id="closeDeleteModalBtn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="button" id="confirmDeleteBtn" disabled class="px-6 py-2 text-sm font-medium text-white bg-gradient-to-r from-red-600 to-red-700 rounded-lg shadow-sm hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 flex items-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            <span>Delete User</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Enhanced Edit User Modal
        const editUserModal = document.getElementById('editUserModal');
        const editUserBtns = document.querySelectorAll('.edit-user-btn');
        const closeEditModal = document.getElementById('closeEditModal');
        const closeEditModalBtn = document.getElementById('closeEditModalBtn');
        const updateUserBtn = document.getElementById('updateUserBtn');

        editUserBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const userId = this.getAttribute('data-user-id');
                const userName = this.getAttribute('data-user-name');
                const userEmail = this.closest('tr').querySelector('td:first-child .text-gray-500')?.textContent || 'No email';
                const userRole = this.getAttribute('data-user-role');
                const userAvatar = this.closest('tr').querySelector('img')?.src || '';

                document.getElementById('edit_user_id').value = userId;
                document.getElementById('edit_name').value = userName;
                document.getElementById('edit_email').value = userEmail;
                document.getElementById('edit_user_name_preview').textContent = userName;
                document.getElementById('edit_user_email_preview').textContent = userEmail;

                // Set user avatar
                const avatarContainer = document.getElementById('edit_user_avatar');
                if (userAvatar) {
                    avatarContainer.innerHTML = `<img src="${userAvatar}" alt="${userName}" class="h-12 w-12 rounded-full object-cover border-2 border-white shadow-sm">`;
                } else {
                    const initial = userName.charAt(0).toUpperCase();
                    avatarContainer.innerHTML = `<span class="text-lg font-semibold text-blue-700">${initial}</span>`;
                }

                // Set role radio button
                document.querySelectorAll('#editUserModal input[name="role"]').forEach(radio => {
                    radio.checked = radio.value === userRole;
                });

                editUserModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            });
        });

        function closeEditUserModal() {
            editUserModal.classList.add('hidden');
            document.body.style.overflow = '';
        }

        closeEditModal.addEventListener('click', closeEditUserModal);
        closeEditModalBtn.addEventListener('click', closeEditUserModal);

        updateUserBtn.addEventListener('click', function() {
            const userId = document.getElementById('edit_user_id').value;
            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('_method', 'PUT');
            formData.append('name', document.getElementById('edit_name').value);
            formData.append('email', document.getElementById('edit_email').value);
            formData.append('role', document.querySelector('#editUserModal input[name="role"]:checked')?.value);

            // Show loading state
            const originalText = updateUserBtn.innerHTML;
            updateUserBtn.innerHTML = `
            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mx-auto"></div>
            <span>Updating...</span>
        `;
            updateUserBtn.disabled = true;

            fetch(`/admin/users/${userId}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('User updated successfully', 'success');
                        closeEditUserModal();
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        throw new Error(data.message || 'Failed to update user');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification(error.message || 'An error occurred while updating the user', 'error');
                })
                .finally(() => {
                    updateUserBtn.innerHTML = originalText;
                    updateUserBtn.disabled = false;
                });
        });

        // Enhanced Delete User Modal
        const deleteUserModal = document.getElementById('deleteUserModal');
        const deleteUserBtns = document.querySelectorAll('.delete-user-btn');
        const closeDeleteModal = document.getElementById('closeDeleteModal');
        const closeDeleteModalBtn = document.getElementById('closeDeleteModalBtn');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        const deleteConfirmation = document.getElementById('deleteConfirmation');

        deleteUserBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const userId = this.getAttribute('data-user-id');
                const userName = this.getAttribute('data-user-name');
                const userEmail = this.closest('tr').querySelector('td:first-child .text-gray-500')?.textContent || 'No email';
                const userAvatar = this.closest('tr').querySelector('img')?.src || '';

                document.getElementById('delete_user_id').value = userId;
                document.getElementById('delete_user_name').textContent = userName;
                document.getElementById('delete_user_email').textContent = userEmail;
                document.getElementById('delete_user_id_display').textContent = userId;

                // Set user avatar
                const avatarContainer = document.getElementById('delete_user_avatar');
                if (userAvatar) {
                    avatarContainer.innerHTML = `<img src="${userAvatar}" alt="${userName}" class="h-16 w-16 rounded-full object-cover border-2 border-white shadow-sm">`;
                } else {
                    const initial = userName.charAt(0).toUpperCase();
                    avatarContainer.innerHTML = `<span class="text-xl font-semibold text-red-700">${initial}</span>`;
                }

                // Reset confirmation
                deleteConfirmation.checked = false;
                confirmDeleteBtn.disabled = true;

                deleteUserModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            });
        });

        function closeDeleteUserModal() {
            deleteUserModal.classList.add('hidden');
            document.body.style.overflow = '';
        }

        closeDeleteModal.addEventListener('click', closeDeleteUserModal);
        closeDeleteModalBtn.addEventListener('click', closeDeleteUserModal);

        // Enable delete button only when confirmation is checked
        deleteConfirmation.addEventListener('change', function() {
            confirmDeleteBtn.disabled = !this.checked;
        });

        confirmDeleteBtn.addEventListener('click', function() {
            if (!deleteConfirmation.checked) return;

            const userId = document.getElementById('delete_user_id').value;
            const formData = new FormData(document.getElementById('deleteUserForm'));

            // Show loading state
            const originalText = confirmDeleteBtn.innerHTML;
            confirmDeleteBtn.innerHTML = `
            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mx-auto"></div>
            <span>Deleting...</span>
        `;
            confirmDeleteBtn.disabled = true;

            fetch(`/admin/users/${userId}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('User deleted successfully', 'success');
                        closeDeleteUserModal();
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        throw new Error(data.message || 'Failed to delete user');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification(error.message || 'An error occurred while deleting the user', 'error');
                })
                .finally(() => {
                    confirmDeleteBtn.innerHTML = originalText;
                    confirmDeleteBtn.disabled = false;
                });
        });

        // Close modals when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === editUserModal) {
                closeEditUserModal();
            }
            if (event.target === deleteUserModal) {
                closeDeleteUserModal();
            }
        });

        // Enhanced notification function (reuse from previous)
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-xl shadow-lg text-white transform transition-all duration-300 ${
            type === 'success' ? 'bg-green-500' :
            type === 'error' ? 'bg-red-500' :
            'bg-blue-500'
        } animate-fade-in-up`;
            notification.innerHTML = `
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${
                        type === 'success' ? 'M5 13l4 4L19 7' :
                        type === 'error' ? 'M6 18L18 6M6 6l12 12' :
                        'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
                    }"></path>
                </svg>
                <span class="font-medium">${message}</span>
            </div>
        `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.classList.add('opacity-0', 'transform', 'scale-95');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Enhanced Roles & Permissions Modal
        const rolesPermissionsModal = document.getElementById('rolesPermissionsModal');
        const rolesPermissionsBtns = document.querySelectorAll('.roles-permissions-btn');
        const closeRolesPermissionsModal = document.getElementById('closeRolesPermissionsModal');
        const closeRolesPermissionsModalBtn = document.getElementById('closeRolesPermissionsModalBtn');
        const updateRolesPermissionsBtn = document.getElementById('updateRolesPermissionsBtn');
        const selectAllPermissionsBtn = document.getElementById('selectAllPermissions');
        const deselectAllPermissionsBtn = document.getElementById('deselectAllPermissions');
        const selectedPermissionsCount = document.getElementById('selectedPermissionsCount');

        // Enhanced modal opening
        rolesPermissionsBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const userId = this.getAttribute('data-user-id');
                const userName = this.getAttribute('data-user-name');
                const userEmail = this.closest('tr').querySelector('td:first-child .text-gray-500')?.textContent || 'No email';
                const userRole = this.getAttribute('data-user-role');
                const userAvatar = this.closest('tr').querySelector('img')?.src || '';

                document.getElementById('rp_user_id').value = userId;
                document.getElementById('rp_user_name').textContent = userName;
                document.getElementById('rp_user_email').textContent = userEmail;
                document.getElementById('rp_user_id_display').textContent = userId;

                // Set user avatar
                const avatarContainer = document.getElementById('rp_user_avatar');
                if (userAvatar) {
                    avatarContainer.innerHTML = `<img src="${userAvatar}" alt="${userName}" class="h-12 w-12 rounded-full object-cover border-2 border-white shadow-sm">`;
                } else {
                    const initial = userName.charAt(0).toUpperCase();
                    avatarContainer.innerHTML = `<span class="text-lg font-semibold text-blue-700">${initial}</span>`;
                }

                // Set role radio button
                document.querySelectorAll('input[name="role"]').forEach(radio => {
                    radio.checked = radio.value === userRole;
                });

                // Load user permissions and render sections
                loadUserPermissions(userId);

                rolesPermissionsModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden'; // Prevent background scrolling
            });
        });

        // Close modal functions
        function closeRolesModal() {
            rolesPermissionsModal.classList.add('hidden');
            document.body.style.overflow = ''; // Restore scrolling
        }

        closeRolesPermissionsModal.addEventListener('click', closeRolesModal);
        closeRolesPermissionsModalBtn.addEventListener('click', closeRolesModal);

        // Enhanced permission submission with proper array handling
        updateRolesPermissionsBtn.addEventListener('click', function() {
            const userId = document.getElementById('rp_user_id').value;
            const selectedRole = document.querySelector('input[name="role"]:checked')?.value;

            if (!selectedRole) {
                showNotification('Please select a role', 'error');
                return;
            }

            // Get selected permissions as array
            const selectedPermissions = Array.from(document.querySelectorAll('input[name="permissions[]"]:checked'))
                .map(checkbox => checkbox.value);

            // Create form data with proper array format
            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('role', selectedRole);

            // Append each permission individually for proper array handling
            selectedPermissions.forEach(permission => {
                formData.append('permissions[]', permission);
            });

            // Show loading state
            const originalText = updateRolesPermissionsBtn.innerHTML;
            updateRolesPermissionsBtn.innerHTML = `
            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mx-auto"></div>
            <span>Updating...</span>
        `;
            updateRolesPermissionsBtn.disabled = true;

            fetch(`/admin/users/${userId}/permissions`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showNotification('User permissions updated successfully', 'success');
                        closeRolesModal();
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        throw new Error(data.message || 'Failed to update permissions');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification(error.message || 'An error occurred while updating permissions', 'error');
                })
                .finally(() => {
                    // Restore button state
                    updateRolesPermissionsBtn.innerHTML = originalText;
                    updateRolesPermissionsBtn.disabled = false;
                });
        });

        // Select All / Deselect All functionality
        selectAllPermissionsBtn.addEventListener('click', function() {
            document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
                checkbox.checked = true;
            });
            updatePermissionsCount();
        });

        deselectAllPermissionsBtn.addEventListener('click', function() {
            document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
                checkbox.checked = false;
            });
            updatePermissionsCount();
        });

        // Update permissions count
        function updatePermissionsCount() {
            const count = document.querySelectorAll('input[name="permissions[]"]:checked').length;
            selectedPermissionsCount.textContent = count;
        }

        // Enhanced permission loading and rendering
        function loadUserPermissions(userId) {
            fetch(`/admin/users/${userId}/permissions`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to load permissions');
                    }
                    return response.json();
                })
                .then(data => {
                    renderPermissionSections(data.permissions, data.user_permissions);
                })
                .catch(error => {
                    console.error('Error loading permissions:', error);
                    showNotification('Failed to load permissions', 'error');
                    document.getElementById('permissionsSections').innerHTML = `
                <div class="text-center py-8 text-red-600">
                    <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <p class="text-sm">Failed to load permissions. Please try again.</p>
                </div>
            `;
                });
        }

        // Enhanced permission sections rendering
        function renderPermissionSections(permissions, userPermissions) {
            const container = document.getElementById('permissionsSections');
            container.innerHTML = '';

            const sectionTitles = {
                'events': 'Event Management',
                'voting': 'Voting System',
                'user_management': 'User Management',
                'organizer_specific': 'Organizer Tools',
                'vendor_specific': 'Vendor Services',
                'system': 'System Administration',
                'general': 'General Access'
            };

            const sectionIcons = {
                'events': 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                'voting': 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                'user_management': 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z',
                'organizer_specific': 'M13 10V3L4 14h7v7l9-11h-7z',
                'vendor_specific': 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
                'system': 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z',
                'general': 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'
            };

            Object.keys(permissions).forEach(section => {
                const sectionPermissions = permissions[section];
                if (sectionPermissions.length === 0) return;

                const sectionHtml = `
                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-4 py-3 border-b border-gray-200">
                        <div class="flex items-center space-x-3">
                            <div class="bg-white p-2 rounded-lg shadow-sm">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${sectionIcons[section] || sectionIcons.general}"></path>
                                </svg>
                            </div>
                            <h4 class="text-sm font-semibold text-gray-900">${sectionTitles[section] || section}</h4>
                            <span class="bg-white px-2 py-1 rounded-full text-xs font-medium text-gray-600 border border-gray-200">
                                ${sectionPermissions.length}
                            </span>
                        </div>
                    </div>
                    <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-3 bg-white">
                        ${sectionPermissions.map(permission => `
                            <label class="flex items-center space-x-3 p-3 rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-all duration-200 cursor-pointer group">
                                <input id="perm_${permission.name}"
                                       name="permissions[]"
                                       type="checkbox"
                                       value="${permission.name}"
                                       ${userPermissions.includes(permission.name) ? 'checked' : ''}
                                       class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded transition-colors duration-200">
                                <div class="flex-1 min-w-0">
                                    <span class="block text-sm font-medium text-gray-900 capitalize group-hover:text-blue-700 transition-colors duration-200">
                                        ${permission.name.replace(/_/g, ' ')}
                                    </span>
                                    <span class="block text-xs text-gray-500 mt-1">
                                        ${permission.name}
                                    </span>
                                </div>
                            </label>
                        `).join('')}
                    </div>
                </div>
            `;

                container.innerHTML += sectionHtml;
            });

            // Add event listeners to checkboxes
            container.addEventListener('change', function(e) {
                if (e.target.name === 'permissions[]') {
                    updatePermissionsCount();
                }
            });

            // Initial count update
            updatePermissionsCount();
        }

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === rolesPermissionsModal) {
                closeRolesModal();
            }
        });

        // Enhanced notification function
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-xl shadow-lg text-white transform transition-all duration-300 ${
            type === 'success' ? 'bg-green-500' :
            type === 'error' ? 'bg-red-500' :
            'bg-blue-500'
        } animate-fade-in-up`;
            notification.innerHTML = `
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${
                        type === 'success' ? 'M5 13l4 4L19 7' :
                        type === 'error' ? 'M6 18L18 6M6 6l12 12' :
                        'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
                    }"></path>
                </svg>
                <span class="font-medium">${message}</span>
            </div>
        `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.classList.add('opacity-0', 'transform', 'scale-95');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Add CSS for animations
        const style = document.createElement('style');
        style.textContent = `
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
    `;
        document.head.appendChild(style);
    });
</script>

<script>
    let currentPage = 1;
    const usersPerPage = 10;

    function loadPage(page) {
        // Show loading state
        document.getElementById('users-table-body').innerHTML = `
        <tr>
            <td colspan="6" class="px-6 py-8 text-center">
                <div class="flex justify-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                </div>
                <p class="mt-2 text-sm text-gray-500">Loading users...</p>
            </td>
        </tr>
    `;

        // Make AJAX request
        fetch(`/admin/users?page=${page}`)
            .then(response => response.json())
            .then(data => {
                updateTable(data.users);
                updatePagination(data.pagination);
                currentPage = page;
            })
            .catch(error => {
                console.error('Error loading users:', error);
            });
    }

    function updateTable(users) {
        const tbody = document.getElementById('users-table-body');

        if (users.length === 0) {
            tbody.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No users found</h3>
                </td>
            </tr>
        `;
            return;
        }

        let html = '';
        users.forEach(user => {
            html += `
            <tr class="hover:bg-gray-50 transition-colors duration-150">
                <!-- Your user row HTML here -->
            </tr>
        `;
        });

        tbody.innerHTML = html;
    }

    function updatePagination(pagination) {
        const paginationContainer = document.getElementById('pagination-container');
        // Update pagination buttons based on pagination data
    }

    // Event listeners for buttons
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('next-btn').addEventListener('click', function() {
            if (this.classList.contains('cursor-not-allowed')) return;
            loadPage(currentPage + 1);
        });

        document.getElementById('prev-btn').addEventListener('click', function() {
            if (this.classList.contains('cursor-not-allowed')) return;
            loadPage(currentPage - 1);
        });
    });
</script>
@endsection
