@extends('layouts.app')

@section('content')
@php
    use Illuminate\Support\Facades\Auth;
@endphp

@can('view bills')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    @php
    $actionButtons = [];

    if (auth()->user()->can('add bills')) {
        $actionButtons[] = [
            'text' => 'Record Reading & Generate Bill',
            'onclick' => 'openBillModal()',
            'icon' => 'fas fa-plus',
            'color' => 'bg-green-600 hover:bg-green-700'
        ];
    }
    @endphp

    @include('components.dashboard-header', [
        'title' => 'Billings Management',
        'subtitle' => 'Financial Management Platform',
        'actionButtons' => $actionButtons
    ])

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-blue-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Customers</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $customers->total() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-file-invoice text-green-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Bills</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $bills->total() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Unpaid Bills</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $bills->where('bill_status', 'unpaid')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-purple-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Paid Bills</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $bills->where('bill_status', 'paid')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

       <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Quick Actions</h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('admin.customers.index') }}" 
                class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center hover:bg-blue-100 transition duration-200 group">
                    <i class="fas fa-users text-blue-600 text-xl mb-2 group-hover:text-blue-700"></i>
                    <p class="text-sm font-medium text-blue-700">All Customers</p>
                    <p class="text-xs text-blue-600 mt-1">{{ $customers->total() }} customers</p>
                </a>
                <a href="{{ route('admin.meter-readings.create') }}" 
                class="bg-green-50 border border-green-200 rounded-lg p-4 text-center hover:bg-green-100 transition duration-200 group">
                    <i class="fas fa-tachometer-alt text-green-600 text-xl mb-2 group-hover:text-green-700"></i>
                    <p class="text-sm font-medium text-green-700">Meter Readings</p>
                    <p class="text-xs text-green-600 mt-1">Record readings</p>
                </a>
                <a href="{{ route('admin.meters.index') }}" 
                class="bg-purple-50 border border-purple-200 rounded-lg p-4 text-center hover:bg-purple-100 transition duration-200 group">
                    <i class="fas fa-water text-purple-600 text-xl mb-2 group-hover:text-purple-700"></i>
                    <p class="text-sm font-medium text-purple-700">Meter Management</p>
                    <p class="text-xs text-purple-600 mt-1">Manage meters</p>
                </a>
                <a href="{{ route('payments.index') }}" 
                class="bg-orange-50 border border-orange-200 rounded-lg p-4 text-center hover:bg-orange-100 transition duration-200 group">
                    <i class="fas fa-credit-card text-orange-600 text-xl mb-2 group-hover:text-orange-700"></i>
                    <p class="text-sm font-medium text-orange-700">Payments</p>
                    <p class="text-xs text-orange-600 mt-1">View payments</p>
                </a>
            </div>
        </div>

        <!-- Bills Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800">Recent Bills</h2>
                    
                </div>
            </div>

            <!-- Add this after the Bills Section header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <input type="text" 
                            id="billSearch" 
                            placeholder="Search bills by customer name, bill number, meter number..."
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 pl-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                            autocomplete="off">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                        {{ $bills->total() }} bills
                    </span>
                    <div class="flex space-x-2">
                        <select id="statusFilter" onchange="filterBills()" 
                                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="all">All Status</option>
                            <option value="unpaid">Unpaid</option>
                            <option value="paid">Paid</option>
                            <option value="partial">Partial</option>
                        </select>
                        <select id="sortFilter" onchange="filterBills()"
                                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="amount_high">Amount: High to Low</option>
                            <option value="amount_low">Amount: Low to High</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Add search results container -->
            <div id="searchResults" class="hidden mb-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div id="searchResultsContent"></div>
            </div>

            <!-- Bills Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bill #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Consumption</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($bills as $bill)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-mono font-semibold text-blue-600">{{ $bill->bill_number ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $bill->customer->first_name ?? 'N/A' }} {{ $bill->customer->last_name ?? '' }}
                                </div>
                                <div class="text-sm text-gray-500">{{ $bill->customer->customer_number ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($bill->billing_period_start && $bill->billing_period_end)
                                    {{ $bill->billing_period_start->format('M d') }} - {{ $bill->billing_period_end->format('M d, Y') }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($bill->consumption, 2) }} m³
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                KSh {{ number_format($bill->total_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'paid' => 'bg-green-100 text-green-800',
                                        'unpaid' => 'bg-red-100 text-red-800',
                                        'partial' => 'bg-yellow-100 text-yellow-800'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$bill->bill_status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($bill->bill_status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($bill->due_date)
                                    <span class="{{ $bill->is_overdue ? 'text-red-600 font-semibold' : '' }}">
                                        {{ $bill->formatted_due_date }}
                                    </span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('bills.show', $bill->id) }}" 
                                       class="text-blue-600 hover:text-blue-900 px-2 py-1 rounded transition duration-200">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('bills.edit', $bill->id) }}" 
                                       class="text-green-600 hover:text-green-900 px-2 py-1 rounded transition duration-200">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('bills.destroy', $bill->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                onclick="return confirm('Are you sure you want to delete this bill?')"
                                                class="text-red-600 hover:text-red-900 px-2 py-1 rounded transition duration-200">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <i class="fas fa-file-invoice text-4xl mb-3"></i>
                                    <p class="text-lg font-medium text-gray-900">No bills found</p>
                                    <p class="text-gray-500">Start by recording a meter reading to generate bills.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($bills->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $bills->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Recording Modal (Same as your existing modal) -->
@include('bills.partials.recording-modal')

<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

// Filter bills
function filterBills() {
    const status = document.getElementById('statusFilter').value;
    const sort = document.getElementById('sortFilter').value;
    
    const params = new URLSearchParams();
    if (status !== 'all') params.append('status', status);
    if (sort !== 'newest') params.append('sort', sort);
    
    window.location.href = '{{ route('bills.index') }}?' + params.toString();
}

// Modal functionality
let currentStep = 1;
let selectedCustomer = null;
let searchTimeout = null;

function openBillModal() {
    const modal = document.getElementById('billModal');
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    resetModal();
    
    setTimeout(() => {
        document.getElementById('modalCustomerSearch').focus();
    }, 100);
}

function closeBillModal() {
    const modal = document.getElementById('billModal');
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    resetModal();
}

function resetModal() {
    currentStep = 1;
    selectedCustomer = null;
    showStep(1);
    document.getElementById('modalCustomerSearch').value = '';
    document.getElementById('modalSearchResults').classList.add('hidden');
    document.getElementById('noResultsMessage').classList.add('hidden');
    document.getElementById('selectedCustomerId').value = '';
    document.getElementById('readingForm').reset();
    
    if (searchTimeout) {
        clearTimeout(searchTimeout);
        searchTimeout = null;
    }
}

function showStep(step) {
    document.querySelectorAll('.step').forEach(s => s.classList.add('hidden'));
    document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
    
    document.getElementById(step === 1 ? 'searchStep' : 'readingStep').classList.remove('hidden');
    document.getElementById(step === 1 ? 'searchStep' : 'readingStep').classList.add('active');
    
    document.getElementById('prevBtn').classList.toggle('hidden', step === 1);
    document.getElementById('nextBtn').classList.toggle('hidden', step === 2);
    document.getElementById('submitBtn').classList.toggle('hidden', step === 1);
}

function nextStep() {
    if (currentStep === 1) {
        if (selectedCustomer) {
            currentStep = 2;
            showStep(2);
            loadCustomerInfo();
            
            // Focus on the current reading input
            setTimeout(() => {
                document.getElementById('current_reading').focus();
            }, 300);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'No Customer Selected',
                text: 'Please select a customer first',
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'OK'
            });
        }
    } else if (currentStep === 2) {
        // This would be for going to step 3 if you had more steps
      
    }
}

function previousStep() {
    if (currentStep === 2) {
        currentStep = 1;
        showStep(1);
        
        setTimeout(() => {
            document.getElementById('modalCustomerSearch').focus();
        }, 100);
    }
}

// Live search functionality
function setupLiveSearch() {
    const searchInput = document.getElementById('modalCustomerSearch');
    
    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.trim();
        
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }
        
        if (searchTerm.length === 0) {
            document.getElementById('modalSearchResults').classList.add('hidden');
            document.getElementById('noResultsMessage').classList.add('hidden');
            return;
        }
        
        const resultsList = document.getElementById('modalResultsList');
        resultsList.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-blue-500"></i> Searching...</div>';
        document.getElementById('modalSearchResults').classList.remove('hidden');
        document.getElementById('noResultsMessage').classList.add('hidden');
        
        searchTimeout = setTimeout(() => {
            performSearch(searchTerm);
        }, 300);
    });
    
    // Enter key support
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const searchTerm = e.target.value.trim();
            if (searchTerm.length > 0) {
                performSearch(searchTerm);
            }
        }
    });
}

