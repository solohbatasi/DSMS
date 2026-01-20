@extends('layouts.app')

@section('content')
@php
    use Illuminate\Support\Facades\Auth;
@endphp

@can('view payments')
        <!-- Branch Context Indicator & Switcher -->
@if(auth()->user()->isAdmin())
<div class="mb-6 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 rounded-xl p-4 shadow-sm">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Current Branch Info -->
        <div class="flex items-center">
            <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-white border border-blue-200 shadow-sm mr-3">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <div>
                  <p class="text-sm font-medium text-blue-800 mb-0.5">Current Branch {{ session('admin_selected_branch_name', 'All Branches') }}
               </p>

                @if(session('admin_selected_branch_name'))
                    <p class="text-xs text-gray-500 mt-0.5">
                        Viewing data for this branch only
                    </p>
                @else
                    <p class="text-xs text-gray-500 mt-0.5">
                        Viewing aggregated data from all branches
                    </p>
                @endif
            </div>
        </div>

        <!-- Branch Switcher -->
        <div class="relative" x-data="branchSwitcher()">
            <button @click="toggleDropdown()"
                    :class="{'ring-2 ring-blue-500 ring-opacity-50': open}"
                    class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-300 rounded-xl font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition-all duration-200 shadow-sm min-w-[200px] justify-between">
                <div class="flex items-center">
                    @if(session('admin_selected_branch_name'))
                        <svg class="w-4 h-4 mr-2.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span class="truncate">{{ session('admin_selected_branch_name') }}</span>
                    @else
                        <svg class="w-4 h-4 mr-2.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <span class="text-gray-600">All Branches</span>
                    @endif
                </div>
                <svg class="w-4 h-4 ml-2 text-gray-400 transition-transform duration-200"
                     :class="{'transform rotate-180': open}"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open"
                 x-cloak
                 @click.away="open = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-72 rounded-xl shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 overflow-hidden">
                <!-- Header -->
                <div class="px-4 py-3 bg-gradient-to-r from-blue-50 to-blue-100 border-b border-blue-200">
                    <p class="text-sm font-semibold text-blue-900">Switch Branch Context</p>
                    <p class="text-xs text-blue-700 mt-0.5">Select a branch to view its data</p>
                </div>

                <div class="py-2 max-h-80 overflow-y-auto">
                    <!-- All Branches -->
                    <button @click="switchBranch('')"
                            :class="{'bg-blue-50 border-l-4 border-blue-500': !currentBranch}"
                            class="w-full text-left px-4 py-3 hover:bg-gray-50 transition-colors duration-150 border-l-4 border-transparent">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">All Branches</p>
                                <p class="text-xs text-gray-500 mt-0.5">View aggregated data</p>
                            </div>
                            @if(!session('admin_selected_branch_name'))
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            @endif
                        </div>
                    </button>

                    <!-- Divider -->
                    <div class="border-t border-gray-200 my-2"></div>

                    <!-- Branch List -->
                    @foreach($branches as $branch)
                    <button @click="switchBranch('{{ $branch->id }}')"
                            :class="{'bg-blue-50 border-l-4 border-blue-500': currentBranch == '{{ $branch->id }}'}"
                            class="w-full text-left px-4 py-3 hover:bg-gray-50 transition-colors duration-150 border-l-4 border-transparent">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $branch->name }}</p>
                                @if($branch->location)
                                <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $branch->location }}</p>
                                @endif
                            </div>
                            @if(session('admin_selected_branch_id') == $branch->id)
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            @endif
                        </div>
                    </button>
                    @endforeach
                </div>

                <!-- Footer -->
                <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                    <p class="text-xs text-gray-500">
                        Branch changes apply to data views only
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function branchSwitcher() {
    return {
        open: false,
        isLoading: false,
        currentBranch: '{{ session('admin_selected_branch_id') }}',

        toggleDropdown() {
            this.open = !this.open;
        },

        async switchBranch(branchId) {
            this.isLoading = true;
            this.open = false;

            // Get the button element
            const button = document.querySelector('[x-data] button');
            const originalContent = button.innerHTML;

            // Show loading state
            button.innerHTML = `
                <div class="flex items-center">
                    <div class="animate-spin rounded-full h-4 w-4 border-2 border-blue-600 border-t-transparent mr-2.5"></div>
                    <span class="text-blue-600">Switching...</span>
                </div>
            `;
            button.disabled = true;

            try {
                const response = await fetch('{{ route("branch.update-selected") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ branch_id: branchId })
                });

                const data = await response.json();

                if (data.success) {
                    // Show success feedback
                    button.innerHTML = `
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-green-600">Switched!</span>
                        </div>
                    `;

                    // Wait a moment before reloading for better UX
                    setTimeout(() => {
                        window.location.reload();
                    }, 600);
                } else {
                    this.showNotification(data.message || 'Failed to switch branch', 'error');
                    button.innerHTML = originalContent;
                    button.disabled = false;
                }
            } catch (error) {
                console.error('Error:', error);
                this.showNotification('Network error. Please try again.', 'error');
                button.innerHTML = originalContent;
                button.disabled = false;
            }

            this.isLoading = false;
        },

        showNotification(message, type = 'info') {
            // Use existing notification system or create a temporary one
            if (typeof toastr !== 'undefined') {
                const toastType = type === 'error' ? 'error' : type === 'success' ? 'success' : 'info';
                toastr[toastType](message);
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: type,
                    title: message,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            } else {
                // Create a simple notification
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 px-4 py-3 rounded-lg shadow-lg z-50 ${
                    type === 'error' ? 'bg-red-50 text-red-800 border border-red-200' :
                    type === 'success' ? 'bg-green-50 text-green-800 border border-green-200' :
                    'bg-blue-50 text-blue-800 border border-blue-200'
                }`;
                notification.innerHTML = `
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="${type === 'error' ? 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' :
                                  type === 'success' ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' :
                                  'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'}"
                            ></path>
                        </svg>
                        <span>${message}</span>
                    </div>
                `;
                document.body.appendChild(notification);

                // Remove after 3 seconds
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }
        }
    };
}
</script>

<style>
[x-cloak] { display: none !important; }

/* Custom scrollbar for dropdown */
.scrollbar-custom {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f7fafc;
}

.scrollbar-custom::-webkit-scrollbar {
    width: 6px;
}

.scrollbar-custom::-webkit-scrollbar-track {
    background: #f7fafc;
    border-radius: 3px;
}

.scrollbar-custom::-webkit-scrollbar-thumb {
    background-color: #cbd5e0;
    border-radius: 3px;
}
</style>
<style>
    /* Thermal Printer Styles for 80mm */
@page {
    size: 80mm auto;
    margin: 0;
}

@media print {
    body * {
        visibility: hidden;
    }

    .receipt-container, .receipt-container * {
        visibility: visible;
    }

    .receipt-container {
        position: absolute;
        left: 0;
        top: 0;
        width: 80mm;
        margin: 0;
        padding: 2mm;
        font-family: 'Monaco', 'Menlo', 'Courier New', monospace;
        font-size: 10px;
        line-height: 1.2;
        background: white;
        color: black;
    }

    /* Hide unnecessary elements in print */
    .no-print, .action-buttons, .modal, .footer, .header {
        display: none !important;
    }

    /* Thermal printer specific styling */
    .receipt-title {
        text-align: center;
        font-weight: bold;
        font-size: 11px;
        margin-bottom: 2mm;
        text-transform: uppercase;
    }

    .receipt-line {
        border-bottom: 1px dashed #000;
        margin: 2mm 0;
    }

    .receipt-detail {
        display: flex;
        justify-content: space-between;
        margin: 1mm 0;
    }

    .receipt-label {
        font-weight: bold;
    }

    .receipt-value {
        text-align: right;
    }

    .receipt-header, .receipt-footer {
        text-align: center;
        margin: 2mm 0;
    }

    .receipt-qr {
        text-align: center;
        margin: 2mm 0;
    }

    .text-center {
        text-align: center;
    }

    .text-right {
        text-align: right;
    }

    .bold {
        font-weight: bold;
    }

    .underline {
        border-bottom: 1px solid #000;
        padding-bottom: 1mm;
        margin-bottom: 1mm;
    }

    .double-line {
        border-bottom: 3px double #000;
        margin: 2mm 0;
    }
}
</style>
@endif
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    @php
    $actionButtons = [];

    if (auth()->user()->can('add payments')) {
        $actionButtons[] = [
            'text' => 'Add Payment',
            'onclick' => 'openPaymentModal()',
            'icon' => 'fas fa-plus',
            'color' => 'bg-green-600 hover:bg-green-700'
        ];
    }
    @endphp

    @include('components.dashboard-header', [
        'title' => 'Payments Management',
        'subtitle' => 'Manage all student payments and transactions',
        'actionButtons' => $actionButtons
    ])

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <!-- Total Payments -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Payments</p>
                        <p class="text-2xl font-semibold text-gray-900">KSh {{ number_format($totalPayments, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Today's Payments -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-day text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Today's Payments</p>
                        <p class="text-2xl font-semibold text-gray-900">KSh {{ number_format($todayPayments, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Pending Payments -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Pending Payments</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $pendingPayments }}</p>
                    </div>
                </div>
            </div>

            <!-- Completed Payments -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Completed Payments</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $completedPayments }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Quick Actions</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- All Payments -->
                <a href="{{ route('payments.index') }}"
                class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center hover:bg-blue-100 transition duration-200 group">
                    <i class="fas fa-list text-blue-600 text-xl mb-2 group-hover:text-blue-700"></i>
                    <p class="text-sm font-medium text-blue-700">All Payments</p>
                    <p class="text-xs text-blue-600 mt-1">View all payments</p>
                </a>

                <!-- Add Payment -->
                <a href="javascript:void(0)"
                onclick="openPaymentModal()"
                class="bg-green-50 border border-green-200 rounded-lg p-4 text-center hover:bg-green-100 transition duration-200 group">
                    <i class="fas fa-plus text-green-600 text-xl mb-2 group-hover:text-green-700"></i>
                    <p class="text-sm font-medium text-green-700">Add Payment</p>
                    <p class="text-xs text-green-600 mt-1">Record new payment</p>
                </a>

                <!-- Payment Reports -->
                <a href="#"
                class="bg-purple-50 border border-purple-200 rounded-lg p-4 text-center hover:bg-purple-100 transition duration-200 group">
                    <i class="fas fa-chart-bar text-purple-600 text-xl mb-2 group-hover:text-purple-700"></i>
                    <p class="text-sm font-medium text-purple-700">Reports</p>
                    <p class="text-xs text-purple-600 mt-1">Payment analytics</p>
                </a>

                <!-- Export Payments -->
                <a href="#"
                class="bg-orange-50 border border-orange-200 rounded-lg p-4 text-center hover:bg-orange-100 transition duration-200 group">
                    <i class="fas fa-download text-orange-600 text-xl mb-2 group-hover:text-orange-700"></i>
                    <p class="text-sm font-medium text-orange-700">Export</p>
                    <p class="text-xs text-orange-600 mt-1">Export payments data</p>
                </a>
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Recent Payments</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($recentPayments as $payment)
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition duration-200">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-gray-800">{{ $payment->efList->student->name ?? '' }}</h3>
                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Paid</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">REG No: {{ $payment->efList->ef_no }}</p>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-blue-600 font-semibold">KSh {{ number_format($payment->amount, 2) }}</span>
                        <span class="text-gray-500">{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') : 'N/A' }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Payments Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800">All Payments</h2>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 p-6">
                <!-- SEARCH BAR -->
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <input type="text"
                        id="paymentSearch"
                        placeholder="Search by student name, REG number, reference..."
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 pl-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                        autocomplete="off">

                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- RIGHT SIDE CONTROLS -->
                <div class="flex items-center space-x-4">
                    <!-- Count -->
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                        {{ $payments->total() }} payments
                    </span>

                    <!-- Filters -->
                    <div class="flex space-x-2">
                        <!-- STATUS FILTER -->
                        <select id="statusFilter" onchange="filterPayments()"
                                class="border border-gray-300 rounded-lg px-3 py-2 text-sm
                                    focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="all">All Status</option>
                            <option value="completed">Completed</option>
                            <option value="pending">Pending</option>
                            <option value="failed">Failed</option>
                        </select>

                        <!-- DATE FILTER -->
                        <select id="dateFilter" onchange="filterPayments()"
                                class="border border-gray-300 rounded-lg px-3 py-2 text-sm
                                    focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="all">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                        </select>

                        <!-- SORT FILTER -->
                        <select id="sortFilter" onchange="filterPayments()"
                                class="border border-gray-300 rounded-lg px-3 py-2 text-sm
                                    focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="amount_high">Amount: High to Low</option>
                            <option value="amount_low">Amount: Low to High</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Payments Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left">Student</th>
                            <th class="px-4 py-3 text-left">REG Number</th>
                            <th class="px-4 py-3 text-left">Course</th>
                            <th class="px-4 py-3 text-left">Reference</th>
                            <th class="px-4 py-3 text-left">Amount</th>
                            <th class="px-4 py-3 text-left">Balance</th>
                            <th class="px-4 py-3 text-left">Date</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Method</th>
                            <th class="px-4 py-3 text-left">Actions</th>
                        </tr>
                    </thead>

                    <tbody id="paymentsTableBody">
                        @foreach($payments as $payment)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-blue-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-800">{{ $payment->efList->student->name ?? ''}}</span>
                                        <p class="text-xs text-gray-500">{{ $payment->efList->student->id_no }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-mono text-sm text-gray-700">{{ $payment->efList->ef_no }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm text-gray-600">{{ $payment->efList->course->course }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-mono text-sm text-gray-700">{{ $payment->reference ?? 'N/A' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-semibold text-green-600">KSh {{ number_format($payment->amount, 2) }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-semibold {{ $payment->balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    KSh {{ number_format($payment->balance, 2) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                              {{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($payment->payment_status == 'completed')
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">Completed</span>
                                @elseif($payment->payment_status == 'pending')
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-medium">Pending</span>
                                @else
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">Failed</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $payment->payment_method ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <!-- Print Button -->
                                    <button onclick="printReceipt({{ $payment->id }})"
                                        class="text-green-600 hover:text-green-800 text-sm print-btn">
                                        <i class="fas fa-print"></i> Print
                                    </button>
                                    @can('edit payments')
                                    <button onclick="openEditModal({{ $payment->id }})"
                                        class="text-blue-600 hover:text-blue-800 text-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    @endcan
                                    @can('delete payments')
                                    <button onclick="openDeleteModal({{ $payment->id }})"
                                        class="text-red-600 hover:text-red-800 text-sm">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4 p-4">
                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Payment Modal -->
<div id="paymentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 relative max-h-[90vh] overflow-y-auto">
        <button onclick="closePaymentModal()" class="absolute top-3 right-3 text-gray-600 hover:text-black">
            <i class="fas fa-times text-xl"></i>
        </button>

        <h2 class="text-xl font-semibold text-gray-800 mb-4">Record New Payment</h2>

        <form id="paymentForm" method="POST" action="{{ route('payments.store') }}">
            @csrf

            <!-- Student Search and Selection -->
            <div class="mb-4">
                <label class="text-sm font-medium">Search Student *</label>
                <div class="relative">
                    <input type="text"
                           id="studentSearch"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 pl-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Search by student name, ID number, or REG number..."
                           autocomplete="off">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>

                <!-- Search Results -->
                <div id="studentSearchResults" class="hidden mt-2 max-h-60 overflow-y-auto border border-gray-200 rounded-lg bg-white shadow-lg z-50">
                    <div id="studentSearchResultsList" class="py-2"></div>
                </div>

                <!-- Selected Student Display -->
                <div id="selectedStudent" class="hidden mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-semibold text-gray-800" id="selectedStudentName"></h4>
                            <p class="text-sm text-gray-600">ID: <span id="selectedStudentId"></span></p>
                            <p class="text-sm text-gray-600">REG Number: <span id="selectedEfNumber"></span></p>
                            <p class="text-sm text-gray-600">Course: <span id="selectedCourse"></span></p>
                        </div>
                        <button type="button" onclick="clearSelection()" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <!-- Fee Information -->
                    <div class="mt-2 grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="font-medium">Total Fee:</span>
                            <span id="selectedTotalFee" class="font-semibold">-</span>
                        </div>
                        <div>
                            <span class="font-medium">Paid Amount:</span>
                            <span id="selectedPaidAmount" class="font-semibold">-</span>
                        </div>
                        <div>
                            <span class="font-medium">Balance:</span>
                            <span id="selectedBalance" class="font-semibold">-</span>
                        </div>
                        <div>
                            <span class="font-medium">Status:</span>
                            <span id="selectedStatus" class="font-semibold">-</span>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="ef_id" id="selectedEfId">
            </div>

            <!-- Payment Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="text-sm font-medium">Reference Number</label>
                    <input type="text" name="reference" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" placeholder="Payment reference">
                </div>

                <div>
                    <label class="text-sm font-medium">Amount (KSh) *</label>
                    <input type="number" name="amount" id="amount" step="0.01" min="0" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required onchange="calculateNewBalance()">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="text-sm font-medium">Payment Date *</label>
                    <input type="date" name="payment_date" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" value="{{ date('Y-m-d') }}" required>
                </div>

                <div>
                    <label class="text-sm font-medium">Payment Method *</label>
                    <select name="payment_method" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Select Method</option>
                        <option value="cash">Cash</option>
                        <option value="mpesa">M-Pesa</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="card">Card</option>
                        <option value="cheque">Cheque</option>
                    </select>
                </div>
            </div>

            <!-- New Balance Calculation -->
            <div id="balanceCalculation" class="hidden mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex justify-between items-center text-sm">
                    <span>Current Balance: <strong id="currentBalance">0.00</strong></span>
                    <span>Payment Amount: <strong id="paymentAmount">0.00</strong></span>
                    <span>New Balance: <strong id="newBalance">0.00</strong></span>
                </div>
            </div>

            <!-- Payment Status -->
            <div class="mb-4">
                <label class="text-sm font-medium">Payment Status *</label>
                <select name="payment_status" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
                    <option value="completed">Completed</option>
                    <option value="pending">Pending</option>
                    <option value="failed">Failed</option>
                </select>
            </div>

            <div class="mb-6">
                <label class="text-sm font-medium">Remarks</label>
                <textarea name="remarks" rows="3" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" placeholder="Additional notes about this payment..."></textarea>
            </div>

            <button type="submit" class="w-full bg-green-600 text-white p-3 rounded-lg hover:bg-green-700 transition duration-200 font-semibold">
                Record Payment
            </button>
        </form>
    </div>
</div>

<!-- Edit Payment Modal -->
<div id="editPaymentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 relative max-h-[90vh] overflow-y-auto">
        <button onclick="closeEditModal()" class="absolute top-3 right-3 text-gray-600 hover:text-black">
            <i class="fas fa-times text-xl"></i>
        </button>

        <h2 class="text-xl font-semibold text-gray-800 mb-4">Edit Payment</h2>

        <form id="editPaymentForm" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="payment_id" id="edit_payment_id">

            <!-- Payment Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="text-sm font-medium">Reference Number</label>
                    <input type="text" name="reference" id="edit_reference" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="text-sm font-medium">Amount (KSh) *</label>
                    <input type="number" name="amount" id="edit_amount" step="0.01" min="0" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="text-sm font-medium">Payment Date *</label>
                    <input type="date" name="payment_date" id="edit_payment_date" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div>
                    <label class="text-sm font-medium">Balance (KSh) *</label>
                    <input type="number" name="balance" id="edit_balance" step="0.01" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
                </div>
            </div>

            <!-- Payment Method and Status -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="text-sm font-medium">Payment Method *</label>
                    <select name="payment_method" id="edit_payment_method" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
                        <option value="cash">Cash</option>
                        <option value="mpesa">M-Pesa</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="card">Card</option>
                        <option value="cheque">Cheque</option>
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium">Payment Status *</label>
                    <select name="payment_status" id="edit_payment_status" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
                        <option value="completed">Completed</option>
                        <option value="pending">Pending</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="text-sm font-medium">Remarks</label>
                <textarea name="remarks" id="edit_remarks" rows="3" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition duration-200 font-semibold">
                Update Payment
            </button>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <h2 class="text-lg font-semibold text-gray-800 mb-3">Confirm Delete</h2>
        <p class="text-gray-600 mb-5">Are you sure you want to delete this payment record? This action cannot be undone.</p>
        <input type="hidden" id="deletePaymentId">
        <div class="flex justify-end gap-3">
            <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">
                Cancel
            </button>
            <button onclick="confirmDelete()" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                Delete
            </button>
        </div>
    </div>
</div>

<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Student Search Functions
let searchTimeout;
let currentSearchQuery = '';

// In your student search function:
function searchStudents(query) {
    if (query.length < 2) {
        hideStudentSearchResults();
        return;
    }

    // Limit query length on client side too
    if (query.length > 100) {
        const resultsList = document.getElementById('studentSearchResultsList');
        resultsList.innerHTML = `
            <div class="px-4 py-2 text-center text-red-500">
                <i class="fas fa-exclamation-triangle mr-2"></i>Search query too long
            </div>
        `;
        showStudentSearchResults();
        return;
    }

    if (query === currentSearchQuery) return;
    currentSearchQuery = query;

    // Show loading in search results
    const resultsList = document.getElementById('studentSearchResultsList');
    resultsList.innerHTML = `
        <div class="px-4 py-2 text-center text-gray-500">
            <i class="fas fa-spinner fa-spin mr-2"></i>Searching...
        </div>
    `;
    showStudentSearchResults();

    fetch(`/payments/search-students?q=${encodeURIComponent(query)}`)
        .then(response => {
            if (response.status === 429) {
                throw new Error('RATE_LIMITED');
            }
            if (!response.ok) throw new Error('Search failed');
            return response.json();
        })
        .then(data => {
            if (data.success && data.students.length > 0) {
                displayStudentSearchResults(data.students, data.has_more);
            } else {
                let message = 'No students found';
                if (data.message) {
                    message = data.message;
                }
                resultsList.innerHTML = `
                    <div class="px-4 py-2 text-center text-gray-500">
                        <i class="fas fa-search mr-2"></i>${message}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Search error:', error);
            let errorMessage = 'Search failed';
            if (error.message === 'RATE_LIMITED') {
                errorMessage = 'Too many searches. Please wait a moment.';
            }
            resultsList.innerHTML = `
                <div class="px-4 py-2 text-center text-red-500">
                    <i class="fas fa-exclamation-triangle mr-2"></i>${errorMessage}
                </div>
            `;
        });
}

