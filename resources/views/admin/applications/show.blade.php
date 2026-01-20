@extends('layouts.app')

@section('content')
@can('view applications')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    @php
    $actionButtons = [
        [
            'text' => 'Back to List',
            'href' => route('admin.applications.index'),
            'icon' => 'fas fa-arrow-left',
            'color' => 'bg-gray-600 hover:bg-gray-700'
        ]
    ];

    if (auth()->user()->can('edit applications')) {
        $actionButtons[] = [
            'text' => 'Edit',
            'onclick' => 'openEditModal()',
            'icon' => 'fas fa-edit',
            'color' => 'bg-blue-600 hover:bg-blue-700'
        ];
    }

    if ($application->status == 'approved' && auth()->user()->can('add students')) {
        $actionButtons[] = [
            'text' => 'Enroll Student',
            'onclick' => 'enrollStudent()',
            'icon' => 'fas fa-user-plus',
            'color' => 'bg-green-600 hover:bg-green-700'
        ];
    }
    @endphp

    @include('components.dashboard-header', [
        'title' => 'Application Details',
        'subtitle' => 'Application #' . $application->application_number,
        'actionButtons' => $actionButtons
    ])

    <!-- Main Content -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Application Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Application Status Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Application Status</h3>
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                                    'under_review' => 'bg-orange-100 text-orange-800 border-orange-300',
                                    'approved' => 'bg-green-100 text-green-800 border-green-300',
                                    'rejected' => 'bg-red-100 text-red-800 border-red-300',
                                    'enrolled' => 'bg-purple-100 text-purple-800 border-purple-300',
                                ];
                            @endphp
                            <div class="inline-flex items-center px-4 py-2 rounded-full border {{ $statusColors[$application->status] ?? 'bg-gray-100 text-gray-800 border-gray-300' }}">
                                <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $application->status)) }}</span>
                                <span class="ml-2 text-xs">{{ $application->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>

                        <div class="relative">
                            <button onclick="toggleStatusDropdown()"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                <i class="fas fa-sync-alt mr-2"></i> Change Status
                            </button>

                            <div id="statusDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                                 <form action="{{ route('admin.applications.update-status', $application) }}" method="POST">
                                    @csrf
                                    @method('PUT') <!-- Add this line -->
                                    <div class="p-2">
                                        <select name="status" class="w-full border rounded-lg p-2 mb-2">
                                            <option value="pending" {{ $application->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="under_review" {{ $application->status == 'under_review' ? 'selected' : '' }}>Under Review</option>
                                            <option value="approved" {{ $application->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                            <option value="rejected" {{ $application->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                            <option value="enrolled" {{ $application->status == 'enrolled' ? 'selected' : '' }}>Enrolled</option>
                                        </select>
                                        <textarea name="admin_notes"
                                                placeholder="Add notes (optional)"
                                                class="w-full border rounded-lg p-2 text-sm mb-2"
                                                rows="2">{{ $application->admin_notes }}</textarea>
                                        <button type="submit"
                                                class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700">
                                            Update Status
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Student Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Student Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Full Name</label>
                            <p class="text-gray-900">{{ $application->full_name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Email Address</label>
                            <p class="text-gray-900">{{ $application->email }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Phone Number</label>
                            <p class="text-gray-900">{{ $application->phone }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">ID Number</label>
                            <p class="text-gray-900">{{ $application->id_number ?? 'Not provided' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium text-gray-500">Address</label>
                            <p class="text-gray-900">{{ $application->address ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Course Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Course Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Selected Course</label>
                            <p class="text-gray-900">{{ $application->course->course ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Branch</label>
                            <p class="text-gray-900">{{ $application->course->branch->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Preferred Start Date</label>
                            <p class="text-gray-900">{{ \Carbon\Carbon::parse($application->preferred_start_date)->format('F d, Y') }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Preferred Schedule</label>
                            <p class="text-gray-900">{{ ucfirst(str_replace('_', ' ', $application->preferred_schedule)) }}</p>
                        </div>
                        @if($application->special_requirements)
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium text-gray-500">Special Requirements</label>
                            <p class="text-gray-900">{{ $application->special_requirements }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column - Meta Information -->
            <div class="space-y-6">
                <!-- Application Meta -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Application Details</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Application Number</label>
                            <p class="text-gray-900 font-mono">{{ $application->application_number }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Submitted On</label>
                            <p class="text-gray-900">{{ $application->created_at->format('F d, Y h:i A') }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Last Updated</label>
                            <p class="text-gray-900">{{ $application->updated_at->format('F d, Y h:i A') }}</p>
                        </div>
                        @if($application->student)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Existing Student</label>
                            <p class="text-gray-900">
                                <a href="{{ route('students.show', $application->student_id) }}"
                                   class="text-blue-600 hover:text-blue-800">
                                    {{ $application->student->name }} (ID: {{ $application->student->id_no }})
                                </a>
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <button onclick="printApplication()"
                                class="w-full flex items-center justify-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                            <i class="fas fa-print mr-2"></i> Print Application
                        </button>

                        <a href="mailto:{{ $application->email }}"
                           class="block w-full text-center px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200">
                            <i class="fas fa-envelope mr-2"></i> Send Email
                        </a>

                        @if($application->phone)
                        <a href="tel:{{ $application->phone }}"
                           class="block w-full text-center px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200">
                            <i class="fas fa-phone mr-2"></i> Call Student
                        </a>
                        @endif

                        @if($application->status == 'approved' && auth()->user()->can('add students'))
                        <button onclick="enrollStudent()"
                                class="w-full flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            <i class="fas fa-user-plus mr-2"></i> Enroll as Student
                        </button>
                        @endif
                    </div>
                </div>

                <!-- Notes Section -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Admin Notes</h3>
                    @if($application->admin_notes)
                        <p class="text-gray-700">{{ $application->admin_notes }}</p>
                    @else
                        <p class="text-gray-500 italic">No notes added yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleStatusDropdown() {
    const dropdown = document.getElementById('statusDropdown');
    dropdown.classList.toggle('hidden');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('statusDropdown');
    const button = document.querySelector('[onclick="toggleStatusDropdown()"]');

    if (!dropdown.contains(event.target) && !button.contains(event.target)) {
        dropdown.classList.add('hidden');
    }
});

function printApplication() {
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Application #{{ $application->application_number }}</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 20px; margin-bottom: 30px; }
                .section { margin-bottom: 25px; }
                .section-title { font-weight: bold; font-size: 18px; margin-bottom: 10px; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
                .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
                .label { font-weight: bold; }
                .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Application #{{ $application->application_number }}</h1>
                <p>Printed on: ${new Date().toLocaleDateString()}</p>
            </div>

            <div class="section">
                <div class="section-title">Student Information</div>
                <div class="info-grid">
                    <div><span class="label">Name:</span> {{ $application->full_name }}</div>
                    <div><span class="label">Email:</span> {{ $application->email }}</div>
                    <div><span class="label">Phone:</span> {{ $application->phone }}</div>
                    <div><span class="label">ID Number:</span> {{ $application->id_number ?? 'N/A' }}</div>
                </div>
            </div>

            <div class="section">
                <div class="section-title">Course Information</div>
                <div class="info-grid">
                    <div><span class="label">Course:</span> {{ $application->course->course ?? 'N/A' }}</div>
                    <div><span class="label">Branch:</span> {{ $application->course->branch->name ?? 'N/A' }}</div>
                    <div><span class="label">Start Date:</span> {{ \Carbon\Carbon::parse($application->preferred_start_date)->format('F d, Y') }}</div>
                    <div><span class="label">Schedule:</span> {{ ucfirst(str_replace('_', ' ', $application->preferred_schedule)) }}</div>
                </div>
            </div>

            <div class="section">
                <div class="section-title">Application Details</div>
                <div class="info-grid">
                    <div><span class="label">Status:</span> {{ ucfirst(str_replace('_', ' ', $application->status)) }}</div>
                    <div><span class="label">Submitted:</span> {{ $application->created_at->format('F d, Y') }}</div>
                </div>
            </div>

            <div class="footer">
                <p>Generated by PACE Driving School Management System</p>
            </div>
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

function enrollStudent() {
    Swal.fire({
        title: 'Enroll Student',
        text: 'Enroll this applicant as a student?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Enroll',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirect to enrollment page with pre-filled data
            window.location.href = "{{ route('students.index') }}?application={{ $application->id }}";
        }
    });
}
</script>
@endpush

@endcan
@endsection
