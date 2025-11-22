@extends('layouts.app')
@php
use Illuminate\Support\Facades\Auth;

// Prepare action buttons based on permissions
$actionButtons = [];

if (auth()->user()->can('view applications')) {
    $actionButtons[] = [
        'text' => 'Applications',
        'href' => route('admin.water-applications.index'),
        'icon' => 'fas fa-folder-open',
        'color' => 'bg-green-600 hover:bg-green-700'
    ];
}

if (auth()->user()->can('add customers')) {
    $actionButtons[] = [
        'text' => 'Add',
        'href' => route('water-connection.apply'),
        'icon' => 'fas fa-plus',
        'color' => 'bg-green-600 hover:bg-green-700'
    ];
}
@endphp
@section('title', 'Water Connection Applications - NYAWASCO')

@section('content')
@can('view applications')
<div class="container mx-auto px-4 py-8">
      @include('components.dashboard-header', [
        'title' => 'Water Applications',
        'subtitle' => 'Manage applications and customers',
        'actionButtons' => $actionButtons
    ])

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Total Applications</div>
            <div class="text-2xl font-bold text-blue-600">{{ $applications->total() }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Pending</div>
            <div class="text-2xl font-bold text-yellow-600">
                {{ $applications->where('status', 'pending')->count() }}
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Approved</div>
            <div class="text-2xl font-bold text-green-600">
                {{ $applications->where('status', 'approved')->count() }}
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex-1">
                <form action="{{ route('admin.water-applications.index') }}" method="GET" class="flex gap-3">
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text"
                            name="search"
                            value="{{ $search ?? '' }}"
                            placeholder="Search by name, email, phone, ID, plot number, reference ID..."
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Search
                    </button>
                    @if($search ?? '')
                    <a href="{{ route('admin.water-applications.index') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition duration-200 flex items-center">
                        Clear
                    </a>
                    @endif
                </form>
            </div>
            @if($search ?? '')
            <div class="text-sm text-gray-600 bg-blue-50 px-4 py-2 rounded-lg">
                Search results for: "<strong>{{ $search }}</strong>"
                @if($applications->count())
                - Found {{ $applications->total() }} application(s)
                @endif
            </div>
            @endif
        </div>
    </div>

    <!-- Applications Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Applicant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Property</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($applications as $application)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $application->full_name }}</div>
                            <div class="text-sm text-gray-500">ID: {{ $application->national_id }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $application->email }}</div>
                            <div class="text-sm text-gray-500">{{ $application->phone }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $application->plot_number }}, {{ $application->house_number }}</div>
                            <div class="text-sm text-gray-500">{{ $application->estate }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $application->status == 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                   ($application->status == 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($application->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $application->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.water-applications.show', $application) }}"
                               class="text-blue-600 hover:text-blue-900 mr-3">Review</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            No water connection applications found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $applications->links() }}
        </div>
    </div>
</div>
@endcan
@endsection