// Perform the actual search
async function performSearch(searchTerm) {
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const resultsList = document.getElementById('modalResultsList');
        const searchResults = document.getElementById('modalSearchResults');
        const noResultsMessage = document.getElementById('noResultsMessage');
        
        // Show loading state
        resultsList.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-blue-500"></i> Searching...</div>';
        searchResults.classList.remove('hidden');
        noResultsMessage.classList.add('hidden');

        const response = await fetch(`/api/customers/search?search=${encodeURIComponent(searchTerm)}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const customers = await response.json();
        
        resultsList.innerHTML = '';
        
        if (customers.length === 0) {
            searchResults.classList.add('hidden');
            noResultsMessage.classList.remove('hidden');
        } else {
            searchResults.classList.remove('hidden');
            noResultsMessage.classList.add('hidden');
            
            customers.forEach(customer => {
                const customerDiv = document.createElement('div');
                customerDiv.className = 'border border-gray-200 rounded-lg p-3 hover:bg-white cursor-pointer transition duration-200 bg-white shadow-sm mb-2';
                customerDiv.innerHTML = `
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            ${customer.has_recent_reading ? `
                                <div class="mb-3 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                    <div class="flex items-center">
                                        <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                                        <span class="text-yellow-800 font-semibold text-sm">Recent Reading Exists</span>
                                    </div>
                                    <p class="text-yellow-700 text-xs mt-1">
                                        Reading recorded ${customer.recent_reading_info.days_ago} days ago
                                        (${new Date(customer.recent_reading_info.reading_date).toLocaleDateString()})
                                    </p>
                                </div>
                            ` : ''}
                            
                            <h6 class="font-semibold text-gray-900">${customer.first_name} ${customer.last_name}</h6>
                            <div class="grid grid-cols-2 gap-2 text-sm mt-2">
                                <div>
                                    <span class="text-gray-600">Customer #:</span>
                                    <span class="font-medium">${customer.customer_number}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Phone:</span>
                                    <span class="font-medium">${customer.phone}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Meter:</span>
                                    <span class="font-medium">${customer.meter ? customer.meter.meter_number : 'No Meter'}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Location:</span>
                                    <span class="font-medium">${customer.plot_number}, ${customer.house_number}</span>
                                </div>
                            </div>
                            ${customer.last_reading ? 
                                `<div class="mt-2 p-2 ${customer.has_recent_reading ? 'bg-yellow-50 border-yellow-200' : 'bg-green-50 border-green-200'} border rounded">
                                    <p class="text-sm ${customer.has_recent_reading ? 'text-yellow-700' : 'text-green-700'}">
                                        <strong>Last Reading:</strong> ${parseFloat(customer.last_reading.current_reading).toFixed(2)} m³ 
                                        (${new Date(customer.last_reading.reading_date).toLocaleDateString()})
                                        ${customer.has_recent_reading ? ' - <span class="font-semibold">THIS MONTH</span>' : ''}
                                    </p>
                                </div>` : 
                                '<p class="text-sm text-yellow-600 mt-2">No previous readings</p>'
                            }
                        </div>
                        <button onclick="selectCustomer(${JSON.stringify(customer).replace(/"/g, '&quot;')})" 
                                class="ml-4 ${customer.has_recent_reading ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700'} text-white px-3 py-2 rounded-lg text-sm transition duration-200 whitespace-nowrap self-center">
                            ${customer.has_recent_reading ? '<i class="fas fa-exclamation-triangle mr-1"></i>' : ''}
                            Select
                        </button>
                    </div>
                `;
                resultsList.appendChild(customerDiv);
            });
        }
        
    } catch (error) {
        console.error('Search error:', error);
        const resultsList = document.getElementById('modalResultsList');
        resultsList.innerHTML = `
            <div class="text-center py-4 text-red-500">
                <i class="fas fa-exclamation-triangle mb-2"></i>
                <p>Error searching customers. Please try again.</p>
                <p class="text-sm text-gray-500 mt-1">${error.message}</p>
            </div>
        `;
        document.getElementById('modalSearchResults').classList.remove('hidden');
        document.getElementById('noResultsMessage').classList.add('hidden');
    }
}

// function selectCustomer(customer) {
//     selectedCustomer = customer;
//     document.getElementById('selectedCustomerId').value = customer.id;
    
//     // Hide search results
//     document.getElementById('modalSearchResults').classList.add('hidden');
//     document.getElementById('noResultsMessage').classList.add('hidden');
//     document.getElementById('modalCustomerSearch').value = '';
    
//     // Show success message briefly, then auto-advance
//     Swal.fire({
//         icon: 'success',
//         title: 'Customer Selected',
//         html: `
//             <div class="text-left">
//                 <p class="font-semibold">${customer.first_name} ${customer.last_name}</p>
//                 <p class="text-sm">Customer #: ${customer.customer_number}</p>
//                 <p class="text-sm">Meter: ${customer.meter ? customer.meter.meter_number : 'No Meter'}</p>
//                 <p class="text-sm">Location: ${customer.plot_number}, ${customer.house_number}</p>
//             </div>
//         `,
//         timer: 1500,
//         showConfirmButton: false,
//         didClose: () => {
//             // Auto-advance to next step after modal closes
//             nextStep();
//         }
//     });
// }

