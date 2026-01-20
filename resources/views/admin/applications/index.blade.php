@extends('layouts.app')

@section('content')
@php
    use App\Models\LessonApplication;
@endphp

@can('view applications')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    @php
    $actionButtons = [];

    if (auth()->user()->can('add applications')) {
        $actionButtons[] = [
            'text' => 'Add Application',
            'onclick' => 'openAddModal()',
            'icon' => 'fas fa-plus',
            'color' => 'bg-green-600 hover:bg-green-700'
        ];
    }
    @endphp

    @include('components.dashboard-header', [
        'title' => 'Lesson Applications',
        'subtitle' => 'Manage student lesson applications',
        'actionButtons' => $actionButtons
    ])

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
            <!-- Total Applications -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $applications->total() }}</p>
                    </div>
                </div>
            </div>

            <!-- Pending -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Pending</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Under Review -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-search text-orange-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Under Review</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['under_review'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Approved -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Approved</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['approved'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Enrolled -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-graduate text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Enrolled</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ LessonApplication::enrolled()->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Quick Filters</h2>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.applications.index') }}"
                   class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition">
                    <i class="fas fa-list mr-2"></i>All Applications
                </a>
                <a href="{{ route('admin.applications.index', ['status' => 'pending']) }}"
                   class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition">
                    <i class="fas fa-clock mr-2"></i>Pending
                </a>
                <a href="{{ route('admin.applications.index', ['status' => 'under_review']) }}"
                   class="px-4 py-2 bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200 transition">
                    <i class="fas fa-search mr-2"></i>Under Review
                </a>
                <a href="{{ route('admin.applications.index', ['status' => 'approved']) }}"
                   class="px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition">
                    <i class="fas fa-check-circle mr-2"></i>Approved
                </a>
                <a href="{{ route('admin.applications.index', ['status' => 'enrolled']) }}"
                   class="px-4 py-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition">
                    <i class="fas fa-user-graduate mr-2"></i>Enrolled
                </a>
            </div>
        </div>

        <!-- Applications Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800">All Applications</h2>

                    <!-- Search and Filters -->
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <input type="text"
                                   id="applicationSearch"
                                   placeholder="Search applications..."
                                   class="border border-gray-300 rounded-lg px-4 py-2 pl-10 focus:ring-2 focus:ring-blue-500">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>

                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                            {{ $applications->total() }} applications
                        </span>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Application #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applied On</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($applications as $application)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $application->application_number }}</div>
                                <div class="text-xs text-gray-500">Ref: APP-{{ str_pad($application->id, 5, '0', STR_PAD_LEFT) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <i class="fas fa-user text-blue-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $application->full_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $application->email }}</div>
                                        <div class="text-xs text-gray-500">{{ $application->phone }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $application->course->course ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ $application->course->branch->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($application->preferred_start_date)->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ ucfirst(str_replace('_', ' ', $application->preferred_schedule)) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $application->created_at->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $application->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'under_review' => 'bg-orange-100 text-orange-800',
                                        'approved' => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                        'enrolled' => 'bg-purple-100 text-purple-800',
                                    ];
                                @endphp
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$application->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.applications.show', $application) }}"
                                       class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye mr-1"></i> View
                                    </a>

                                    @can('edit applications')
                                    <a href="{{ route('admin.applications.edit', $application) }}"
                                       class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </a>
                                    @endcan

                                    @can('delete applications')
                                    <button onclick="confirmDelete({{ $application->id }})"
                                            class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash mr-1"></i> Delete
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $applications->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <h2 class="text-lg font-semibold text-gray-800 mb-3">Confirm Delete</h2>
        <p class="text-gray-600 mb-5">Are you sure you want to delete this application? This action cannot be undone.</p>
        <input type="hidden" id="deleteApplicationId">
        <div class="flex justify-end gap-3">
            <button onclick="closeDeleteModal()"
                    class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">
                Cancel
            </button>
            <button onclick="deleteApplication()"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                Delete
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(applicationId) {
    document.getElementById('deleteApplicationId').value = applicationId;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

function deleteApplication() {
    const applicationId = document.getElementById('deleteApplicationId').value;

    fetch(`/admin/applications/${applicationId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(async response => {
        const data = await response.json();

        if (response.ok && data.success) {
            closeDeleteModal();
            toastr.success(data.message || 'Application deleted successfully');
            setTimeout(() => location.reload(), 1000);
        } else {
            toastr.error(data.message || 'Failed to delete application');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('Network error. Please try again.');
    });
}

// Search functionality
let searchTimeout;
document.getElementById('applicationSearch').addEventListener('keyup', function(e) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        const query = e.target.value;
        if (query.length >= 2 || query.length === 0) {
            window.location.href = '{{ route("admin.applications.index") }}?search=' + encodeURIComponent(query);
        }
    }, 500);
});
</script>
@endpush

@endcan
@endsection
