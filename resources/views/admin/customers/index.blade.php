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
@section('title', 'Customers - NYAWASCO')

@section('content')
@can('view customers')
<!-- Background Image -->
<div class="fixed inset-0 -z-10">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/80 to-white/90"></div>
</div>

<div class="container mx-auto px-4 py-8 relative z-10">

    <!-- Header Section -->
    @include('components.dashboard-header', [
        'title' => 'Customer Management',
        'subtitle' => 'Manage applications and customer accounts',
        'actionButtons' => $actionButtons
    ])

    <!-- Dashboard Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Applications -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border-l-4 border-blue-500 transform hover:-translate-y-1 transition duration-300">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Applications</p>
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\WaterConnectionApplication::count() }}</p>
                </div>
            </div>
        </div>

        <!-- Pending Applications -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border-l-4 border-yellow-500 transform hover:-translate-y-1 transition duration-300">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pending Review</p>
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\WaterConnectionApplication::where('status', 'pending')->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Approved Applications -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border-l-4 border-green-500 transform hover:-translate-y-1 transition duration-300">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Approved</p>
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\WaterConnectionApplication::where('status', 'approved')->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Declined Applications -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border-l-4 border-red-500 transform hover:-translate-y-1 transition duration-300">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Declined</p>
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\WaterConnectionApplication::where('status', 'declined')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 border border-white/20">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('water-connection.apply') }}" class="bg-blue-50/80 hover:bg-blue-100 border border-blue-200 rounded-xl p-4 text-center transition duration-200 transform hover:-translate-y-1 hover:shadow-lg">
                <div class="text-blue-600 mb-2">
                    <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-blue-700">New Application</h3>
                <p class="text-sm text-gray-600 mt-1">Create new water connection request</p>
            </a>

            <a href="{{ route('admin.water-applications.index') }}" class="bg-green-50/80 hover:bg-green-100 border border-green-200 rounded-xl p-4 text-center transition duration-200 transform hover:-translate-y-1 hover:shadow-lg">
                <div class="text-green-600 mb-2">
                    <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-green-700">All Applications</h3>
                <p class="text-sm text-gray-600 mt-1">View and manage all applications</p>
            </a>

            <a href="{{ route('admin.water-applications.index') }}?status=pending" class="bg-yellow-50/80 hover:bg-yellow-100 border border-yellow-200 rounded-xl p-4 text-center transition duration-200 transform hover:-translate-y-1 hover:shadow-lg">
                <div class="text-yellow-600 mb-2">
                    <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-yellow-700">Pending Review</h3>
                <p class="text-sm text-gray-600 mt-1">Review pending applications</p>
            </a>

            <a href="{{ route('admin.water-applications.index') }}?status=approved" class="bg-purple-50/80 hover:bg-purple-100 border border-purple-200 rounded-xl p-4 text-center transition duration-200 transform hover:-translate-y-1 hover:shadow-lg">
                <div class="text-purple-600 mb-2">
                    <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-purple-700">Approved Customers</h3>
                <p class="text-sm text-gray-600 mt-1">View approved customers</p>
            </a>
        </div>
    </div>

    <!-- Active Customers Section -->
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 border border-white/20">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-green-700">Active Customers</h2>
            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                {{ $activeCustomers->total() }} customers
            </span>
        </div>

        @if($activeCustomers->count())
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meter Details</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Connection</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white/50 divide-y divide-gray-200">
                    @foreach($activeCustomers as $customer)
                    <tr class="hover:bg-green-50/50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-mono text-blue-600 font-semibold">{{ $customer->customer_number }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $customer->first_name }} {{ $customer->last_name }}</div>
                            <div class="text-sm text-gray-500">{{ $customer->plot_number }}, {{ $customer->house_number }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $customer->email }}</div>
                            <div class="text-sm text-gray-500">{{ $customer->phone }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $customer->meter_number }}</div>
                            <div class="text-sm text-gray-500 capitalize">{{ $customer->meter_type }}</div>
                            <div class="text-xs text-gray-400">Reading: {{ $customer->current_meter_reading ?? '0' }} m³</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 capitalize">{{ $customer->connection_type }}</div>
                            <div class="text-sm text-gray-500">{{ $customer->connection_date?->format('M d, Y') ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.customers.show', $customer) }}"
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition duration-200 flex items-center">
                                    View
                                </a>
                                <a href="{{ route('admin.meter-readings.create', ['customer' => $customer->id]) }}"
                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded text-sm transition duration-200 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Record Reading
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $activeCustomers->links() }}
        </div>
        @else
        <div class="text-center py-8">
            <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">
                @if($search ?? '')
                    No customers found
                @else
                    No active customers
                @endif
            </h3>
            <p class="mt-2 text-gray-500">
                @if($search ?? '')
                    No customers match your search criteria.
                @else
                    Approved applications will appear here as active customers.
                @endif
            </p>
        </div>
        @endif
    </div>

    <!-- Pending Applications Section -->
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 border border-white/20">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-blue-700">
                @if($search ?? '')
                    Search Results
                @else
                    Pending Water Connection Applications
                @endif
            </h2>
            <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-medium">
                {{ $pendingApplications->count() }}
                @if($search ?? '')
                    found
                @else
                    pending
                @endif
            </span>
        </div>

        @if($pendingApplications->count())
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ref ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applicant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Applied</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white/50 divide-y divide-gray-200">
                    @foreach($pendingApplications as $application)
                    <tr class="hover:bg-blue-50/50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-mono text-blue-600 font-semibold">#WC{{ $application->id }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 font-semibold">{{ substr($application->first_name, 0, 1) }}{{ substr($application->last_name, 0, 1) }}</span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $application->first_name }} {{ $application->last_name }}</div>
                                    <div class="text-sm text-gray-500">ID: {{ $application->national_id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $application->email }}</div>
                            <div class="text-sm text-gray-500">{{ $application->phone }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $application->plot_number }}, {{ $application->house_number }}</div>
                            <div class="text-sm text-gray-500">{{ $application->estate ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div>{{ $application->created_at->format('M d, Y') }}</div>
                            <div class="text-xs text-gray-400">{{ $application->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="/admin/water-applications/{{ $application->id }}"
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm transition duration-200 flex items-center shadow hover:shadow-md">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Review
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-8">
            <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">
                @if($search ?? '')
                    No applications found
                @else
                    No pending applications
                @endif
            </h3>
            <p class="mt-2 text-gray-500">
                @if($search ?? '')
                    No applications match your search criteria.
                @else
                    All water connection applications have been processed.
                @endif
            </p>
            @if($search ?? '')
            <a href="{{ route('admin.customers.index') }}" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition duration-200">
                Clear Search
            </a>
            @else
            <a href="{{ route('water-connection.apply') }}" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition duration-200">
                Create New Application
            </a>
            @endif
        </div>
        @endif
    </div>

    <!-- Recent Activity Section -->
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-white/20">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Recent Activity</h2>
        <div class="space-y-4">
            @php
                $recentApplications = \App\Models\WaterConnectionApplication::latest()->take(5)->get();
            @endphp

            @foreach($recentApplications as $recentApp)
            <div class="flex items-center space-x-4 p-3 border border-gray-100 rounded-lg hover:bg-gray-50/50 transition duration-150">
                <div class="flex-shrink-0">
                    @if($recentApp->status == 'approved')
                    <div class="h-8 w-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    @elseif($recentApp->status == 'declined')
                    <div class="h-8 w-8 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    @else
                    <div class="h-8 w-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">
                        {{ $recentApp->first_name }} {{ $recentApp->last_name }}
                    </p>
                    <p class="text-sm text-gray-500">
                        Application {{ ucfirst($recentApp->status) }} • {{ $recentApp->created_at->diffForHumans() }}
                    </p>
                </div>
                <div class="flex-shrink-0">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        {{ $recentApp->status == 'pending' ? 'bg-yellow-100 text-yellow-800' :
                           ($recentApp->status == 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                        {{ ucfirst($recentApp->status) }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- JavaScript for Enhanced Search -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');

    if (searchInput) {
        // Focus on search input when page loads if there's a search term
        if (searchInput.value) {
            searchInput.focus();
        }

        // Add real-time search suggestions (optional enhancement)
        searchInput.addEventListener('input', function() {
            // You can add AJAX real-time search here if needed
        });
    }

    // Add keyboard shortcut for search (Ctrl+K or Cmd+K)
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            if (searchInput) {
                searchInput.focus();
            }
        }
    });
});
</script>
@endcan
@endsection