function selectCustomer(customer) {
    // Show warning if customer has recent reading
    if (customer.has_recent_reading) {
        showRecentReadingWarning(customer);
    } else {
        proceedWithCustomerSelection(customer);
    }
}

function showRecentReadingWarning(customer) {
    const readingDate = new Date(customer.recent_reading_info.reading_date);
    const daysAgo = customer.recent_reading_info.days_ago;
    
    Swal.fire({
        icon: 'warning',
        title: 'Recent Reading Found',
        html: `
            <div class="text-left">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-exclamation-triangle text-yellow-500 text-xl mr-2"></i>
                        <h3 class="font-semibold text-yellow-800">Reading Already Recorded This Month</h3>
                    </div>
                    
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-yellow-700">Customer:</span>
                            <span class="font-semibold">${customer.first_name} ${customer.last_name}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-yellow-700">Last Reading Date:</span>
                            <span class="font-semibold">${readingDate.toLocaleDateString()}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-yellow-700">Days Since Last Reading:</span>
                            <span class="font-semibold">${daysAgo} days</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-yellow-700">Last Reading Value:</span>
                            <span class="font-semibold">${parseFloat(customer.last_reading.current_reading).toFixed(2)} m³</span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <h4 class="font-semibold text-blue-800 text-sm mb-2">Please Note:</h4>
                    <ul class="text-xs text-blue-700 space-y-1">
                        <li>• Monthly readings are typically recorded once per billing cycle</li>
                        <li>• You can still proceed if this is a correction or special reading</li>
                        <li>• Bills can be edited later in the bills management table</li>
                        <li>• Duplicate readings may create multiple bills for the same period</li>
                    </ul>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Proceed Anyway',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
        focusCancel: true,
        width: '600px'
    }).then((result) => {
        if (result.isConfirmed) {
            proceedWithCustomerSelection(customer);
        } else {
            // User cancelled - keep them on the search step
            document.getElementById('modalCustomerSearch').focus();
        }
    });
}

function proceedWithCustomerSelection(customer) {
    selectedCustomer = customer;
    document.getElementById('selectedCustomerId').value = customer.id;
    
    // Hide search results
    document.getElementById('modalSearchResults').classList.add('hidden');
    document.getElementById('noResultsMessage').classList.add('hidden');
    document.getElementById('modalCustomerSearch').value = '';
    
    // Show success message briefly, then auto-advance
    Swal.fire({
        icon: customer.has_recent_reading ? 'warning' : 'success',
        title: customer.has_recent_reading ? 'Proceeding with Recent Customer' : 'Customer Selected',
        html: `
            <div class="text-left">
                <p class="font-semibold">${customer.first_name} ${customer.last_name}</p>
                <p class="text-sm">Customer #: ${customer.customer_number}</p>
                <p class="text-sm">Meter: ${customer.meter ? customer.meter.meter_number : 'No Meter'}</p>
                <p class="text-sm">Location: ${customer.plot_number}, ${customer.house_number}</p>
                ${customer.has_recent_reading ? `
                    <div class="mt-2 bg-yellow-50 border border-yellow-200 rounded p-2">
                        <p class="text-xs text-yellow-700">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Recent reading exists (${new Date(customer.recent_reading_info.reading_date).toLocaleDateString()})
                        </p>
                    </div>
                ` : ''}
            </div>
        `,
        timer: customer.has_recent_reading ? 2500 : 1500,
        showConfirmButton: false,
        didClose: () => {
            // Auto-advance to next step after modal closes
            nextStep();
        }
    });
}

// function loadCustomerInfo() {
//     if (!selectedCustomer) return;
    
//     const customerInfo = document.getElementById('customerInfo');
//     customerInfo.innerHTML = `
//         <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
//             <div>
//                 <strong class="text-gray-700">Customer:</strong> ${selectedCustomer.first_name} ${selectedCustomer.last_name}<br>
//                 <strong class="text-gray-700">Customer #:</strong> ${selectedCustomer.customer_number}<br>
//                 <strong class="text-gray-700">Phone:</strong> ${selectedCustomer.phone}<br>
//                 <strong class="text-gray-700">Email:</strong> ${selectedCustomer.email || 'N/A'}
//             </div>
//             <div>
//                 <strong class="text-gray-700">Meter #:</strong> ${selectedCustomer.meter ? selectedCustomer.meter.meter_number : 'N/A'}<br>
//                 <strong class="text-gray-700">Meter Type:</strong> ${selectedCustomer.meter ? selectedCustomer.meter.meter_type : 'N/A'}<br>
//                 <strong class="text-gray-700">Location:</strong> ${selectedCustomer.plot_number}, ${selectedCustomer.house_number}, ${selectedCustomer.estate}<br>
//                 <strong class="text-gray-700">Last Reading:</strong> ${selectedCustomer.last_reading ? parseFloat(selectedCustomer.last_reading.current_reading).toFixed(2) + ' m³ (' + new Date(selectedCustomer.last_reading.reading_date).toLocaleDateString() + ')' : 'No previous readings'}
//             </div>
//         </div>
//     `;
    
//     const currentReadingInput = document.getElementById('current_reading');
//     if (selectedCustomer.last_reading) {
//         currentReadingInput.min = selectedCustomer.last_reading.current_reading;
//         currentReadingInput.placeholder = `Minimum: ${parseFloat(selectedCustomer.last_reading.current_reading).toFixed(2)} m³ (Last reading)`;
//     } else if (selectedCustomer.meter?.initial_reading) {
//         currentReadingInput.min = selectedCustomer.meter.initial_reading;
//         currentReadingInput.placeholder = `Minimum: ${parseFloat(selectedCustomer.meter.initial_reading).toFixed(2)} m³ (Initial reading)`;
//     } else {
//         currentReadingInput.min = 0;
//         currentReadingInput.placeholder = 'Enter current reading';
//     }
// }


function loadCustomerInfo() {
    if (!selectedCustomer) return;
    
    const customerInfo = document.getElementById('customerInfo');
    
    let warningHtml = '';
    if (selectedCustomer.has_recent_reading) {
        warningHtml = `
            <div class="mb-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                    <h4 class="font-semibold text-yellow-800">Recent Reading Alert</h4>
                </div>
                <p class="text-yellow-700 text-sm mt-1">
                    A reading was already recorded this month on 
                    ${new Date(selectedCustomer.recent_reading_info.reading_date).toLocaleDateString()} 
                    (${selectedCustomer.recent_reading_info.days_ago} days ago).
                </p>
                <p class="text-yellow-600 text-xs mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    Please ensure this is a correction or special reading.
                </p>
            </div>
        `;
    }
    
    customerInfo.innerHTML = warningHtml + `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <strong class="text-gray-700">Customer:</strong> ${selectedCustomer.first_name} ${selectedCustomer.last_name}<br>
                <strong class="text-gray-700">Customer #:</strong> ${selectedCustomer.customer_number}<br>
                <strong class="text-gray-700">Phone:</strong> ${selectedCustomer.phone}<br>
                <strong class="text-gray-700">Email:</strong> ${selectedCustomer.email || 'N/A'}
            </div>
            <div>
                <strong class="text-gray-700">Meter #:</strong> ${selectedCustomer.meter ? selectedCustomer.meter.meter_number : 'N/A'}<br>
                <strong class="text-gray-700">Meter Type:</strong> ${selectedCustomer.meter ? selectedCustomer.meter.meter_type : 'N/A'}<br>
                <strong class="text-gray-700">Location:</strong> ${selectedCustomer.plot_number}, ${selectedCustomer.house_number}, ${selectedCustomer.estate}<br>
                <strong class="text-gray-700">Last Reading:</strong> ${selectedCustomer.last_reading ? parseFloat(selectedCustomer.last_reading.current_reading).toFixed(2) + ' m³ (' + new Date(selectedCustomer.last_reading.reading_date).toLocaleDateString() + ')' : 'No previous readings'}
            </div>
        </div>
    `;
    
    const currentReadingInput = document.getElementById('current_reading');
    if (selectedCustomer.last_reading) {
        currentReadingInput.min = selectedCustomer.last_reading.current_reading;
        currentReadingInput.placeholder = `Minimum: ${parseFloat(selectedCustomer.last_reading.current_reading).toFixed(2)} m³ (Last reading)`;
    } else if (selectedCustomer.meter?.initial_reading) {
        currentReadingInput.min = selectedCustomer.meter.initial_reading;
        currentReadingInput.placeholder = `Minimum: ${parseFloat(selectedCustomer.meter.initial_reading).toFixed(2)} m³ (Initial reading)`;
    } else {
        currentReadingInput.min = 0;
        currentReadingInput.placeholder = 'Enter current reading';
    }
}

function submitReading() {
    const form = document.getElementById('readingForm');
    
    const currentReading = parseFloat(document.getElementById('current_reading').value);
    const minReading = parseFloat(document.getElementById('current_reading').min);
    
    if (currentReading < minReading) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Reading',
            text: `Current reading cannot be less than ${minReading.toFixed(2)} m³`,
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    // Show loading state
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
    submitBtn.disabled = true;
    
    // Submit form via AJAX
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
            // Show success message and redirect
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message || 'Meter reading recorded and bill generated successfully!',
                confirmButtonColor: '#10B981',
                confirmButtonText: 'OK'
            }).then((result) => {
                // Close modal and redirect to bills index
                closeBillModal();
                window.location.href = "{{ route('bills.index') }}";
            });
        } else {
            throw new Error(data.message || 'Failed to record reading');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'Failed to record meter reading. Please try again.',
            confirmButtonColor: '#DC2626',
            confirmButtonText: 'OK'
        });
    })
    .finally(() => {
        // Restore button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Close when clicking outside modal
window.addEventListener('click', function (event) {
    const modal = document.getElementById('billModal');
    if (event.target === modal) {
        closeBillModal();
    }
});

// Initialize live search when page loads
document.addEventListener('DOMContentLoaded', function() {
    setupLiveSearch();
});





// OCR Camera functionality for modal
function initializeModalCamera() {
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const preview = document.getElementById('preview');
    const startCamera = document.getElementById('startCamera');
    const capture = document.getElementById('capture');
    const retake = document.getElementById('retake');
    const cameraPreview = document.getElementById('cameraPreview');
    const imagePreview = document.getElementById('imagePreview');
    const cameraControls = document.getElementById('cameraControls');
    const captureControls = document.getElementById('captureControls');
    const fileInput = document.getElementById('fileInput');
    const meterValidation = document.getElementById('meterValidation');
    const ocrResult = document.getElementById('ocrResult');
    const currentReadingInput = document.getElementById('current_reading');
    let stream = null;

    // Start Camera
    startCamera.addEventListener('click', async function() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ 
                video: { 
                    facingMode: 'environment',
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                } 
            });
            video.srcObject = stream;
            cameraPreview.classList.remove('hidden');
            captureControls.classList.remove('hidden');
            startCamera.classList.add('hidden');
        } catch (err) {
            console.error('Error accessing camera:', err);
            showModalError('Unable to access camera. Please check permissions or use file upload.');
        }
    });

    // Capture Photo and Perform OCR
    capture.addEventListener('click', async function() {
        const context = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        // Convert to blob and create file
        canvas.toBlob(async function(blob) {
            const file = new File([blob], 'meter-reading-' + Date.now() + '.jpg', { type: 'image/jpeg' });
            
            // Create a new FileList
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;
            
            // Show preview
            preview.src = URL.createObjectURL(blob);
            imagePreview.classList.remove('hidden');
            cameraPreview.classList.add('hidden');
            captureControls.classList.add('hidden');
            startCamera.classList.remove('hidden');
            
            // Stop camera
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }

            // Show processing state
            ocrResult.innerHTML = `
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <div class="flex items-center justify-center">
                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mr-3"></div>
                        <span class="text-blue-700 font-medium">Analyzing meter photo...</span>
                    </div>
                </div>
            `;

            // Perform OCR detection
            const result = await window.OCRService.detectMeter(canvas);
            handleOCRResult(result);

        }, 'image/jpeg', 0.8);
    });

    // Handle OCR results
    function handleOCRResult(result) {
        if (result.success && result.isMeter) {
            // Valid meter detected
            meterValidation.innerHTML = `
                <span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs font-bold">
                    <i class="fas fa-check-circle mr-1"></i>Valid Meter
                </span>
            `;

            ocrResult.innerHTML = `
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-semibold text-green-800">Meter Detected Successfully!</h4>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">${result.confidence.toFixed(1)}% confidence</span>
                    </div>
                    ${result.meterNumber ? `
                    <p class="text-sm text-green-700 mb-2">
                        <strong>Meter No:</strong> ${result.meterNumber}
                    </p>
                    ` : ''}
                    ${result.reading ? `
                    <div class="bg-white rounded p-3 mb-3 border">
                        <p class="text-sm text-gray-600 mb-1">Detected Reading:</p>
                        <p class="text-2xl font-bold text-green-600">${result.reading} m³</p>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="useDetectedReading(${result.reading})" 
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 px-3 rounded text-sm font-medium transition duration-200">
                            <i class="fas fa-check mr-1"></i>Use This Reading
                        </button>
                        <button onclick="editDetectedReading(${result.reading})" 
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 rounded text-sm font-medium transition duration-200">
                            <i class="fas fa-edit mr-1"></i>Edit Reading
                        </button>
                    </div>
                    ` : `
                    <p class="text-yellow-600 text-sm">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Meter detected but reading not found. Please enter manually.
                    </p>
                    `}
                </div>
            `;
        } else {
            // Not a valid meter or error
            meterValidation.innerHTML = `
                <span class="bg-red-500 text-white px-2 py-1 rounded-full text-xs font-bold">
                    <i class="fas fa-exclamation-triangle mr-1"></i>Not a Meter
                </span>
            `;

            ocrResult.innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <h4 class="font-semibold text-red-800 mb-2">Meter Not Detected</h4>
                    <p class="text-sm text-red-700 mb-3">${result.message}</p>
                    <div class="bg-yellow-50 border border-yellow-200 rounded p-3">
                        <p class="text-sm text-yellow-700 font-medium mb-1">Tips for better detection:</p>
                        <ul class="text-xs text-yellow-600 list-disc list-inside space-y-1">
                            <li>Capture a clear photo of the water meter display</li>
                            <li>Ensure good lighting and avoid glare</li>
                            <li>Make sure meter numbers are visible and not blurry</li>
                            <li>Include the entire meter face in the photo</li>
                        </ul>
                    </div>
                    <button onclick="retakePhoto()" 
                            class="mt-3 w-full bg-red-600 hover:bg-red-700 text-white py-2 px-3 rounded text-sm font-medium transition duration-200">
                        <i class="fas fa-camera mr-1"></i>Retake Photo
                    </button>
                </div>
            `;
        }
    }

    // Global functions for OCR actions
    window.useDetectedReading = function(reading) {
        currentReadingInput.value = reading;
        showModalSuccess(`Reading ${reading} m³ has been auto-filled!`);
        
        // Auto-focus on reading input for verification
        setTimeout(() => {
            currentReadingInput.focus();
            currentReadingInput.select();
        }, 500);
    };

    window.editDetectedReading = function(reading) {
        currentReadingInput.value = reading;
        currentReadingInput.focus();
        currentReadingInput.select();
        showModalInfo('Reading filled. Please verify and edit if needed.');
    };

    window.retakePhoto = function() {
        imagePreview.classList.add('hidden');
        cameraPreview.classList.remove('hidden');
        preview.src = '';
        fileInput.value = '';
        ocrResult.innerHTML = '';
        meterValidation.innerHTML = '';
        
        // Restart camera
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
        startCamera.click();
    };

    // Retake Photo
    retake.addEventListener('click', window.retakePhoto);

    // Handle file input change
    fileInput.addEventListener('change', async function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = async function(e) {
                preview.src = e.target.result;
                imagePreview.classList.remove('hidden');
                
                // Hide camera controls when file is selected
                cameraPreview.classList.add('hidden');
                captureControls.classList.add('hidden');
                startCamera.classList.remove('hidden');

                // Show processing state
                ocrResult.innerHTML = `
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <div class="flex items-center justify-center">
                            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mr-3"></div>
                            <span class="text-blue-700 font-medium">Analyzing uploaded photo...</span>
                        </div>
                    </div>
                `;

                // Perform OCR on uploaded file
                const img = new Image();
                img.onload = async function() {
                    const tempCanvas = document.createElement('canvas');
                    const tempCtx = tempCanvas.getContext('2d');
                    tempCanvas.width = img.width;
                    tempCanvas.height = img.height;
                    tempCtx.drawImage(img, 0, 0);
                    
                    const result = await window.OCRService.detectMeter(tempCanvas);
                    handleOCRResult(result);
                };
                img.src = e.target.result;
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Helper functions for modal notifications
    function showModalSuccess(message) {
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: message,
            timer: 3000,
            showConfirmButton: false,
            position: 'top-end',
            toast: true
        });
    }

    function showModalError(message) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message,
            confirmButtonColor: '#dc2626',
            position: 'top-end',
            toast: true
        });
    }

    function showModalInfo(message) {
        Swal.fire({
            icon: 'info',
            title: 'Information',
            text: message,
            timer: 2000,
            showConfirmButton: false,
            position: 'top-end',
            toast: true
        });
    }
}

// Initialize camera when modal opens
function initializeCameraOnModalOpen() {
    setTimeout(() => {
        initializeModalCamera();
    }, 500);
}

// Update your openBillModal function to initialize camera
const originalOpenBillModal = openBillModal;
openBillModal = function() {
    originalOpenBillModal();
    initializeCameraOnModalOpen();
};


//search on bills

// Bill search functionality
function initializeBillSearch() {
    const searchInput = document.getElementById('billSearch');
    const searchResults = document.getElementById('searchResults');
    const searchResultsContent = document.getElementById('searchResultsContent');
    let searchTimeout = null;

    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.trim();
        
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }
        
        if (searchTerm.length === 0) {
            searchResults.classList.add('hidden');
            return;
        }
        
        searchResultsContent.innerHTML = `
            <div class="flex items-center justify-center py-4">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mr-3"></div>
                <span class="text-blue-700">Searching bills...</span>
            </div>
        `;
        searchResults.classList.remove('hidden');
        
        searchTimeout = setTimeout(() => {
            performBillSearch(searchTerm);
        }, 500);
    });
}

async function performBillSearch(searchTerm) {
    try {
        const response = await fetch(`/api/bills/search?search=${encodeURIComponent(searchTerm)}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error('Search failed');
        }

        const bills = await response.json();
        const searchResultsContent = document.getElementById('searchResultsContent');
        const searchResults = document.getElementById('searchResults');
        
        if (bills.length === 0) {
            searchResultsContent.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-search text-gray-400 text-2xl mb-2"></i>
                    <p class="text-gray-500">No bills found matching "${searchTerm}"</p>
                </div>
            `;
        } else {
            let html = `<div class="flex justify-between items-center mb-3">
                <h4 class="font-semibold text-blue-800">Found ${bills.length} bills</h4>
                <button onclick="clearSearch()" class="text-blue-600 hover:text-blue-800 text-sm">
                    <i class="fas fa-times mr-1"></i>Clear
                </button>
            </div>`;
            
            bills.forEach(bill => {
                html += `
                    <div class="bg-white rounded-lg p-3 mb-2 border border-blue-100 hover:border-blue-300 transition duration-200">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <a href="/admin/customers/${bill.customer_id}" 
                                class="font-semibold text-blue-600 hover:text-blue-800">
                                    ${bill.customer_name}
                                </a>
                                <div class="grid grid-cols-2 gap-2 text-sm mt-1">
                                    <div>
                                        <span class="text-gray-600">Bill #:</span>
                                        <span class="font-medium">${bill.bill_number}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Amount:</span>
                                        <span class="font-semibold">KSh ${parseFloat(bill.total_amount).toFixed(2)}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Meter:</span>
                                        <span class="font-medium">${bill.meter_number || 'N/A'}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Status:</span>
                                        <span class="font-medium ${bill.bill_status === 'paid' ? 'text-green-600' : bill.bill_status === 'unpaid' ? 'text-red-600' : 'text-yellow-600'}">
                                            ${bill.bill_status}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex space-x-2 ml-4">
                                <a href="/bills/${bill.id}" 
                                class="text-blue-600 hover:text-blue-800 px-2 py-1 rounded text-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            searchResultsContent.innerHTML = html;
        }
        
    } catch (error) {
        console.error('Search error:', error);
        document.getElementById('searchResultsContent').innerHTML = `
            <div class="text-center py-4 text-red-500">
                <i class="fas fa-exclamation-triangle mb-2"></i>
                <p>Error searching bills. Please try again.</p>
            </div>
        `;
    }
}

function clearSearch() {
    document.getElementById('billSearch').value = '';
    document.getElementById('searchResults').classList.add('hidden');
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeBillSearch();
});
</script>

<style>
.step {
    transition: all 0.3s ease;
}
.step.hidden {
    display: none;
}
.step.active {
    display: block;
}
</style>

@endsection
@endcan