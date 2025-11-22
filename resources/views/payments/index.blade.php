@extends('layouts.app')
@section('content')
@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Auth;
@endphp
@can('view payments')

<div class="min-h-screen bg-gray-50">

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
        'subtitle' => 'Financial Management Platform',
        'actionButtons' => $actionButtons
    ])

    <!-- Filters and Search Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search -->
                <div class="md:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Payments</label>
                    <div class="relative rounded-md shadow-sm">
                        <input type="text" name="search" id="search" placeholder="Search by payment number, customer..."
                               class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-4 pr-12 py-3 text-sm border-gray-300 rounded-md">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="status" name="status" class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-3 pr-10 py-3 text-sm border-gray-300 rounded-md">
                        <option value="all">All</option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Payments Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-blue-50 border-b border-blue-200">
                        <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Payment #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Bill #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Payment Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-blue-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-blue-100">
                    @forelse ($payments as $payment)
                    <tr class="hover:bg-blue-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $payment->payment_no }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $payment->user?->name ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $payment->user?->email ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $payment->bill?->bill_number ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">KSh {{ number_format($payment->amount, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucfirst($payment->payment_method) ?? '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'completed' => 'bg-green-100 text-green-800',
                                    'pending' => 'bg-blue-100 text-blue-800',
                                    'failed' => 'bg-red-100 text-red-800'
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$payment->payment_status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($payment->payment_status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <a href="{{ route('payments.show', $payment->id) }}" class="inline-flex items-center px-3 py-1.5 border border-blue-300 rounded-lg text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                                    <i class="fas fa-eye mr-1"></i> View
                                </a>
                                <a href="{{ route('payments.edit', $payment->id) }}" class="inline-flex items-center px-3 py-1.5 border border-blue-300 rounded-lg text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </a>
                                <form action="{{ route('payments.destroy', $payment->id) }}" method="POST" onsubmit="return confirm('Delete this payment?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-lg text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 transition-colors duration-200">
                                        <i class="fas fa-trash-alt mr-1"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-sm text-gray-500">No payments found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-blue-100 bg-blue-50">
            {{ $payments->links() }}
        </div>
    </div>
</div>

<!-- Modal -->
<div id="paymentModal" class="hidden fixed inset-0 z-60 bg-gray-900 bg-opacity-50 flex items-end justify-center overflow-y-auto">
    <div class="flex items-end justify-center w-full min-h-screen px-4 pb-8">
        <!-- Modal Content -->
        <div class="relative w-full max-w-2xl bg-white rounded-t-lg shadow-lg flex flex-col overflow-hidden transform translate-y-full transition-transform duration-300" id="paymentModalContent">

            <!-- Header -->
            <div class="flex justify-between items-center border-b px-6 py-4 sticky top-0 bg-white z-20">
                <h3 class="text-lg font-semibold text-gray-800">Create New Payment</h3>
                <button onclick="closePaymentModal()" class="text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>
            </div>

            <!-- Body -->
            <div class="px-6 py-4 overflow-y-auto" style="max-height: calc(100vh - 8rem);">
                <form action="{{ route('payments.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <!-- Search by Meter Number -->
                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700">Meter Number</label>
                            <input type="text" name="meter_no" id="meter_no" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Enter Meter Number">
                            <p id="meter_error" class="text-red-600 text-sm mt-1 hidden"></p>
                        </div>

                        <!-- Meter Model -->
                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700">Meter Model</label>
                            <input type="text" id="meter_model" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100" readonly>
                        </div>

                        <!-- Meter Type -->
                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700">Meter Type</label>
                            <input type="text" id="meter_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100" readonly>
                        </div>

                        <!-- Auto-filled Customer Name -->
                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700">Customer</label>
                            <input type="text" id="customer_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100" readonly>
                        </div>

                    </div>
                    <!-- Unpaid Bills Table -->
                    <h4 class="mt-6 font-semibold text-gray-700">Unpaid Bills</h4>
                    <table id="unpaid-bills-table" class="table-auto w-full mt-2 border">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-2 py-1">Bill No</th>
                                <th class="border px-2 py-1">Total</th>
                                <th class="border px-2 py-1">Due</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <br>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <!-- Total Unpaid Amount -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Total Due Amount</label>
                            <input type="text" id="due_amount" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100" readonly>
                        </div>

                        <!-- Amount to Pay -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Amount to Pay</label>
                            <input type="number" name="amount" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                         <!-- Payment Method -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                            <select name="payment_method" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option>-*-select-*-</option>
                                <option value="mpesa">MPESA</option>
                                <option value="bank">Bank</option>
                            </select>
                        </div>

                        <!-- Transaction Reference -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Transaction Reference</label>
                            <input type="text" name="transaction_reference" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>


                    </div>



                    <!-- Footer -->
                    <div class="mt-6 flex justify-end space-x-2 border-t pt-4 sticky bottom-0 bg-white z-10">
                        <button type="button" onclick="closePaymentModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Save Payment</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<!-- jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
/* Modal Open/Close */
function openPaymentModal() {
    const modal = document.getElementById('paymentModal');
    const modalContent = document.getElementById('paymentModalContent');

    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');

    setTimeout(() => modalContent.classList.remove('translate-y-full'), 10);
}

function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    const modalContent = document.getElementById('paymentModalContent');

    modalContent.classList.add('translate-y-full');

    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }, 300);
}

window.addEventListener('click', function (event) {
    const modal = document.getElementById('paymentModal');
    if (event.target === modal) closePaymentModal();
});


$(document).on('keyup', '#meter_no', function () {
    let meter = $(this).val().trim();
    if (meter.length < 3) return; // only search when at least 3 chars typed

    $.ajax({
        url: `/bills/info/meter/${meter}`,
        method: 'GET',
        success: function (response) {
            // --- Clear error message ---
            $("#meter_error").addClass("hidden").text("");
            // Customer Info
            $('#customer_name').val(response.customer.name);

            // Meter Info
            $('#meter_model').val(response.meter.model);
            $('#meter_type').val(response.meter.type);

            // Total Due
            $('#due_amount').val(response.total_due);

            // Bills
            let table = $('#unpaid-bills-table tbody');
            table.empty();

            response.unpaid_bills.forEach(bill => {
                table.append(`
                    <tr>
                        <td class="border px-2 py-1">${bill.bill_number}</td>
                        <td class="border px-2 py-1">${bill.total_amount}</td>
                        <td class="border px-2 py-1">${bill.due}</td>
                    </tr>
                `);
            });
        },


        error: function (xhr) {
            if (xhr.status === 404) {
                // Show error
                $("#meter_error")
                    .removeClass("hidden")
                    .text("Meter number not found.");

                // Clear all fields
                $('#customer_name').val("");
                $('#meter_model').val("");
                $('#meter_type').val("");
                $('#due_amount').val("");
                $('#unpaid-bills-table tbody').empty();
            }
        }
    });
});


</script>

@endsection
@endcan
