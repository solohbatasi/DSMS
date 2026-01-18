@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-blue-50 to-white py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-white">Select Branch to View</h2>
                        <p class="text-blue-100 mt-1">Choose a branch to view its data and statistics</p>
                    </div>
                    <div class="bg-white/20 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Current Selection Info -->
            @if(session('admin_selected_branch_name'))
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mx-8 mt-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <div>
                        <p class="text-sm text-blue-900">
                            Currently viewing: <span class="font-bold">{{ session('admin_selected_branch_name') }}</span>
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Branch Cards -->
            <div class="px-8 py-6">
                <!-- View All Branches Option -->
                <div class="mb-6">
                    <form action="{{ route('branch.clear') }}" method="GET">
                        <button type="submit"
                                class="w-full bg-gradient-to-r from-gray-50 to-gray-100 border-2 border-dashed border-gray-300 rounded-xl p-6 hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 group">
                            <div class="flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400 group-hover:text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                                </svg>
                                <div class="text-left">
                                    <h3 class="text-lg font-semibold text-gray-900 group-hover:text-gray-900">View All Branches</h3>
                                    <p class="text-sm text-gray-600 group-hover:text-gray-700">See consolidated data from all branches</p>
                                </div>
                            </div>
                        </button>
                    </form>
                </div>

                <!-- Individual Branches -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($branches as $branch)
                    <form action="{{ route('branch.switch') }}" method="POST">
                        @csrf
                        <input type="hidden" name="branch_id" value="{{ $branch->id }}">

                        <button type="submit"
                                class="w-full bg-white border-2 border-gray-200 rounded-xl p-6 hover:border-blue-500 hover:shadow-lg transition-all duration-200 group">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 bg-blue-100 p-3 rounded-lg group-hover:bg-blue-200 transition-colors duration-200">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-700">
                                        {{ $branch->name }}
                                    </h3>
                                    <p class="text-sm text-gray-600 mt-1">Code: {{ $branch->branch_code }}</p>
                                    <div class="mt-3 text-xs text-gray-500 space-y-1">
                                        <p class="flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            {{ $branch->students_count ?? 0 }} Students
                                        </p>
                                        <p class="flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.567-3 3.5S10.343 15 12 15s3-1.567 3-3.5S13.657 8 12 8z"></path>
                                            </svg>
                                            KES {{ number_format($branch->total_revenue ?? 0, 2) }} Revenue
                                        </p>
                                    </div>
                                </div>
                                @if(session('admin_selected_branch') == $branch->id)
                                <div class="text-green-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                @endif
                            </div>
                        </button>
                    </form>
                    @endforeach
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-8 py-4 border-t border-gray-100">
                <div class="flex items-center justify-between">
                    <a href="{{ route('dashboard') }}"
                       class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-gray-900">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Dashboard
                    </a>
                    <span class="text-xs text-gray-500">
                        {{ count($branches) }} active branches
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