function displayStudentSearchResults(students, hasMore = false) {
    const resultsList = document.getElementById('studentSearchResultsList');
    resultsList.innerHTML = '';

    students.forEach(student => {
        const balanceClass = student.balance > 0 ? 'text-red-600' : 'text-green-600';
        const statusClass = student.status === 'active' ? 'bg-green-100 text-green-800' :
                           student.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                           'bg-gray-100 text-gray-800';

        const item = document.createElement('div');
        item.className = 'px-4 py-3 border-b border-gray-100 hover:bg-blue-50 cursor-pointer transition duration-150';
        item.innerHTML = `
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="font-medium text-gray-800">${student.student_name}</div>
                    <div class="text-sm text-gray-600 mt-1">
                        <span class="font-mono">${student.id_no}</span> |
                        EF: <span class="font-mono">${student.ef_number}</span>
                    </div>
                    <div class="text-sm text-gray-600">${student.course_name}</div>
                </div>
                <div class="text-right ml-2">
                    <div class="text-xs ${balanceClass} font-semibold">
                        Balance: KSh ${student.balance}
                    </div>
                    <span class="inline-block mt-1 px-2 py-1 text-xs rounded-full ${statusClass}">
                        ${student.status}
                    </span>
                </div>
            </div>
        `;

        item.addEventListener('click', () => selectStudent(student));
        resultsList.appendChild(item);
    });

    // Show message if results are limited
    if (hasMore) {
        const limitMessage = document.createElement('div');
        limitMessage.className = 'px-4 py-2 text-center text-blue-600 bg-blue-50 text-xs';
        limitMessage.innerHTML = '<i class="fas fa-info-circle mr-1"></i> Showing first 15 results. Refine your search for more.';
        resultsList.appendChild(limitMessage);
    }
}

