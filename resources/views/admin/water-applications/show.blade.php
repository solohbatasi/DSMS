@extends('layouts.app')

@section('title', 'Review Application - NYAWASCO')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-blue-700">Review Water Connection Application</h1>
            <a href="{{ route('admin.water-applications.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">
                Back to Applications
            </a>
        </div>

        <!-- Application Details -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-blue-700 mb-4">Application Details</h2>
            
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Applicant Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Applicant Information</h3>
                    <div class="space-y-2">
                        <div>
                            <label class="text-sm text-gray-500">Full Name</label>
                            <p class="font-medium">{{ $application->first_name }} {{ $application->last_name }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Email</label>
                            <p class="font-medium">{{ $application->email }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Phone</label>
                            <p class="font-medium">{{ $application->phone }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Gender</label>
                            <p class="font-medium">{{ $application->gender }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">National ID</label>
                            <p class="font-medium">{{ $application->national_id }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">KRA Pin</label>
                            <p class="font-medium">{{ $application->kra_pin }}</p>
                        </div>
                    </div>
                </div>

                <!-- Property Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Property Information</h3>
                    <div class="space-y-2">
                        <div>
                            <label class="text-sm text-gray-500">Plot Number</label>
                            <p class="font-medium">{{ $application->plot_number }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">House Number</label>
                            <p class="font-medium">{{ $application->house_number }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Estate</label>
                            <p class="font-medium">{{ $application->estate ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Expected Users</label>
                            <p class="font-medium">{{ $application->expected_users ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Property Owner</label>
                            <p class="font-medium">{{ $application->property_owner }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents Section -->
            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Attached Documents</h3>
                <div class="grid md:grid-cols-3 gap-4">
                    <!-- National ID -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-medium text-gray-700">National ID</h4>
                            @if($application->national_id_file)
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Uploaded</span>
                            @else
                                <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded">Missing</span>
                            @endif
                        </div>
                        @if($application->national_id_file)
                            <div class="flex space-x-2">
                                <a href="{{ asset('storage/' . $application->national_id_file) }}" 
                                   target="_blank"
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View
                                </a>
                                <a href="{{ asset('storage/' . $application->national_id_file) }}" 
                                   download
                                   class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    Download
                                </a>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 italic">No document uploaded</p>
                        @endif
                    </div>

                    <!-- KRA Pin -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-medium text-gray-700">KRA Pin Certificate</h4>
                            @if($application->kra_pin_file)
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Uploaded</span>
                            @else
                                <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded">Missing</span>
                            @endif
                        </div>
                        @if($application->kra_pin_file)
                            <div class="flex space-x-2">
                                <a href="{{ asset('storage/' . $application->kra_pin_file) }}" 
                                   target="_blank"
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View
                                </a>
                                <a href="{{ asset('storage/' . $application->kra_pin_file) }}" 
                                   download
                                   class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    Download
                                </a>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 italic">No document uploaded</p>
                        @endif
                    </div>

                    <!-- Title Document -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-medium text-gray-700">Title Document</h4>
                            @if($application->title_document)
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Uploaded</span>
                            @else
                                <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded">Missing</span>
                            @endif
                        </div>
                        @if($application->title_document)
                            <div class="flex space-x-2">
                                <a href="{{ asset('storage/' . $application->title_document) }}" 
                                   target="_blank"
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View
                                </a>
                                <a href="{{ asset('storage/' . $application->title_document) }}" 
                                   download
                                   class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    Download
                                </a>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 italic">No document uploaded</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Meter Details Section (For Approved Applications) -->
        @if($application->status == 'approved' && $application->customer && $application->customer->meter)
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-blue-700 mb-4">Meter Assignment Details</h2>
            
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Meter Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Meter Information</h3>
                    <div class="space-y-2">
                        <div>
                            <label class="text-sm text-gray-500">Meter Number</label>
                            <p class="font-medium text-blue-600">{{ $application->customer->meter->meter_number }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Meter Type</label>
                            <p class="font-medium capitalize">{{ $application->customer->meter->meter_type }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Connection Type</label>
                            <p class="font-medium capitalize">{{ $application->customer->meter->connection_type }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Initial Reading</label>
                            <p class="font-medium">{{ number_format($application->customer->meter->initial_reading, 2) }} m³</p>
                        </div>
                    </div>
                </div>

                <!-- Installation Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Installation Details</h3>
                    <div class="space-y-2">
                        <div>
                            <label class="text-sm text-gray-500">Connection Date</label>
                            <p class="font-medium">{{ \Carbon\Carbon::parse($application->customer->meter->connection_date)->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Installation Status</label>
                            <p class="font-medium">
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Active</span>
                            </p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Current Reading</label>
                            <p class="font-medium text-green-600">{{ number_format($application->customer->meter->current_reading, 2) }} m³</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Total Consumption</label>
                            <p class="font-medium text-blue-600">{{ number_format($application->customer->meter->current_reading - $application->customer->meter->initial_reading, 2) }} m³</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($application->customer->meter->notes)
            <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                <h4 class="font-medium text-gray-700 mb-2">Installation Notes</h4>
                <p class="text-gray-600">{{ $application->customer->meter->notes }}</p>
            </div>
            @endif
        </div>
        @endif

        <!-- Customer Details Section (For Approved Applications) -->
        @if($application->status == 'approved' && $application->customer)
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-blue-700 mb-4">Customer Account Details</h2>
            
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Account Information</h3>
                    <div class="space-y-2">
                        <div>
                            <label class="text-sm text-gray-500">Customer ID</label>
                            <p class="font-medium">{{ $application->customer->account_number }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Account Status</label>
                            <p class="font-medium">
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Active</span>
                            </p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Registration Date</label>
                            <p class="font-medium">{{ \Carbon\Carbon::parse($application->customer->created_at)->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Contact Information</h3>
                    <div class="space-y-2">
                        <div>
                            <label class="text-sm text-gray-500">Email</label>
                            <p class="font-medium">{{ $application->customer->email }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Phone</label>
                            <p class="font-medium">{{ $application->customer->phone }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Address</label>
                            <p class="font-medium">
                                {{ $application->customer->address }}<br>
                                Plot: {{ $application->plot_number }}, House: {{ $application->house_number }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Approval Actions -->
        @if($application->status == 'pending')
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold text-blue-700 mb-4">Approve Application & Assign Meter</h2>
            
            <form action="{{ route('admin.water-applications.approve', $application) }}" method="POST">
                @csrf
                
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Meter Information -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900">Meter Assignment</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Meter Number *</label>
                            <input type="text" name="meter_number" required
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500"
                                value="{{ old('meter_number', 'MTR' . date('Ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT)) }}"
                                placeholder="Enter unique meter number">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Meter Type *</label>
                            <select name="meter_type" required class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                <option value="">Select Meter Type</option>
                                <option value="domestic" {{ old('meter_type') == 'domestic' ? 'selected' : '' }}>Domestic - Single Phase</option>
                                <option value="commercial" {{ old('meter_type') == 'commercial' ? 'selected' : '' }}>Commercial - Three Phase</option>
                                <option value="industrial" {{ old('meter_type') == 'industrial' ? 'selected' : '' }}>Industrial - High Capacity</option>
                                <option value="institutional" {{ old('meter_type') == 'institutional' ? 'selected' : '' }}>Institutional - Bulk Meter</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Connection Type *</label>
                            <select name="connection_type" required class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                <option value="">Select Connection Type</option>
                                <option value="residential" {{ old('connection_type') == 'residential' ? 'selected' : '' }}>Residential</option>
                                <option value="commercial" {{ old('connection_type') == 'commercial' ? 'selected' : '' }}>Commercial</option>
                                <option value="industrial" {{ old('connection_type') == 'industrial' ? 'selected' : '' }}>Industrial</option>
                                <option value="public" {{ old('connection_type') == 'public' ? 'selected' : '' }}>Public Institution</option>
                            </select>
                        </div>
                    </div>

                    <!-- Installation Details -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900">Installation Details</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Initial Meter Reading (m³) *</label>
                            <input type="number" step="0.01" name="initial_meter_reading" required min="0"
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500"
                                value="{{ old('initial_meter_reading', 0) }}"
                                placeholder="0.00">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Connection Date *</label>
                            <input type="date" name="connection_date" required
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500"
                                value="{{ old('connection_date', date('Y-m-d')) }}">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <textarea name="notes" rows="3" 
                                    class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500"
                                    placeholder="Additional notes about this installation...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Customer Preview -->
                <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                    <h3 class="text-lg font-medium text-blue-900 mb-2">Customer Profile Preview</h3>
                    <div class="grid md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <strong>Name:</strong> {{ $application->first_name }} {{ $application->last_name }}
                        </div>
                        <div>
                            <strong>Email:</strong> {{ $application->email }}
                        </div>
                        <div>
                            <strong>Phone:</strong> {{ $application->phone }}
                        </div>
                        <div>
                            <strong>Location:</strong> {{ $application->plot_number }}, {{ $application->house_number }} {{ $application->estate ? ', ' . $application->estate : '' }}
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end space-x-4">
                    <button type="button" onclick="showDeclineForm()" 
                            class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg">
                        Decline
                    </button>
                    <button type="submit" 
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
                        Approve & Create Customer
                    </button>
                </div>
            </form>
            
            <!-- Decline Form -->
            <div id="declineForm" class="mt-6 p-4 border border-red-300 rounded-lg bg-red-50 hidden">
                <form action="{{ route('admin.water-applications.decline', $application) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-red-700 mb-2">Reason for Decline *</label>
                        <textarea name="decline_reason" required rows="4" 
                                  class="w-full border border-red-300 rounded px-3 py-2 focus:ring-2 focus:ring-red-500"
                                  placeholder="Please provide a detailed reason for declining this application...">{{ old('decline_reason') }}</textarea>
                    </div>
                    <div class="flex justify-end space-x-4">
                        <button type="button" onclick="hideDeclineForm()" 
                                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded">
                            Confirm Decline
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @else
        <!-- Application Status Display -->
        <div class="bg-white rounded-lg shadow-lg p-6 text-center">
            <h2 class="text-xl font-semibold text-gray-700 mb-2">
                This application has been <span class="{{ $application->status == 'approved' ? 'text-green-600' : 'text-red-600' }}">{{ $application->status }}</span>
            </h2>
            @if($application->status == 'declined' && $application->decline_reason)
            <div class="mt-4 p-4 bg-gray-100 rounded-lg text-left">
                <h3 class="font-semibold text-gray-800">Reason for Decline:</h3>
                <p class="text-gray-600 mt-1">{{ $application->decline_reason }}</p>
            </div>
            @endif
        </div>
        @endif
    </div>
</div>

<script>
function showDeclineForm() {
    document.getElementById('declineForm').classList.remove('hidden');
}

function hideDeclineForm() {
    document.getElementById('declineForm').classList.add('hidden');
}
</script>
@endsection