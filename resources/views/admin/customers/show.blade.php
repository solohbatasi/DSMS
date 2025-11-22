@extends('layouts.app')
@php use Illuminate\Support\Facades\Storage; @endphp
@section('title', 'Customer Profile - NYAWASCO')

@section('content')
<!-- Background Image -->
<div class="fixed inset-0 -z-10">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/80 to-white/90"></div>
</div>

<div class="container mx-auto px-4 py-8 relative z-10">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div class="text-center md:text-left">
            <h1 class="text-2xl md:text-3xl font-bold text-blue-800">{{ $customer->first_name }}'s Profile</h1>
            <p class="text-gray-600 mt-2">Complete customer information, billing history, and account management</p>
        </div>

        <!-- Bill Management Button -->
        <a href="{{ route('bills.index') }}" 
            class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg transition duration-200 flex items-center shadow-md">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Bill Management
        </a>
        <a href="{{ route('admin.customers.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-lg transition duration-200 flex items-center justify-center w-20px md:w-auto no-print text-sm">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Customers
        </a>
    </div>

    <!-- Account Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
        <!-- Account Balance -->
        <div class="bg-white rounded-xl shadow-md p-4 md:p-6 border border-gray-100 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Account Balance</p>
                    <p class="text-xl md:text-2xl font-bold mt-1 {{ $billingStats['account_balance'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        KSh {{ number_format(abs($billingStats['account_balance']), 2) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $billingStats['account_balance'] >= 0 ? 'Credit Balance' : 'Overdue Amount' }}
                    </p>
                </div>
                <div class="p-3 rounded-full {{ $billingStats['account_balance'] >= 0 ? 'bg-green-100' : 'bg-red-100' }}">
                    <i class="fas {{ $billingStats['account_balance'] >= 0 ? 'fa-wallet text-green-600' : 'fa-exclamation-triangle text-red-600' }} text-lg md:text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Outstanding Balance -->
        <div class="bg-white rounded-xl shadow-md p-4 md:p-6 border border-gray-100 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Outstanding Balance</p>
                    <p class="text-xl md:text-2xl font-bold mt-1 text-red-600">
                        KSh {{ number_format($billingStats['outstanding_balance'], 2) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">{{ $billingStats['unpaid_bills'] }} unpaid bills</p>
                </div>
                <div class="p-3 rounded-full bg-red-100">
                    <i class="fas fa-file-invoice text-red-600 text-lg md:text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Consumption -->
        <div class="bg-white rounded-xl shadow-md p-4 md:p-6 border border-gray-100 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Consumption</p>
                    <p class="text-xl md:text-2xl font-bold mt-1 text-blue-600">
                        {{ number_format($readingStats['total_consumption'], 2) }} m³
                    </p>
                    <p class="text-xs text-gray-500 mt-1">{{ $readingStats['total_readings'] }} readings</p>
                </div>
                <div class="p-3 rounded-full bg-blue-100">
                    <i class="fas fa-tachometer-alt text-blue-600 text-lg md:text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Average Monthly Usage -->
        <div class="bg-white rounded-xl shadow-md p-4 md:p-6 border border-gray-100 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Avg Monthly Usage</p>
                    <p class="text-xl md:text-2xl font-bold mt-1 text-purple-600">
                        {{ number_format($customer->average_monthly_consumption, 2) }} m³
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Based on history</p>
                </div>
                <div class="p-3 rounded-full bg-purple-100">
                    <i class="fas fa-chart-line text-purple-600 text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6 md:space-y-8">

             <!-- Billing History -->
            <div class="bg-white rounded-xl shadow-md p-4 md:p-6 border border-gray-100">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
                    <h2 class="text-lg md:text-xl font-semibold text-blue-800 flex items-center">
                        <i class="fas fa-file-invoice-dollar mr-2 text-blue-600"></i>
                        Billing History
                    </h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        {{ $billingStats['total_bills'] }} bills
                    </span>
                </div>

                @if($customer->bills->count() > 0)
                <div class="overflow-x-auto -mx-4 md:mx-0">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 md:px-4 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bill #</th>
                                <th class="px-3 py-2 md:px-4 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Period</th>
                                <th class="px-3 py-2 md:px-4 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Consumption</th>
                                <th class="px-3 py-2 md:px-4 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-3 py-2 md:px-4 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-3 py-2 md:px-4 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Due Date</th>
                                <th class="px-3 py-2 md:px-4 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider no-print">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($customer->bills as $bill)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-3 py-2 md:px-4 md:py-3 whitespace-nowrap">
                                    <div class="text-xs md:text-sm font-mono font-semibold text-blue-600">{{ $bill->bill_number }}</div>
                                </td>
                                <td class="px-3 py-2 md:px-4 md:py-3 whitespace-nowrap text-xs md:text-sm text-gray-500 hidden sm:table-cell">
                                    @if($bill->billing_period_start && $bill->billing_period_end)
                                        {{ $bill->billing_period_start->format('M d') }} - {{ $bill->billing_period_end->format('M d, Y') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-3 py-2 md:px-4 md:py-3 whitespace-nowrap text-xs md:text-sm text-gray-900">
                                    {{ number_format($bill->consumption, 2) }} m³
                                </td>
                                <td class="px-3 py-2 md:px-4 md:py-3 whitespace-nowrap">
                                    <div class="text-xs md:text-sm font-semibold text-gray-900">KSh {{ number_format($bill->total_amount, 2) }}</div>
                                    @if($bill->payments->count() > 0)
                                        <div class="text-xs text-green-600">
                                            Paid: KSh {{ number_format($bill->payments->sum('amount'), 2) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-3 py-2 md:px-4 md:py-3 whitespace-nowrap">
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
                                <td class="px-3 py-2 md:px-4 md:py-3 whitespace-nowrap text-xs md:text-sm text-gray-500 hidden md:table-cell">
                                    @if($bill->due_date)
                                        <span class="{{ $bill->is_overdue ? 'text-red-600 font-semibold' : '' }}">
                                            {{ $bill->formatted_due_date }}
                                        </span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-3 py-2 md:px-4 md:py-3 whitespace-nowrap text-sm font-medium no-print">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('bills.show', $bill->id) }}" 
                                           class="text-blue-600 hover:text-blue-900 transition duration-150 p-1 rounded">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('bills.edit', $bill->id) }}" 
                                           class="text-green-600 hover:text-green-900 transition duration-150 p-1 rounded">
                                            <i class="fas fa-edit"></i>
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
                    <i class="fas fa-file-invoice text-4xl text-gray-400 mb-3"></i>
                    <h3 class="text-lg font-medium text-gray-900">No Bills Found</h3>
                    <p class="text-gray-500">No bills have been generated for this customer yet.</p>
                </div>
                @endif
            </div>

            <!-- Reading History -->
            @if($customer->meter)
            <div class="bg-white rounded-xl shadow-md p-4 md:p-6 border border-gray-100">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
                    <h2 class="text-lg md:text-xl font-semibold text-green-700 flex items-center">
                        <i class="fas fa-tachometer-alt mr-2 text-green-600"></i>
                        Meter Reading History
                    </h2>
                    <a href="{{ route('admin.meter-readings.create', ['customer' => $customer->id]) }}" 
                       class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg transition duration-200 flex items-center text-sm no-print">
                        <i class="fas fa-plus mr-2"></i>
                        New Reading
                    </a>
                </div>

                @if($customer->meterReadings->count() > 0)
                <div class="overflow-x-auto -mx-4 md:mx-0">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 md:px-4 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-3 py-2 md:px-4 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Previous</th>
                                <th class="px-3 py-2 md:px-4 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current</th>
                                <th class="px-3 py-2 md:px-4 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Consumption</th>
                                <th class="px-3 py-2 md:px-4 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Charged Amount</th>
                                <th class="px-3 py-2 md:px-4 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Reader</th>
                                <th class="px-3 py-2 md:px-4 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($customer->meterReadings as $reading)
                            @php
                                // Calculate approximate charged amount (you can replace with actual bill amount)
                                $chargedAmount = $reading->consumption * 50; // Example rate: KSh 50 per m³
                            @endphp
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-3 py-2 md:px-4 md:py-3 whitespace-nowrap">
                                    <div class="text-xs md:text-sm font-medium text-gray-900">{{ $reading->reading_date->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500 hidden sm:block">{{ $reading->reading_period }}</div>
                                </td>
                                <td class="px-3 py-2 md:px-4 md:py-3 whitespace-nowrap text-xs md:text-sm text-gray-900 hidden sm:table-cell">
                                    {{ number_format($reading->previous_reading, 2) }} m³
                                </td>
                                <td class="px-3 py-2 md:px-4 md:py-3 whitespace-nowrap text-xs md:text-sm font-medium text-green-600">
                                    {{ number_format($reading->current_reading, 2) }} m³
                                </td>
                                <td class="px-3 py-2 md:px-4 md:py-3 whitespace-nowrap">
                                    <div class="text-xs md:text-sm font-medium text-blue-600">{{ number_format($reading->consumption, 2) }} m³</div>
                                </td>
                                <td class="px-3 py-2 md:px-4 md:py-3 whitespace-nowrap text-xs md:text-sm font-semibold text-purple-600 hidden md:table-cell">
                                    KSh {{ number_format($chargedAmount, 2) }}
                                </td>
                                <td class="px-3 py-2 md:px-4 md:py-3 whitespace-nowrap text-xs md:text-sm text-gray-900 hidden lg:table-cell">
                                    {{ $reading->reader->name ?? 'System' }}
                                </td>
                                <td class="px-3 py-2 md:px-4 md:py-3 whitespace-nowrap">
                                    @if($reading->billed)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Billed
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Unbilled
                                    </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-8">
                    <i class="fas fa-tachometer-alt text-4xl text-gray-400 mb-3"></i>
                    <h3 class="text-lg font-medium text-gray-900">No Reading History</h3>
                    <p class="text-gray-500">No meter readings have been recorded for this customer yet.</p>
                </div>
                @endif
            </div>
            @endif

            <!-- Recent Activity -->
            <div class="bg-white rounded-xl shadow-md p-4 md:p-6 border border-gray-100 print-break-inside-avoid">
                <h2 class="text-lg md:text-xl font-semibold text-purple-700 mb-4 flex items-center">
                    <i class="fas fa-history mr-2 text-purple-600"></i>
                    Recent Activity
                </h2>
                
                <div class="space-y-4">
                    @forelse($recentActivity as $activity)
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-full bg-{{ $activity['color'] }}-100 flex items-center justify-center">
                                <i class="fas fa-{{ $activity['icon'] }} text-{{ $activity['color'] }}-600 text-sm"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $activity['description'] }}</p>
                            <p class="text-xs text-gray-500">{{ $activity['date']->format('M d, Y') }}</p>
                            <p class="text-xs font-semibold {{ $activity['type'] === 'payment' ? 'text-green-600' : 'text-blue-600' }}">
                                KSh {{ number_format($activity['amount'], 2) }}
                            </p>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $activity['color'] }}-100 text-{{ $activity['color'] }}-800">
                                {{ ucfirst($activity['status']) }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="fas fa-history text-2xl text-gray-400 mb-2"></i>
                        <p class="text-gray-500 text-sm">No recent activity</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Customer Information -->
            <div class="bg-white rounded-xl shadow-md p-4 md:p-6 border border-gray-100">
                <h2 class="text-lg md:text-xl font-semibold text-blue-800 mb-4 flex items-center">
                    <i class="fas fa-user-circle mr-2 text-blue-600"></i>
                    Customer Information
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <!-- Personal Information -->
                    <div class="print-break-inside-avoid">
                        <h3 class="text-base md:text-lg font-medium text-gray-900 mb-3 flex items-center">
                            <i class="fas fa-id-card mr-2 text-gray-500 text-sm"></i>
                            Personal Information
                        </h3>
                        <div class="space-y-3">
                            <div>
                                <label class="text-xs md:text-sm text-gray-500 block">Customer ID</label>
                                <p class="font-medium text-blue-600">{{ $customer->customer_number }}</p>
                            </div>
                            <div>
                                <label class="text-xs md:text-sm text-gray-500 block">Full Name</label>
                                <p class="font-medium">{{ $customer->first_name }} {{ $customer->last_name }}</p>
                            </div>
                            <div>
                                <label class="text-xs md:text-sm text-gray-500 block">Email</label>
                                <p class="font-medium break-all">{{ $customer->email }}</p>
                            </div>
                            <div>
                                <label class="text-xs md:text-sm text-gray-500 block">Phone</label>
                                <p class="font-medium">{{ $customer->phone }}</p>
                            </div>
                            <div>
                                <label class="text-xs md:text-sm text-gray-500 block">ID Number</label>
                                <p class="font-medium">{{ $customer->id_number }}</p>
                            </div>
                            <div>
                                <label class="text-xs md:text-sm text-gray-500 block">Account Status</label>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-circle mr-1 text-xs"></i>
                                    {{ ucfirst($customer->status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Property Information -->
                    <div class="print-break-inside-avoid">
                        <h3 class="text-base md:text-lg font-medium text-gray-900 mb-3 flex items-center">
                            <i class="fas fa-home mr-2 text-gray-500 text-sm"></i>
                            Property Information
                        </h3>
                        <div class="space-y-3">
                            <div>
                                <label class="text-xs md:text-sm text-gray-500 block">Plot Number</label>
                                <p class="font-medium">{{ $customer->plot_number }}</p>
                            </div>
                            <div>
                                <label class="text-xs md:text-sm text-gray-500 block">House Number</label>
                                <p class="font-medium">{{ $customer->house_number }}</p>
                            </div>
                            <div>
                                <label class="text-xs md:text-sm text-gray-500 block">Estate/Area</label>
                                <p class="font-medium">{{ $customer->estate ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-xs md:text-sm text-gray-500 block">Physical Address</label>
                                <p class="font-medium">{{ $customer->physical_address }}</p>
                            </div>
                            <div>
                                <label class="text-xs md:text-sm text-gray-500 block">Connection Type</label>
                                <p class="font-medium capitalize">{{ $customer->connection_type }}</p>
                            </div>
                            <div>
                                <label class="text-xs md:text-sm text-gray-500 block">Connection Date</label>
                                <p class="font-medium">{{ $customer->connection_date?->format('M d, Y') ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Application Documents Section -->
            @if($customer->waterApplication)
            <div class="bg-white rounded-xl shadow-md p-4 md:p-6 border border-gray-100">
                <h2 class="text-lg md:text-xl font-semibold text-blue-800 mb-4 flex items-center">
                    <i class="fas fa-file-alt mr-2 text-blue-600"></i>
                    Application Documents
                </h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <!-- National ID -->
                    <div class="border border-gray-200 rounded-lg p-3 hover:shadow-md transition duration-200 card-hover">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-medium text-gray-700 text-sm">National ID</h4>
                            @if($customer->waterApplication->national_id_file)
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded flex items-center">
                                    <i class="fas fa-check mr-1 text-xs"></i>
                                </span>
                            @else
                                <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded flex items-center">
                                    <i class="fas fa-times mr-1 text-xs"></i>
                                </span>
                            @endif
                        </div>
                        @if($customer->waterApplication->national_id_file)
                            <div class="flex flex-col space-y-2">
                                <a href="{{ Storage::url($customer->waterApplication->national_id_file) }}" 
                                   target="_blank"
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded text-xs flex items-center justify-center transition duration-200">
                                    <i class="fas fa-eye mr-1"></i>
                                    View
                                </a>
                                <a href="{{ Storage::url($customer->waterApplication->national_id_file) }}" 
                                   download
                                   class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded text-xs flex items-center justify-center transition duration-200">
                                    <i class="fas fa-download mr-1"></i>
                                    Download
                                </a>
                            </div>
                        @else
                            <p class="text-xs text-gray-500 italic text-center py-1">Not uploaded</p>
                        @endif
                    </div>

                    <!-- KRA Pin -->
                    <div class="border border-gray-200 rounded-lg p-3 hover:shadow-md transition duration-200 card-hover">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-medium text-gray-700 text-sm">KRA Pin</h4>
                            @if($customer->waterApplication->kra_pin_file)
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded flex items-center">
                                    <i class="fas fa-check mr-1 text-xs"></i>
                                </span>
                            @else
                                <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded flex items-center">
                                    <i class="fas fa-times mr-1 text-xs"></i>
                                </span>
                            @endif
                        </div>
                        @if($customer->waterApplication->kra_pin_file)
                            <div class="flex flex-col space-y-2">
                                <a href="{{ Storage::url($customer->waterApplication->kra_pin_file) }}" 
                                   target="_blank"
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded text-xs flex items-center justify-center transition duration-200">
                                    <i class="fas fa-eye mr-1"></i>
                                    View
                                </a>
                                <a href="{{ Storage::url($customer->waterApplication->kra_pin_file) }}" 
                                   download
                                   class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded text-xs flex items-center justify-center transition duration-200">
                                    <i class="fas fa-download mr-1"></i>
                                    Download
                                </a>
                            </div>
                        @else
                            <p class="text-xs text-gray-500 italic text-center py-1">Not uploaded</p>
                        @endif
                    </div>

                    <!-- Title Document -->
                    <div class="border border-gray-200 rounded-lg p-3 hover:shadow-md transition duration-200 card-hover">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-medium text-gray-700 text-sm">Title Document</h4>
                            @if($customer->waterApplication->title_document)
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded flex items-center">
                                    <i class="fas fa-check mr-1 text-xs"></i>
                                </span>
                            @else
                                <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded flex items-center">
                                    <i class="fas fa-times mr-1 text-xs"></i>
                                </span>
                            @endif
                        </div>
                        @if($customer->waterApplication->title_document)
                            <div class="flex flex-col space-y-2">
                                <a href="{{ Storage::url($customer->waterApplication->title_document) }}" 
                                   target="_blank"
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded text-xs flex items-center justify-center transition duration-200">
                                    <i class="fas fa-eye mr-1"></i>
                                    View
                                </a>
                                <a href="{{ Storage::url($customer->waterApplication->title_document) }}" 
                                   download
                                   class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded text-xs flex items-center justify-center transition duration-200">
                                    <i class="fas fa-download mr-1"></i>
                                    Download
                                </a>
                            </div>
                        @else
                            <p class="text-xs text-gray-500 italic text-center py-1">Not uploaded</p>
                        @endif
                    </div>
                </div>

                <!-- Application Details -->
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs">
                        <div class="flex items-center">
                            <strong class="text-gray-700 mr-2 w-20">App ID:</strong>
                            <span class="text-blue-600 font-medium">{{ $customer->waterApplication->id }}</span>
                        </div>
                        <div class="flex items-center">
                            <strong class="text-gray-700 mr-2 w-20">Date:</strong>
                            <span>{{ $customer->waterApplication->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex items-center">
                            <strong class="text-gray-700 mr-2 w-20">Status:</strong>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ ucfirst($customer->waterApplication->status) }}
                            </span>
                        </div>
                        <div class="flex items-center">
                            <strong class="text-gray-700 mr-2 w-20">Processor:</strong>
                            <span class="truncate">{{ $customer->waterApplication->processedBy->name ?? 'System' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <!-- Show message if no application found -->
            <div class="bg-yellow-50 rounded-xl shadow-md p-4 border border-yellow-200">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-yellow-500 text-sm mr-2 mt-0.5"></i>
                    <div>
                        <h3 class="text-sm font-semibold text-yellow-800">No Application Found</h3>
                        <p class="text-yellow-600 text-xs mt-1">This customer was not created through a water connection application.</p>
                    </div>
                </div>
            </div>
            @endif

           
        </div>

        <!-- Right Column -->
        <div class="space-y-6 md:space-y-8">
            <!-- Meter Information -->
            @if($customer->meter)
            <div class="bg-white rounded-xl shadow-md p-4 md:p-6 border border-gray-100 print-break-inside-avoid">
                <h2 class="text-lg md:text-xl font-semibold text-green-700 mb-4 flex items-center">
                    <i class="fas fa-tachometer-alt mr-2 text-green-600"></i>
                    Meter Information
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="text-xs md:text-sm text-gray-500 block">Meter Number</label>
                        <p class="font-medium text-blue-600 text-base md:text-lg">{{ $customer->meter->meter_number }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs md:text-sm text-gray-500 block">Meter Type</label>
                            <p class="font-medium capitalize">{{ $customer->meter->meter_type }}</p>
                        </div>
                        <div>
                            <label class="text-xs md:text-sm text-gray-500 block">Model</label>
                            <p class="font-medium">{{ $customer->meter->meter_model ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs md:text-sm text-gray-500 block">Initial Reading</label>
                            <p class="font-medium">{{ number_format($customer->meter->initial_reading, 2) }} m³</p>
                        </div>
                        <div>
                            <label class="text-xs md:text-sm text-gray-500 block">Current Reading</label>
                            <p class="font-medium text-green-600">{{ number_format($customer->meter->current_reading, 2) }} m³</p>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs md:text-sm text-gray-500 block">Installation Date</label>
                        <p class="font-medium">{{ $customer->meter->installation_date?->format('M d, Y') ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-xs md:text-sm text-gray-500 block">Meter Status</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-circle mr-1 text-xs"></i>
                            {{ ucfirst($customer->meter->status) }}
                        </span>
                    </div>
                </div>

                @if($customer->meter->notes)
                <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                    <h4 class="font-medium text-gray-700 mb-1 text-sm">Installation Notes</h4>
                    <p class="text-gray-600 text-sm">{{ $customer->meter->notes }}</p>
                </div>
                @endif
            </div>
            @else
            <div class="bg-yellow-50 rounded-xl shadow-md p-4 md:p-6 border border-yellow-200">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-yellow-500 text-lg mr-3 mt-1"></i>
                    <div>
                        <h3 class="text-base md:text-lg font-semibold text-yellow-800">No Meter Assigned</h3>
                        <p class="text-yellow-600 text-sm mt-1">This customer does not have a water meter assigned yet.</p>
                    </div>
                </div>
            </div>
            @endif

            

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-md p-4 md:p-6 border border-gray-100 no-print">
                <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-bolt mr-2 text-yellow-500"></i>
                    Quick Actions
                </h2>
                
                <div class="space-y-3">
                    @if($customer->meter)
                    <a href="{{ route('admin.meter-readings.create', ['customer' => $customer->id]) }}" 
                       class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        Record Meter Reading
                    </a>
                    @endif
                    
                    <a href="{{ route('bills.index') }}?customer={{ $customer->id }}" 
                       class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-file-invoice mr-2"></i>
                        View All Bills
                    </a>

                    <a href="{{ route('payments.index') }}?customer={{ $customer->id }}" 
                       class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-credit-card mr-2"></i>
                        Record Payment
                    </a>

                    <button onclick="window.print()" 
                            class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-3 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-print mr-2"></i>
                        Print Profile
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card-hover {
        transition: all 0.3s ease;
    }
    
    .card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    @media (max-width: 768px) {
        .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }
    }

    @media print {
        .no-print {
            display: none !important;
        }
        
        .print-break-inside-avoid {
            break-inside: avoid;
        }
        
        .bg-white {
            background: white !important;
        }
        
        .fixed {
            display: none !important;
        }
        
        .shadow-md {
            box-shadow: none !important;
        }
        
        .border {
            border: 1px solid #e5e7eb !important;
        }
    }
</style>
@endsection