// In your table search function:
function filterPayments() {
    const q = document.getElementById("paymentSearch").value.trim();
    const status = document.getElementById("statusFilter").value;
    const date = document.getElementById("dateFilter").value;
    const sort = document.getElementById("sortFilter").value;

    // Client-side length limit
    if (q.length > 100) {
        const tbody = document.getElementById("paymentsTableBody");
        tbody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center py-8 text-red-500">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Search query too long
                </td>
            </tr>
        `;
        return;
    }

    // Show loading state
    const tbody = document.getElementById("paymentsTableBody");
    tbody.innerHTML = `
        <tr>
            <td colspan="10" class="text-center py-4 text-gray-500">
                <i class="fas fa-spinner fa-spin mr-2"></i>Searching...
            </td>
        </tr>
    `;

    // Build URL with parameters
    const params = new URLSearchParams();
    if (q) params.append('q', q);
    if (status !== 'all') params.append('status', status);
    if (date !== 'all') params.append('date', date);
    if (sort !== 'newest') params.append('sort', sort);

    const url = `/payments/search?${params.toString()}`;

    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            renderPayments(data.payments, data.has_more);
        } else {
            throw new Error(data.message || 'Search returned unsuccessful');
        }
    })
    .catch(error => {
        console.error('Search error:', error);
        tbody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center py-8 text-gray-500">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Search temporarily unavailable
                </td>
            </tr>
        `;
    });
}

