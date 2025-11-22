@extends('layouts.app')

@section('title', 'Meters Management - NYAWASCO')

@section('content')
@can('view meters')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    @php
    $actionButtons = [];

    if (auth()->user()->can('add meters')) {
        $actionButtons[] = [
            'text' => 'Add Meter',
            'onclick' => 'openMeterModal()',
            'icon' => 'fas fa-plus',
            'color' => 'bg-green-600 hover:bg-green-700'
        ];
    }
    @endphp

    @include('components.dashboard-header',[
        'title' => 'Meters Management',
        'subtitle' => 'Meters Management Platform',
        'actionButtons' => $actionButtons
    ])
    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-3xl font-bold text-blue-600">{{ $stats['total'] }}</div>
            <div class="text-gray-600">Total Meters</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-3xl font-bold text-green-600">{{ $stats['assigned'] }}</div>
            <div class="text-gray-600">Assigned</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-3xl font-bold text-orange-600">{{ $stats['unassigned'] }}</div>
            <div class="text-gray-600">Unassigned</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-3xl font-bold text-red-600">{{ $stats['faulty'] }}</div>
            <div class="text-gray-600">Faulty</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <a href="{{ route('admin.meters.available') }}" class="bg-orange-500 hover:bg-orange-600 text-white p-4 rounded-lg text-center transition duration-200">
            <div class="text-lg font-semibold">Unassigned Meters</div>
            <div class="text-sm">View available meters</div>
        </a>
        <a href="{{ route('admin.meters.assigned') }}" class="bg-green-500 hover:bg-green-600 text-white p-4 rounded-lg text-center transition duration-200">
            <div class="text-lg font-semibold">Assigned Meters</div>
            <div class="text-sm">View customer meters</div>
        </a>
        <a href="{{ route('admin.meters.by-location') }}" class="bg-purple-500 hover:bg-purple-600 text-white p-4 rounded-lg text-center transition duration-200">
            <div class="text-lg font-semibold">Meters by Location</div>
            <div class="text-sm">Search by address</div>
        </a>
    </div>

    <!-- Meters Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meter Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Reading</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($meters as $meter)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $meter->meter_number }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 capitalize">{{ $meter->meter_type }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($meter->customer)
                                <a href="{{ route('admin.customers.show', $meter->customer) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                    {{ $meter->customer->first_name }} {{ $meter->customer->last_name }}
                                </a>
                            @else
                                <span class="text-gray-500">Not assigned</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $meter->installation_address ?? 'N/A' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'available' => 'bg-green-100 text-green-800',
                                    'assigned' => 'bg-blue-100 text-blue-800',
                                    'faulty' => 'bg-red-100 text-red-800',
                                    'maintenance' => 'bg-yellow-100 text-yellow-800',
                                ];
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$meter->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($meter->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($meter->meterReadings->count() > 0)
                                    {{ number_format($meter->meterReadings->first()->current_reading) }} m³
                                    <div class="text-xs text-gray-500">
                                        {{ $meter->meterReadings->first()->reading_date->format('M d, Y') }}
                                        @if($meter->meterReadings->first()->consumption > 0)
                                            <br><span class="text-green-600">+{{ $meter->meterReadings->first()->consumption }} m³</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-500">No readings</span>
                                    @if($meter->initial_reading > 0)
                                        <div class="text-xs text-gray-400">Initial: {{ $meter->initial_reading }} m³</div>
                                    @endif
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.meters.show', $meter) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                            <a href="{{ route('admin.meters.edit', $meter) }}" class="text-green-600 hover:text-green-900 mr-3">Edit</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                           No meters found. <button onclick="openMeterModal()" class="text-blue-600 hover:text-blue-900 font-medium">Register the first meter</button>.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $meters->links() }}
        </div>
    </div>

    <!-- Register Meter Modal - UPDATED FOR RESPONSIVENESS -->
    <div id="registerMeterModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md mx-auto">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-xl font-bold text-blue-700">Register New Meter</h3>
                    <button type="button" onclick="closeMeterModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <form id="meterRegistrationForm" action="{{ route('admin.meters.store') }}" method="POST" class="p-4 md:p-5">
                    @csrf

                    <div class="space-y-4 max-h-96 overflow-y-auto pr-2">
                        <div>
                            <label for="modal_meter_number" class="block text-sm font-medium text-gray-700 mb-1">Meter Number *</label>
                            <input type="text" name="meter_number" id="modal_meter_number" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                                   placeholder="MTR20241215001">
                        </div>

                        <div>
                            <label for="modal_meter_type" class="block text-sm font-medium text-gray-700 mb-1">Meter Type *</label>
                            <select name="meter_type" id="modal_meter_type" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm">
                                <option value="">Select Meter Type</option>
                                <option value="domestic">Domestic - Single Phase</option>
                                <option value="commercial">Commercial - Three Phase</option>
                                <option value="industrial">Industrial - High Capacity</option>
                                <option value="institutional">Institutional - Bulk Meter</option>
                                <option value="smart">Smart Meter - Digital</option>
                                <option value="mechanical">Mechanical - Analog</option>
                            </select>
                        </div>

                        <!-- Initial Reading and Installation Date -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="modal_initial_reading" class="block text-sm font-medium text-gray-700 mb-1">Initial Reading (m³)</label>
                                <input type="number" name="initial_reading" id="modal_initial_reading" step="0.01" min="0"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                                    placeholder="0.00"
                                    value="0">
                            </div>

                            <div>
                                <label for="modal_installation_date" class="block text-sm font-medium text-gray-700 mb-1">Installation Date</label>
                                <input type="date" name="installation_date" id="modal_installation_date"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                        </div>

                        <div>
                            <label for="modal_customer_id" class="block text-sm font-medium text-gray-700 mb-1">Assign to Customer (Optional)</label>
                            <select name="customer_id" id="modal_customer_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm">
                                <option value="">Select Customer (Leave empty if not assigned)</option>
                                @foreach(App\Models\Customer::active()->get() as $customer)
                                    <option value="{{ $customer->id }}">
                                        {{ $customer->customer_number }} - {{ $customer->first_name }} {{ $customer->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="customer_installation_fields" class="hidden">
                            <div>
                                <label for="modal_installation_address" class="block text-sm font-medium text-gray-700 mb-1">Installation Address</label>
                                <input type="text" name="installation_address" id="modal_installation_address"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                                    placeholder="Customer's installation address">
                            </div>
                        </div>

                        <div>
                            <label for="modal_meter_model" class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                            <input type="text" name="meter_model" id="modal_meter_model"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                                   placeholder="e.g., K-1000, S-2000">
                        </div>

                        <div>
                            <label for="modal_notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                            <textarea name="notes" id="modal_notes" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                                      placeholder="Any additional notes..."></textarea>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-2 mt-6 pt-4 border-t">
                        <button type="button" onclick="closeMeterModal()" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm transition duration-200 order-2 sm:order-1">
                            Cancel
                        </button>
                        <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-semibold transition duration-200 order-1 sm:order-2">
                            Register Meter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function openMeterModal() {
        document.getElementById('registerMeterModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeMeterModal() {
        document.getElementById('registerMeterModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // Close modal when clicking outside
    document.getElementById('registerMeterModal').addEventListener('click', function(e) {
        if (e.target.id === 'registerMeterModal') {
            closeMeterModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeMeterModal();
        }
    });

    // Auto-generate meter number if empty
    document.getElementById('modal_meter_number').addEventListener('focus', function() {
        if (!this.value) {
            const now = new Date();
            const dateStr = now.toISOString().slice(0,10).replace(/-/g, '');
            const randomNum = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            this.value = 'MTR' + dateStr + randomNum;
        }
    });

    // Handle form submission
    document.getElementById('meterRegistrationForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Registering...';
        submitBtn.disabled = true;

        // Submit the form
        fetch(this.action, {
            method: 'POST',
            body: new FormData(this),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeMeterModal();
                location.reload(); // Refresh to show new meter
            } else {
                alert('Error: ' + (data.message || 'Failed to register meter'));
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
        })
        .finally(() => {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
    });

    // Handle customer selection
    document.getElementById('modal_customer_id').addEventListener('change', function() {
        const customerFields = document.getElementById('customer_installation_fields');
        const installationAddress = document.getElementById('modal_installation_address');

        if (this.value) {
            customerFields.classList.remove('hidden');
            // Auto-fill installation address if customer is selected
            fetch(`/admin/customers/${this.value}/address`)
                .then(response => response.json())
                .then(data => {
                    if (data.address) {
                        installationAddress.value = data.address;
                    }
                });
        } else {
            customerFields.classList.add('hidden');
            installationAddress.value = '';
        }
    });

    // Set today's date as default for installation date
    document.getElementById('modal_installation_date').value = new Date().toISOString().split('T')[0];
    </script>
</div>
@endcan
@endsection
