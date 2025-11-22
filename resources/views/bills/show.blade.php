@extends('layouts.app')

@section('title', $title ?? 'Bill Details - NYAWASCO')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <nav class="flex mb-4" aria-label="Breadcrumb">
                        <ol class="flex items-center space-x-4">
                            <li>
                                <a href="{{ route('bills.index') }}" class="text-gray-400 hover:text-gray-500">
                                    <i class="fas fa-arrow-left mr-1"></i>
                                    Back to Bills
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <span class="text-gray-400">/</span>
                                    <span class="ml-4 text-sm font-medium text-gray-500">Bill Details</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h1 class="text-2xl font-bold text-gray-900">Bill #{{ $bill->bill_number }}</h1>
                    <p class="text-gray-600 mt-1">Detailed view of water consumption bill</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('bills.edit', $bill) }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Bill
                    </a>
                    <button onclick="window.print()" 
                            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                        <i class="fas fa-print mr-2"></i>
                        Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Bill Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Bill Summary Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Bill Summary</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Customer Information -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Customer Information</h3>
                            <div class="space-y-2">
                                <div>
                                    <label class="text-sm text-gray-500">Customer Name</label>
                                    <p class="font-medium text-gray-900">
                                        <a href="{{ route('admin.customers.show', $bill->customer) }}" 
                                           class="text-blue-600 hover:text-blue-800">
                                            {{ $bill->customer->first_name }} {{ $bill->customer->last_name }}
                                        </a>
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-500">Customer Number</label>
                                    <p class="font-medium text-blue-600">{{ $bill->customer->customer_number }}</p>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-500">Location</label>
                                    <p class="font-medium text-gray-900">
                                        {{ $bill->customer->plot_number }}, {{ $bill->customer->house_number }}
                                        @if($bill->customer->estate)
                                            , {{ $bill->customer->estate }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Billing Period -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Billing Information</h3>
                            <div class="space-y-2">
                                <div>
                                    <label class="text-sm text-gray-500">Billing Period</label>
                                    <p class="font-medium text-gray-900">
                                        @if($bill->billing_period_start && $bill->billing_period_end)
                                            {{ $bill->billing_period_start->format('M d, Y') }} - {{ $bill->billing_period_end->format('M d, Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-500">Due Date</label>
                                    <p class="font-medium {{ $bill->is_overdue ? 'text-red-600' : 'text-gray-900' }}">
                                        {{ $bill->formatted_due_date ?? 'N/A' }}
                                        @if($bill->is_overdue)
                                            <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded ml-2">OVERDUE</span>
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-500">Bill Status</label>
                                    <p class="font-medium">
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
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Consumption & Charges Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Consumption & Charges</h2>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rate</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <!-- Base Charge -->
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                        Water Service Charge
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        1
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        KSh {{ number_format($bill->base_charge, 2) }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                        KSh {{ number_format($bill->base_charge, 2) }}
                                    </td>
                                </tr>

                                <!-- Consumption Charge -->
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                        Water Consumption
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($bill->consumption, 2) }} m³
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        KSh {{ number_format($bill->consumption_charge / max($bill->consumption, 1), 2) }}/m³
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                        KSh {{ number_format($bill->consumption_charge, 2) }}
                                    </td>
                                </tr>

                                <!-- Tax -->
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                        VAT (16%)
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        -
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        16%
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                        KSh {{ number_format($bill->tax_amount, 2) }}
                                    </td>
                                </tr>

                                <!-- Late Fee (if applicable) -->
                                @if($bill->late_fee > 0)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                        Late Payment Fee
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        1
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        KSh {{ number_format($bill->late_fee, 2) }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                        KSh {{ number_format($bill->late_fee, 2) }}
                                    </td>
                                </tr>
                                @endif

                                <!-- Total Row -->
                                <tr class="bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900" colspan="3">
                                        TOTAL AMOUNT DUE
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-blue-600">
                                        KSh {{ number_format($bill->total_amount, 2) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Payment History -->
                @if($bill->payments->count() > 0)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Payment History</h2>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($bill->payments as $payment)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                        {{ $payment->payment_date?->format('M d, Y') ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 capitalize">
                                        {{ $payment->payment_method }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ $payment->transaction_reference ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-green-600">
                                        KSh {{ number_format($payment->amount, 2) }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Completed
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column - Payment Summary & Actions -->
            <div class="space-y-6">
                <!-- Payment Summary Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Payment Summary</h2>
                    
                    <div class="space-y-4">
                        <!-- Progress Bar -->
                        <div>
                            <div class="flex justify-between text-sm text-gray-600 mb-1">
                                <span>Payment Progress</span>
                                <span>{{ number_format($paymentPercentage, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $paymentPercentage }}%"></div>
                            </div>
                        </div>

                        <!-- Amount Breakdown -->
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Amount:</span>
                                <span class="font-medium">KSh {{ number_format($bill->total_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Amount Paid:</span>
                                <span class="font-medium text-green-600">KSh {{ number_format($paidAmount, 2) }}</span>
                            </div>
                            <div class="flex justify-between border-t pt-2">
                                <span class="text-gray-800 font-semibold">Balance Due:</span>
                                <span class="font-semibold {{ $dueAmount > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    KSh {{ number_format($dueAmount, 2) }}
                                </span>
                            </div>
                        </div>

                        <!-- Payment Actions -->
                        @if($dueAmount > 0)
                        <div class="mt-4 space-y-2">
                            <a href="{{ route('payments.create', ['bill_id' => $bill->id]) }}" 
                               class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg transition duration-200 flex items-center justify-center font-medium">
                                <i class="fas fa-credit-card mr-2"></i>
                                Record Payment
                            </a>
                            <button onclick="showPaymentOptions()" 
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center justify-center text-sm">
                                <i class="fas fa-mobile-alt mr-2"></i>
                                Pay Online
                            </button>
                        </div>
                        @else
                        <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                <span class="text-green-800 font-medium">Fully Paid</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Bill Actions Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Bill Actions</h2>
                    
                    <div class="space-y-3">
                        <a href="{{ route('bills.edit', $bill) }}" 
                           class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg transition duration-200 flex items-center justify-center">
                            <i class="fas fa-edit mr-2"></i>
                            Edit Bill Details
                        </a>
                        
                        <a href="{{ route('admin.customers.show', $bill->customer) }}" 
                           class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-3 rounded-lg transition duration-200 flex items-center justify-center">
                            <i class="fas fa-user mr-2"></i>
                            View Customer Profile
                        </a>

                        <button onclick="window.print()" 
                                class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded-lg transition duration-200 flex items-center justify-center">
                            <i class="fas fa-print mr-2"></i>
                            Print Bill
                        </button>

                        <form action="{{ route('bills.destroy', $bill) }}" method="POST" class="mt-2" onsubmit="return confirm('Are you sure you want to delete this bill? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-3 rounded-lg transition duration-200 flex items-center justify-center">
                                <i class="fas fa-trash mr-2"></i>
                                Delete Bill
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Bill Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Bill Information</h2>
                    
                    <div class="space-y-3 text-sm">
                        <div>
                            <label class="text-gray-500">Created By</label>
                            <p class="font-medium text-gray-900">{{ $bill->creator->name ?? 'System' }}</p>
                        </div>
                        <div>
                            <label class="text-gray-500">Created Date</label>
                            <p class="font-medium text-gray-900">{{ $bill->created_at->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <label class="text-gray-500">Last Updated</label>
                            <p class="font-medium text-gray-900">{{ $bill->updated_at->format('M d, Y') }}</p>
                        </div>
                        @if($bill->notes)
                        <div>
                            <label class="text-gray-500">Notes</label>
                            <p class="font-medium text-gray-900">{{ $bill->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Options Modal -->
<div id="paymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Choose Payment Method</h3>
            
            <div class="space-y-3">
                <button class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg transition duration-200 flex items-center justify-center">
                    <i class="fas fa-mobile-alt mr-2"></i>
                    M-Pesa
                </button>
                
                <button class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg transition duration-200 flex items-center justify-center">
                    <i class="fas fa-credit-card mr-2"></i>
                    Credit/Debit Card
                </button>
                
                <button class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded-lg transition duration-200 flex items-center justify-center">
                    <i class="fas fa-university mr-2"></i>
                    Bank Transfer
                </button>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button onclick="closePaymentModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition duration-200">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function showPaymentOptions() {
    document.getElementById('paymentModal').classList.remove('hidden');
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('paymentModal');
    if (event.target === modal) {
        closePaymentModal();
    }
}
</script>

<style>
@media print {
    .bg-gray-50 {
        background: white !important;
    }
    .flex.justify-between.items-center,
    .bg-white.rounded-lg.shadow-sm.border.border-gray-200.p-6:last-child {
        display: none !important;
    }
    .bg-white {
        border: 1px solid #e5e7eb !important;
        box-shadow: none !important;
    }
}
</style>
@endsection