function renderPayments(payments, hasMore = false) {
    const tbody = document.getElementById("paymentsTableBody");

    if (!tbody) {
        console.error('paymentsTableBody not found');
        return;
    }

    tbody.innerHTML = "";

    if (!payments || payments.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center py-8 text-gray-500">
                    <i class="fas fa-search mr-2"></i>No payments found matching your search criteria
                </td>
            </tr>
        `;
        return;
    }

    const canEdit = {{ auth()->user()->can('edit payments') ? 'true' : 'false' }};
    const canDelete = {{ auth()->user()->can('delete payments') ? 'true' : 'false' }};

    payments.forEach(p => {
        // ... existing payment rendering code ...
    });

    // Add limit message if needed
    if (hasMore) {
        const limitRow = document.createElement('tr');
        limitRow.innerHTML = `
            <td colspan="10" class="text-center py-3 bg-yellow-50 text-yellow-700 text-sm">
                <i class="fas fa-info-circle mr-2"></i>
                Showing first 100 results. Refine your search for more specific results.
            </td>
        `;
        tbody.appendChild(limitRow);
    }
}

function displayStudentSearchResults(students) {
    const resultsList = document.getElementById('studentSearchResultsList');
    resultsList.innerHTML = '';

    students.forEach(student => {
        const balanceClass = student.balance > 0 ? 'text-red-600' : 'text-green-600';
        const statusClass = student.status === 'active' ? 'bg-green-100 text-green-800' :
                           student.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                           'bg-gray-100 text-gray-800';

        const item = document.createElement('div');
        item.className = 'px-4 py-3 border-b border-gray-100 hover:bg-blue-50 cursor-pointer transition duration-150';
        item.innerHTML = `
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="font-medium text-gray-800">${student.student_name}</div>
                    <div class="text-sm text-gray-600 mt-1">
                        <span class="font-mono">${student.id_no}</span> |
                        EF: <span class="font-mono">${student.ef_number}</span>
                    </div>
                    <div class="text-sm text-gray-600">${student.course_name}</div>
                </div>
                <div class="text-right ml-2">
                    <div class="text-xs ${balanceClass} font-semibold">
                        Balance: KSh ${student.balance}
                    </div>
                    <span class="inline-block mt-1 px-2 py-1 text-xs rounded-full ${statusClass}">
                        ${student.status}
                    </span>
                </div>
            </div>
        `;

        item.addEventListener('click', () => selectStudent(student));
        resultsList.appendChild(item);
    });
}

function selectStudent(student) {
    // Set the hidden field
    document.getElementById('selectedEfId').value = student.ef_id;

    // Update display
    document.getElementById('selectedStudentName').textContent = student.student_name;
    document.getElementById('selectedStudentId').textContent = student.id_no;
    document.getElementById('selectedEfNumber').textContent = student.ef_number;
    document.getElementById('selectedCourse').textContent = student.course_name;
    document.getElementById('selectedTotalFee').textContent = 'KSh ' + parseFloat(student.total_fee).toFixed(2);
    document.getElementById('selectedPaidAmount').textContent = 'KSh ' + parseFloat(student.paid_amount).toFixed(2);
    document.getElementById('selectedBalance').textContent = 'KSh ' + parseFloat(student.balance).toFixed(2);
    document.getElementById('selectedStatus').textContent = student.status.charAt(0).toUpperCase() + student.status.slice(1);

    // Show selected student section
    document.getElementById('selectedStudent').classList.remove('hidden');

    // Hide search results
    hideStudentSearchResults();

    // Clear search input
    document.getElementById('studentSearch').value = '';

    // Update balance calculation if amount is already entered
    calculateNewBalance();
}

function clearSelection() {
    document.getElementById('selectedEfId').value = '';
    document.getElementById('selectedStudent').classList.add('hidden');
    document.getElementById('studentSearch').value = '';
    document.getElementById('balanceCalculation').classList.add('hidden');
}

function showStudentSearchResults() {
    document.getElementById('studentSearchResults').classList.remove('hidden');
}

function hideStudentSearchResults() {
    document.getElementById('studentSearchResults').classList.add('hidden');
    document.getElementById('studentSearchResultsList').innerHTML = '';
}

// Update the calculateNewBalance function to use selected student
function calculateNewBalance() {
    const selectedStudent = document.getElementById('selectedStudent');
    const amountInput = document.getElementById('amount');

    if (!selectedStudent.classList.contains('hidden') && amountInput.value) {
        const currentBalance = parseFloat(document.getElementById('selectedBalance').textContent.replace('KSh ', ''));
        const paymentAmount = parseFloat(amountInput.value);
        const newBalance = currentBalance - paymentAmount;

        document.getElementById('balanceCalculation').classList.remove('hidden');
        document.getElementById('currentBalance').textContent = 'KSh ' + currentBalance.toFixed(2);
        document.getElementById('paymentAmount').textContent = 'KSh ' + paymentAmount.toFixed(2);
        document.getElementById('newBalance').textContent = 'KSh ' + newBalance.toFixed(2);

        // Color code the new balance
        const newBalanceElement = document.getElementById('newBalance');
        if (newBalance > 0) {
            newBalanceElement.className = 'text-red-600';
        } else if (newBalance < 0) {
            newBalanceElement.className = 'text-orange-600';
        } else {
            newBalanceElement.className = 'text-green-600';
        }
    } else {
        document.getElementById('balanceCalculation').classList.add('hidden');
    }
}

// Event Listeners for Student Search
document.getElementById('studentSearch').addEventListener('input', function(e) {
    const query = e.target.value.trim();
    clearTimeout(searchTimeout);

    if (query.length >= 2) {
        searchTimeout = setTimeout(() => searchStudents(query), 300);
    } else {
        hideStudentSearchResults();
    }
});

// Close search results when clicking outside
document.addEventListener('click', function(e) {
    const searchContainer = document.getElementById('studentSearchResults');
    const searchInput = document.getElementById('studentSearch');

    if (!searchContainer.contains(e.target) && e.target !== searchInput) {
        hideStudentSearchResults();
    }
});

// Payment Modal Functions
function openPaymentModal() {
    document.getElementById('paymentModal').classList.remove('hidden');
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
    document.getElementById('paymentForm').reset();
    document.getElementById('selectedStudent').classList.add('hidden');
    document.getElementById('balanceCalculation').classList.add('hidden');
    hideStudentSearchResults();
}

// Edit Modal Functions
function openEditModal(paymentId) {
    fetch(`/payments/${paymentId}/edit`)
        .then(res => res.json())
        .then(data => {
            const payment = data.payment;

            document.getElementById('edit_payment_id').value = payment.id;
            document.getElementById('edit_reference').value = payment.reference || '';
            document.getElementById('edit_amount').value = payment.amount;
            document.getElementById('edit_balance').value = payment.balance;
            document.getElementById('edit_payment_date').value = payment.payment_date;
            document.getElementById('edit_payment_method').value = payment.payment_method;
            document.getElementById('edit_payment_status').value = payment.payment_status;
            document.getElementById('edit_remarks').value = payment.remarks || '';

            document.getElementById('editPaymentModal').classList.remove('hidden');
        })
        .catch(err => {
            console.error('Error loading payment:', err);
            toastr.error("Failed to load payment details");
        });
}

function closeEditModal() {
    document.getElementById('editPaymentModal').classList.add('hidden');
}

// Delete Modal Functions
function openDeleteModal(paymentId) {
    document.getElementById('deletePaymentId').value = paymentId;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Form Submissions
document.getElementById('paymentForm').addEventListener('submit', function (e) {
    e.preventDefault();

    // Validate student selection
    const selectedEfId = document.getElementById('selectedEfId').value;
    if (!selectedEfId) {
        toastr.error("Please select a student first");
        document.getElementById('studentSearch').focus();
        return;
    }

    const formData = new FormData(this);

    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(async response => {
        const data = await response.json();

        if (response.ok && data.success) {
            toastr.success(data.message || "Payment recorded successfully!");
            closePaymentModal();
            setTimeout(() => location.reload(), 1000);
        } else if (response.status === 422) {
            const errors = data.errors;
            Object.keys(errors).forEach((key) => {
                toastr.error(errors[key][0]);
            });
        } else {
            toastr.error(data.message || "An unexpected error occurred.");
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error("Something went wrong. Please try again.");
    });
});

document.getElementById('editPaymentForm').addEventListener('submit', function(e){
    e.preventDefault();

    const paymentId = document.getElementById('edit_payment_id').value;
    const formData = new FormData(this);

    fetch(`/payments/${paymentId}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-HTTP-Method-Override': 'PUT'
        }
    })
    .then(async response => {
        const data = await response.json();

        if (response.ok && data.success) {
            toastr.success(data.message || "Payment updated successfully");
            setTimeout(() => location.reload(), 1000);
        } else {
            toastr.error(data.message || "Update failed");
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error("Update failed");
    });
});

function confirmDelete() {
    const paymentId = document.getElementById('deletePaymentId').value;

    fetch(`/payments/${paymentId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(async response => {
        const data = await response.json();

        if (response.ok && data.success) {
            toastr.success("Payment deleted successfully");
            closeDeleteModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            toastr.error(data.message || "Delete failed");
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error("Server error");
    });
}

// Table Search and Filter Functions
let tableSearchTimeout;

function filterPayments() {
    const q = document.getElementById("paymentSearch").value.trim();
    const status = document.getElementById("statusFilter").value;
    const date = document.getElementById("dateFilter").value;
    const sort = document.getElementById("sortFilter").value;

    // Show loading state
    const tbody = document.getElementById("paymentsTableBody");
    tbody.innerHTML = `
        <tr>
            <td colspan="10" class="text-center py-4 text-gray-500">
                <i class="fas fa-spinner fa-spin mr-2"></i>Searching...
            </td>
        </tr>
    `;

    // Build URL with parameters
    const params = new URLSearchParams();
    if (q) params.append('q', q);
    if (status !== 'all') params.append('status', status);
    if (date !== 'all') params.append('date', date);
    if (sort !== 'newest') params.append('sort', sort);

    const url = `/payments/search?${params.toString()}`;

    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            renderPayments(data.payments);
        } else {
            throw new Error(data.message || 'Search returned unsuccessful');
        }
    })
    .catch(error => {
        console.error('Search error:', error);
        tbody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center py-8 text-gray-500">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Search temporarily unavailable
                </td>
            </tr>
        `;
    });
}

document.getElementById("paymentSearch").addEventListener("keyup", function () {
    clearTimeout(tableSearchTimeout);
    const query = this.value.trim();

    if (query === '' || query.length >= 2) {
        tableSearchTimeout = setTimeout(() => filterPayments(), 400);
    }
});

// Also trigger filter when status or sort changes
document.getElementById("statusFilter").addEventListener("change", filterPayments);
document.getElementById("dateFilter").addEventListener("change", filterPayments);
document.getElementById("sortFilter").addEventListener("change", filterPayments);

function renderPayments(payments) {
    const tbody = document.getElementById("paymentsTableBody");

    if (!tbody) {
        console.error('paymentsTableBody not found');
        return;
    }

    tbody.innerHTML = "";

    if (!payments || payments.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center py-8 text-gray-500">
                    <i class="fas fa-search mr-2"></i>No payments found matching your search criteria
                </td>
            </tr>
        `;
        return;
    }

    const canEdit = {{ auth()->user()->can('edit payments') ? 'true' : 'false' }};
    const canDelete = {{ auth()->user()->can('delete payments') ? 'true' : 'false' }};

    payments.forEach(p => {
        const statusClass = p.payment_status === 'completed' ? 'bg-green-100 text-green-800' :
                           p.payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                           'bg-red-100 text-red-800';

        const statusText = p.payment_status === 'completed' ? 'Completed' :
                          p.payment_status === 'pending' ? 'Pending' : 'Failed';

        tbody.innerHTML += `
            <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-3">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-user text-blue-600 text-sm"></i>
                        </div>
                        <div>
                            <span class="font-medium text-gray-800">${p.student_name}</span>
                            <p class="text-xs text-gray-500">${p.student_id}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3">
                    <span class="font-mono text-sm text-gray-700">${p.ef_number}</span>
                </td>
                <td class="px-4 py-3">
                    <span class="text-sm text-gray-600">${p.course_name}</span>
                </td>
                <td class="px-4 py-3">
                    <span class="font-mono text-sm text-gray-700">${p.reference || 'N/A'}</span>
                </td>
                <td class="px-4 py-3">
                    <span class="font-semibold text-green-600">KSh ${p.amount}</span>
                </td>
                <td class="px-4 py-3">
                    <span class="font-semibold ${p.balance > 0 ? 'text-red-600' : 'text-green-600'}">
                        KSh ${p.balance}
                    </span>
                </td>
                <td class="px-4 py-3 text-sm text-gray-600">
                    ${p.payment_date}
                </td>
                <td class="px-4 py-3">
                    <span class="${statusClass} px-2 py-1 rounded-full text-xs font-medium">${statusText}</span>
                </td>
                <td class="px-4 py-3 text-sm text-gray-600">
                    ${p.payment_method || 'N/A'}
                </td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        ${canEdit ? `
                            <button onclick="openEditModal(${p.id})" class="text-blue-600 hover:text-blue-800 text-sm">
                                <i class="fas fa-edit"></i> Edit
                            </button>` : ''}
                        ${canDelete ? `
                            <button onclick="openDeleteModal(${p.id})" class="text-red-600 hover:text-red-800 text-sm">
                                <i class="fas fa-trash"></i> Delete
                            </button>` : ''}
                    </div>
                </td>
            </tr>
        `;
    });
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('Payments page loaded');
});
// Print Receipt Function
async function printReceipt(paymentId) {
    try {
        // Show loading state
        const printBtn = document.querySelector(`button[onclick="printReceipt(${paymentId})"]`);
        const originalContent = printBtn.innerHTML;
        printBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        printBtn.disabled = true;

        // Fetch payment data
        const response = await fetch(`/payments/${paymentId}/receipt-data`);
        if (!response.ok) throw new Error('Failed to fetch receipt data');

        const data = await response.json();

        if (!data.success) {
            throw new Error(data.message || 'Failed to load receipt data');
        }

        // Populate receipt template
        populateReceiptTemplate(data.payment, data.student, data.course, data.branch);

        // Print the receipt
        setTimeout(() => {
            const receiptDiv = document.getElementById('receiptTemplate');
            const printWindow = window.open('', '_blank');

            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Receipt - ${data.payment.reference || 'Payment'}</title>
                    <style>
                        @page { size: 80mm auto; margin: 0; }
                        body {
                            margin: 0;
                            padding: 2mm;
                            font-family: 'Monaco', 'Menlo', 'Courier New', monospace;
                            font-size: 10px;
                            line-height: 1.2;
                            width: 80mm;
                        }
                        .receipt-title { text-align: center; font-weight: bold; font-size: 11px; margin-bottom: 2mm; text-transform: uppercase; }
                        .receipt-line { border-bottom: 1px dashed #000; margin: 2mm 0; }
                        .receipt-detail { display: flex; justify-content: space-between; margin: 1mm 0; }
                        .receipt-label { font-weight: bold; }
                        .receipt-value { text-align: right; }
                        .receipt-header, .receipt-footer { text-align: center; margin: 2mm 0; }
                        .text-center { text-align: center; }
                        .text-right { text-align: right; }
                        .bold { font-weight: bold; }
                        .underline { border-bottom: 1px solid #000; padding-bottom: 1mm; margin-bottom: 1mm; }
                        .double-line { border-bottom: 3px double #000; margin: 2mm 0; }
                        .receipt-qr { text-align: center; margin: 2mm 0; }
                    </style>
                </head>
                <body>
                    ${receiptDiv.innerHTML}
                </body>
                </html>
            `);

            printWindow.document.close();

            // Auto-print for thermal printer
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 250);

            // Restore button
            printBtn.innerHTML = originalContent;
            printBtn.disabled = false;

            // Show success message
            toastr.success('Receipt sent to printer');

        }, 100);

    } catch (error) {
        console.error('Print error:', error);
        toastr.error('Failed to print receipt: ' + error.message);

        // Restore button on error
        const printBtn = document.querySelector(`button[onclick="printReceipt(${paymentId})"]`);
        if (printBtn) {
            printBtn.innerHTML = '<i class="fas fa-print"></i> Print';
            printBtn.disabled = false;
        }
    }
}

// Populate receipt template with data
function populateReceiptTemplate(payment, student, course, branch) {
    const now = new Date();

    // Format currency
    const formatCurrency = (amount) => {
        return 'KSh ' + parseFloat(amount).toLocaleString('en-KE', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    };

    // Calculate previous balance
    const previousBalance = parseFloat(payment.balance) + parseFloat(payment.amount);

    // Populate receipt fields
    document.getElementById('receiptRefNo').textContent = payment.reference || `PAY-${payment.id}`;
    document.getElementById('receiptDate').textContent = now.toLocaleDateString('en-KE');
    document.getElementById('receiptTime').textContent = now.toLocaleTimeString('en-KE', {
        hour: '2-digit',
        minute: '2-digit'
    });
    document.getElementById('receiptStudentName').textContent = student.name;
    document.getElementById('receiptStudentId').textContent = student.id_no;
    document.getElementById('receiptEfNo').textContent = student.ef_no;
    document.getElementById('receiptCourse').textContent = course.course;
    document.getElementById('receiptBranch').textContent = branch.name;
    document.getElementById('receiptMethod').textContent = payment.payment_method.toUpperCase();
    document.getElementById('receiptReference').textContent = payment.reference || 'N/A';
    document.getElementById('receiptAmount').textContent = formatCurrency(payment.amount);
    document.getElementById('receiptOldBalance').textContent = formatCurrency(previousBalance);
    document.getElementById('receiptNewBalance').textContent = formatCurrency(payment.balance);
    document.getElementById('receiptTotalPaid').textContent = formatCurrency(student.paid_amount || 0);
    document.getElementById('receiptRemaining').textContent = formatCurrency(payment.balance);
}

// Quick Print Function (for already loaded payments)
function quickPrint(paymentData) {
    const receiptWindow = window.open('', '_blank');

    receiptWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Payment Receipt</title>
            <style>
                body {
                    margin: 0;
                    padding: 2mm;
                    font-family: 'Courier New', monospace;
                    font-size: 10px;
                    line-height: 1.2;
                    width: 80mm;
                }
                .header { text-align: center; }
                .title { font-weight: bold; font-size: 12px; }
                .line { border-bottom: 1px dashed #000; margin: 2mm 0; }
                .row { display: flex; justify-content: space-between; margin: 1mm 0; }
                .label { font-weight: bold; }
                .value { text-align: right; }
                .center { text-align: center; }
                .right { text-align: right; }
                .bold { font-weight: bold; }
                .divider { border-bottom: 2px double #000; margin: 2mm 0; }
            </style>
        </head>
        <body>
            <div class="header">
                <div class="title">PACE DRIVING SCHOOL</div>
                <div>Professional Driving Training</div>
                <div>+254 728 725 559</div>
            </div>
            <div class="line"></div>
            <div class="row">
                <div class="label">Receipt:</div>
                <div>${paymentData.reference}</div>
            </div>
            <div class="row">
                <div>Date:</div>
                <div>${new Date().toLocaleDateString()}</div>
            </div>
            <div class="divider"></div>
            <div class="row">
                <div>Student:</div>
                <div>${paymentData.studentName}</div>
            </div>
            <div class="row">
                <div>REG No:</div>
                <div>${paymentData.efNumber}</div>
            </div>
            <div class="row">
                <div>Course:</div>
                <div>${paymentData.courseName}</div>
            </div>
            <div class="divider"></div>
            <div class="row bold">
                <div>Amount Paid:</div>
                <div>KSh ${parseFloat(paymentData.amount).toFixed(2)}</div>
            </div>
            <div class="row">
                <div>Method:</div>
                <div>${paymentData.method}</div>
            </div>
            <div class="divider"></div>
            <div class="center">
                <div>*** THANK YOU ***</div>

            </div>
        </body>
        </html>
    `);

    receiptWindow.document.close();

    // Auto print after a short delay
    setTimeout(() => {
        receiptWindow.print();
        receiptWindow.close();
    }, 500);
}
</script>
<!-- Receipt Template for Thermal Printer -->
<div id="receiptTemplate" class="hidden">
    <div class="receipt-container">
        <div class="receipt-logo">
            <img src="{{ asset('img/Logo.png') }}" style="max-width: 60mm; max-height: 20mm; display: block; margin: 0 auto;">
        </div>
        <!-- School Header -->
        <div class="receipt-header">
            <div class="receipt-title">PACE DRIVING SCHOOL</div>
            <div>{{ config('app.address', 'Nairobi, Kenya') }}</div>
            <div>Tel: +254 728 725 559</div>
            <div>Email: info@pacedriving.com</div>
        </div>

        <div class="receipt-line"></div>

        <!-- Receipt Info -->
        <div class="receipt-detail">
            <div class="receipt-label">RECEIPT NO:</div>
            <div class="receipt-value" id="receiptRefNo">-</div>
        </div>

        <div class="receipt-detail">
            <div class="receipt-label">DATE:</div>
            <div class="receipt-value" id="receiptDate">-</div>
        </div>

        <div class="receipt-detail">
            <div class="receipt-label">TIME:</div>
            <div class="receipt-value" id="receiptTime">-</div>
        </div>

        <div class="double-line"></div>

        <!-- Student Details -->
        <div class="text-center bold underline">STUDENT DETAILS</div>

        <div class="receipt-detail">
            <div>Name:</div>
            <div class="text-right" id="receiptStudentName">-</div>
        </div>

        <div class="receipt-detail">
            <div>ID No:</div>
            <div class="text-right" id="receiptStudentId">-</div>
        </div>

        <div class="receipt-detail">
            <div>REG Number:</div>
            <div class="text-right" id="receiptEfNo">-</div>
        </div>

        <div class="receipt-detail">
            <div>Course:</div>
            <div class="text-right" id="receiptCourse">-</div>
        </div>

        <div class="receipt-detail">
            <div>Branch:</div>
            <div class="text-right" id="receiptBranch">-</div>
        </div>

        <div class="double-line"></div>

        <!-- Payment Details -->
        <div class="text-center bold underline">PAYMENT DETAILS</div>

        <div class="receipt-detail">
            <div>Payment Method:</div>
            <div class="text-right" id="receiptMethod">-</div>
        </div>

        <div class="receipt-detail">
            <div>Reference:</div>
            <div class="text-right" id="receiptReference">-</div>
        </div>

        <div class="receipt-line"></div>

        <!-- Amount Section -->
        <div class="receipt-detail">
            <div class="bold">AMOUNT PAID:</div>
            <div class="text-right bold" id="receiptAmount">KSh 0.00</div>
        </div>

        <div class="receipt-detail">
            <div>Total Payable:</div>
            <div class="text-right" id="receiptOldBalance">KSh 0.00</div>
        </div>

        <div class="receipt-detail">
            <div>New Balance:</div>
            <div class="text-right" id="receiptNewBalance">KSh 0.00</div>
        </div>

        <div class="double-line"></div>

        <!-- Totals -->

        <div class="receipt-detail">
            <div class="bold">TOTAL PAID:</div>
            <div class="text-right bold" id="receiptTotalPaid">KSh 0.00</div>
        </div>

        <div class="receipt-detail">
            <div class="bold">REMAINING BALANCE:</div>
            <div class="text-right bold" id="receiptRemaining">KSh 0.00</div>
        </div>

        <div class="double-line"></div>

        <!-- Footer -->
        <div class="receipt-footer">
            <div>*** THANK YOU FOR YOUR PAYMENT ***</div>
            <div>This is a computer generated receipt</div>

            <div>For queries call: +254 728 725 559</div>
        </div>

        <!-- Cut Line -->
        <div style="text-align: center; margin-top: 5mm;">
            - - - - - - - - - - - - - - - - - - - - - - - - -
        </div>
    </div>
</div>
@endsection
@endcan
