@extends('layouts.app')
@props(['title' => 'Admin Dashboard', 'subtitle' => 'Manage education system, finances, and users'])

@section('content')
@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\DB;
@endphp
<!-- Add this after the page title or in a prominent place -->
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
@endif
<div class="min-h-screen bg-gray-50">
    <!-- Enhanced Header -->
    @include('components.dashboard-header')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Financial Overview Cards -->

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
             <!-- Active Courses -->
            <div class="bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 rounded-xl shadow-sm border border-blue-400 overflow-hidden transition-all duration-200 hover:shadow-md hover:scale-[1.02] cursor-pointer">
                <a href="https://lim110.truehost.cloud:2096/webmaillogout.cgi" target="_blank" class="block">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="h-12 w-12 rounded-lg bg-white/20 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <dt class="text-sm font-medium text-white/90 truncate">Email Access</dt>
                                <dd class="text-2xl font-semibold text-white">Webmail</dd>
                            </div>
                        </div>
                    </div>
                    <div class="bg-blue-800/40 px-5 py-3">
                        <div class="text-sm">
                            <span class="font-medium text-white hover:text-white/90 transition-colors duration-200 flex items-center">
                                Open Webmail Portal
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </span>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Total Revenue -->

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-200 hover:shadow-md">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-lg bg-green-50 flex items-center justify-center">
                                <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.567-3 3.5S10.343 15 12 15s3-1.567 3-3.5S13.657 8 12 8z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Revenue</dt>
                            <dd class="text-2xl font-semibold text-gray-900">KES {{ number_format($total_revenue, 2) }}</dd>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <span class="font-medium text-green-600">+{{ $revenue_growth }}%</span>
                        <span class="text-gray-500 ml-1">from last month</span>
                    </div>
                </div>
            </div>

            <!-- Total Students -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-200 hover:shadow-md">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-lg bg-blue-50 flex items-center justify-center">
                                <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V8H2v12h5m10 0a2 2 0 01-2 2H9a2 2 0 01-2-2m10 0v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Students</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $total_students }}</dd>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('students.index') }}" class="font-medium text-blue-600 hover:text-blue-700 transition-colors duration-200 flex items-center">
                            View all students
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>



            <!-- Pending Payments -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-200 hover:shadow-md">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-lg bg-orange-50 flex items-center justify-center">
                                <svg class="h-6 w-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <dt class="text-sm font-medium text-gray-500 truncate">Pending Payments</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $pending_payments }}</dd>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('payments.index') }}" class="font-medium text-orange-600 hover:text-orange-700 transition-colors duration-200 flex items-center">
                            Review payments
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Second Row Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Expenses -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-200 hover:shadow-md">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-lg bg-red-50 flex items-center justify-center">
                                <svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M5 7V5a2 2 0 012-2h10a2 2 0 012 2v2m-2 4v9a2 2 0 01-2 2H9a2 2 0 01-2-2v-9m10 0H7" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Expenses</dt>
                            <dd class="text-2xl font-semibold text-gray-900">KES {{ number_format($total_expenses, 2) }}</dd>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('expenses.index') }}" class="font-medium text-red-600 hover:text-red-700 transition-colors duration-200 flex items-center">
                            View expenses
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Active EF Lists -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-200 hover:shadow-md">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-lg bg-teal-50 flex items-center justify-center">
                                <svg class="h-6 w-6 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <dt class="text-sm font-medium text-gray-500 truncate">Active EF Lists</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $active_ef_lists }}</dd>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <span class="text-gray-500">{{ $pending_verifications }} pending verification</span>
                    </div>
                </div>
            </div>

            <!-- Outstanding Balance -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-200 hover:shadow-md">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-lg bg-amber-50 flex items-center justify-center">
                                <svg class="h-6 w-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <dt class="text-sm font-medium text-gray-500 truncate">Outstanding Balance</dt>
                            <dd class="text-2xl font-semibold text-gray-900">KES {{ number_format($outstanding_balance, 2) }}</dd>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <span class="text-amber-600 font-medium">{{ $students_with_balance }} students with balance</span>
                    </div>
                </div>
            </div>

            <!-- Net Profit -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-200 hover:shadow-md">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-lg bg-emerald-50 flex items-center justify-center">
                                <svg class="h-6 w-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <dt class="text-sm font-medium text-gray-500 truncate">Net Profit</dt>
                            <dd class="text-2xl font-semibold text-gray-900">KES {{ number_format($net_profit, 2) }}</dd>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <span class="{{ $net_profit >= 0 ? 'text-emerald-600' : 'text-red-600' }} font-medium">
                            {{ $net_profit >= 0 ? '+' : '' }}{{ number_format($profit_margin, 1) }}% margin
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Revenue vs Expenses Chart -->
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-700">Revenue vs Expenses</h3>
                <canvas id="revenueExpensesChart" height="250"></canvas>
            </div>

            <!-- Payment Status Distribution -->
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-700">Payment Status Distribution</h3>
                <canvas id="paymentStatusChart" height="250"></canvas>
            </div>
        </div>

        <!-- Additional Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Expense Categories -->
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-700">Expense Categories</h3>
                <canvas id="expenseCategoriesChart" height="250"></canvas>
            </div>

            <!-- Course Enrollment -->
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-700">Course Enrollment</h3>
                <canvas id="courseEnrollmentChart" height="250"></canvas>
            </div>
        </div>

        <!-- Recent Activity & Quick Stats -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Recent Payments -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden lg:col-span-2">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Payments</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">EF No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($recent_payments as $payment)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $payment->EfList->student->name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $payment->EfList->ef_no ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    KES {{ number_format($payment->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') : 'N/A' }}

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $payment->payment_status === 'completed' ? 'bg-green-100 text-green-800' :
                                           ($payment->payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                           'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($payment->payment_status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                                    No recent payments found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">System Overview</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600">Staff Users</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $staff_users }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600">Instructors</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $instructors }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600">Expense Categories</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $expense_categories }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600">Avg Payment</span>
                        <span class="text-sm font-semibold text-gray-900">KES {{ number_format($average_payment, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600">Collections Rate</span>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format($collection_rate, 1) }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Table Section (Excluding Students and Developers) -->
       @can('view users')
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">System Users (Staff & Instructors)</h3>
                    <div class="flex space-x-3">
                        <div class="relative">
                            <input type="text" id="userSearch" placeholder="Search users..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        @can('add users')
                        <button id="addUserBtn" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add User
                        </button>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Branch</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Active</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100" id="usersTableBody">
                        @forelse($system_users as $user)
                        <tr class="hover:bg-gray-50 transition-colors duration-150" data-user-id="{{ $user->id }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($user->avatar)
                                    <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="h-10 w-10 rounded-full object-cover border border-gray-200">
                                    @else
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center border border-gray-200">
                                        <span class="text-sm font-semibold text-blue-700">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </span>
                                    </div>
                                    @endif
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' :
                                    ($user->role === 'instructor' ? 'bg-blue-100 text-blue-800' :
                                    ($user->role === 'manager' ? 'bg-purple-100 text-purple-800' :
                                    ($user->role === 'ict' ? 'bg-green-100 text-green-800' :
                                    ($user->role === 'ceo' ? 'bg-yellow-100 text-yellow-800' :
                                    ($user->role === 'chief' ? 'bg-indigo-100 text-indigo-800' :
                                    ($user->role === 'registrar' ? 'bg-pink-100 text-pink-800' :
                                    ($user->role === 'report' ? 'bg-orange-100 text-orange-800' :
                                    ($user->role === 'biller' ? 'bg-teal-100 text-teal-800' : 'bg-gray-100 text-gray-800')))))))) }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                             <td class="px-6 py-4 whitespace-nowrap">

                                @if($user->branch)

                                <span class="text-xs text-gray-500 mt-1">{{ $user->branch->name ?? ' '}}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-start space-x-2">
                                    @can('edit users')
                                    <button class="edit-user-btn inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->name }}"
                                            data-user-email="{{ $user->email }}"
                                            data-user-role="{{ $user->role }}"
                                            data-user-branch-id="{{ $user->branch_id }}"
                                            data-user-active="{{ $user->is_active }}">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </button>
                                    @endcan
                                    @can('edit permissions')
                                    <button class="permissions-btn inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200"
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->name }}"
                                            data-user-role="{{ $user->role }}">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                        Permissions
                                    </button>
                                    @endcan
                                    @can('delete users')
                                    <button class="delete-user-btn inline-flex items-center px-3 py-1.5 border border-transparent rounded-lg text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->name }}">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                                No system users found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($system_users->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        Showing <span class="font-medium">{{ $system_users->firstItem() }}</span>
                        to <span class="font-medium">{{ $system_users->lastItem() }}</span>
                        of <span class="font-medium">{{ $system_users->total() }}</span> results
                    </div>
                    <div class="flex space-x-2">
                        @if ($system_users->onFirstPage())
                        <span class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-400 bg-white cursor-not-allowed">
                            Previous
                        </span>
                        @else
                        <a href="{{ $system_users->previousPageUrl() }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            Previous
                        </a>
                        @endif

                        @if ($system_users->hasMorePages())
                        <a href="{{ $system_users->nextPageUrl() }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            Next
                        </a>
                        @else
                        <span class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-400 bg-white cursor-not-allowed">
                            Next
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endcan





        <style>
            @keyframes fade-in-up {
                from {
                    opacity: 0;
                    transform: translateY(1rem);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            .animate-fade-in-up {
                animation: fade-in-up 0.3s ease-out;
            }
        </style>
    </div>
</div>
<!-- Add User Modal -->
<div id="addUserModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Add New User</h3>
                            <p class="text-blue-100 text-sm">Create a new system user account</p>
                        </div>
                    </div>
                    <button type="button" id="closeAddModal" class="text-white hover:text-blue-200 transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="bg-white px-6 py-6">
                <form id="addUserForm">
                    @csrf
                    <div class="space-y-5">
                        <div>
                            <label for="add_name" class="block text-sm font-semibold text-gray-900 mb-2">Full Name</label>
                            <input type="text" name="name" id="add_name" required class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors duration-200" placeholder="Enter full name">
                        </div>

                        <div>
                            <label for="add_email" class="block text-sm font-semibold text-gray-900 mb-2">Email Address</label>
                            <input type="email" name="email" id="add_email" required class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors duration-200" placeholder="Enter email address">
                        </div>

                        <div>
                            <label for="add_password" class="block text-sm font-semibold text-gray-900 mb-2">Password</label>
                            <input type="password" name="password" id="add_password" required class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors duration-200" placeholder="Enter password">
                        </div>

                        <div>
                            <label for="add_role" class="block text-sm font-semibold text-gray-900 mb-3">User Role</label>
                            <select name="role" id="add_role" required class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors duration-200">
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- In the Add User Modal form -->
                        <div>
                            <label for="add_branch_id" class="block text-sm font-semibold text-gray-900 mb-2">Branch</label>
                            <select name="branch_id" id="add_branch_id" class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors duration-200">
                                <option value="">Select Branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">
                                Leave empty for admin users, required for branch-specific roles
                            </p>
                        </div>
                        <div>
                            <input type="hidden" name="is_active" value="0">
                            <label class="flex items-center space-x-3">
                                <input type="checkbox" name="is_active" id="add_is_active" value="1" checked class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded transition-colors duration-200">
                                <span class="text-sm text-gray-700">Active User</span>
                            </label>
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-end space-x-3">
                    <button type="button" id="closeAddModalBtn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="button" id="createUserBtn" class="px-6 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg shadow-sm hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Create User</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Edit User</h3>
                            <p class="text-blue-100 text-sm">Update user information</p>
                        </div>
                    </div>
                    <button type="button" id="closeEditModal" class="text-white hover:text-blue-200 transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="bg-white px-6 py-6">
                <form id="editUserForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_user_id" name="user_id">

                    <div class="space-y-5">
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                            <div class="flex items-center space-x-4">
                                <div id="edit_user_avatar" class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center border-2 border-white shadow-sm">
                                    <!-- Avatar will be dynamically set -->
                                </div>
                                <div>
                                    <h4 id="edit_user_name_preview" class="text-lg font-semibold text-gray-900"></h4>
                                    <p id="edit_user_email_preview" class="text-sm text-gray-600"></p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="edit_name" class="block text-sm font-semibold text-gray-900 mb-2">Full Name</label>
                            <input type="text" name="name" id="edit_name" required class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors duration-200">
                        </div>

                        <div>
                            <label for="edit_email" class="block text-sm font-semibold text-gray-900 mb-2">Email Address</label>
                            <input type="email" name="email" id="edit_email" required class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors duration-200">
                        </div>

                        <div>
                            <label for="edit_role" class="block text-sm font-semibold text-gray-900 mb-3">User Role</label>
                            <select name="role" id="edit_role" required class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors duration-200">
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- In the Edit User Modal form -->
                        <div>
                            <label for="edit_branch_id" class="block text-sm font-semibold text-gray-900 mb-2">Branch</label>
                            <select name="branch_id" id="edit_branch_id" class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors duration-200">
                                <option value="">Select Branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <input type="hidden" name="is_active" value="0">
                            <label class="flex items-center space-x-3">
                                <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded transition-colors duration-200">
                                <span class="text-sm text-gray-700">Active User</span>
                            </label>
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-end space-x-3">
                    <button type="button" id="closeEditModalBtn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="button" id="updateUserBtn" class="px-6 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg shadow-sm hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Update User</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Permissions Modal -->
<div id="permissionsModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Manage Permissions</h3>
                            <p class="text-purple-100 text-sm">Configure user permissions and access</p>
                        </div>
                    </div>
                    <button type="button" id="closePermissionsModal" class="text-white hover:text-purple-200 transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="bg-white px-6 py-6 max-h-96 overflow-y-auto">
                <form id="permissionsForm">
                    @csrf
                    <input type="hidden" id="perm_user_id" name="user_id">

                    <div class="space-y-6">
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                            <div class="flex items-center space-x-4">
                                <div id="perm_user_avatar" class="h-12 w-12 rounded-full bg-gradient-to-br from-purple-100 to-purple-200 flex items-center justify-center border-2 border-white shadow-sm">
                                    <!-- Avatar will be dynamically set -->
                                </div>
                                <div>
                                    <h4 id="perm_user_name" class="text-lg font-semibold text-gray-900"></h4>
                                    <p id="perm_user_email" class="text-sm text-gray-600"></p>
                                    <p class="text-xs text-gray-500">Role: <span id="perm_user_role" class="font-medium"></span></p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-3">Permissions</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($permissions_list as $permission)
                                <label class="flex items-center space-x-3 p-3 rounded-lg border border-gray-200 hover:border-purple-300 hover:bg-purple-50 transition-all duration-200 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                        class="focus:ring-purple-500 h-4 w-4 text-purple-600 border-gray-300 rounded transition-colors duration-200 permission-checkbox">
                                    <span class="text-sm font-medium text-gray-900 capitalize">
                                        {{ str_replace('_', ' ', $permission->name) }}
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        <span id="selectedPermissionsCount">0</span> permissions selected
                    </div>
                    <div class="flex space-x-3">
                        <button type="button" id="closePermissionsModalBtn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="button" id="updatePermissionsBtn" class="px-6 py-2 text-sm font-medium text-white bg-gradient-to-r from-purple-600 to-purple-700 rounded-lg shadow-sm hover:from-purple-700 hover:to-purple-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200 flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Update Permissions</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  // Complete User Management JavaScript
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Modal Elements
        const addUserModal = document.getElementById('addUserModal');
        const editUserModal = document.getElementById('editUserModal');
        const permissionsModal = document.getElementById('permissionsModal');

        // Modal Management Functions
        function openModal(modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            modal.classList.add('animate-fade-in-up');
        }

        function closeModal(modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
            modal.classList.remove('animate-fade-in-up');
        }

        // Close modals when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === addUserModal) closeModal(addUserModal);
            if (event.target === editUserModal) closeModal(editUserModal);
            if (event.target === permissionsModal) closeModal(permissionsModal);
        });

        // Close modals with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                if (!addUserModal.classList.contains('hidden')) closeModal(addUserModal);
                if (!editUserModal.classList.contains('hidden')) closeModal(editUserModal);
                if (!permissionsModal.classList.contains('hidden')) closeModal(permissionsModal);
            }
        });

        // Add User Modal Functionality
        const addUserBtn = document.getElementById('addUserBtn');
        const closeAddModal = document.getElementById('closeAddModal');
        const closeAddModalBtn = document.getElementById('closeAddModalBtn');
        const createUserBtn = document.getElementById('createUserBtn');

        if (addUserBtn) {
            addUserBtn.addEventListener('click', function() {
                openModal(addUserModal);
            });
        }

        if (closeAddModal) {
            closeAddModal.addEventListener('click', function() {
                closeModal(addUserModal);
            });
        }

        if (closeAddModalBtn) {
            closeAddModalBtn.addEventListener('click', function() {
                closeModal(addUserModal);
            });
        }

        if (createUserBtn) {
            createUserBtn.addEventListener('click', function() {
                createUser();
            });
        }

        // Edit User Modal Functionality
        const closeEditModal = document.getElementById('closeEditModal');
        const closeEditModalBtn = document.getElementById('closeEditModalBtn');
        const updateUserBtn = document.getElementById('updateUserBtn');

        if (closeEditModal) {
            closeEditModal.addEventListener('click', function() {
                closeModal(editUserModal);
            });
        }

        if (closeEditModalBtn) {
            closeEditModalBtn.addEventListener('click', function() {
                closeModal(editUserModal);
            });
        }

        if (updateUserBtn) {
            updateUserBtn.addEventListener('click', function() {
                updateUser();
            });
        }

        // Permissions Modal Functionality
        const closePermissionsModal = document.getElementById('closePermissionsModal');
        const closePermissionsModalBtn = document.getElementById('closePermissionsModalBtn');
        const updatePermissionsBtn = document.getElementById('updatePermissionsBtn');

        if (closePermissionsModal) {
            closePermissionsModal.addEventListener('click', function() {
                closeModal(permissionsModal);
            });
        }

        if (closePermissionsModalBtn) {
            closePermissionsModalBtn.addEventListener('click', function() {
                closeModal(permissionsModal);
            });
        }

        if (updatePermissionsBtn) {
            updatePermissionsBtn.addEventListener('click', function() {
                updatePermissions();
            });
        }

        // Delegated event listeners for dynamic content
        document.addEventListener('click', function(e) {
            // Edit User Button
            const editBtn = e.target.closest('.edit-user-btn');
            if (editBtn) {
                e.preventDefault();
                const userId = editBtn.dataset.userId;
                const userName = editBtn.dataset.userName;
                const userEmail = editBtn.dataset.userEmail;
                const userRole = editBtn.dataset.userRole;
                const userActive = editBtn.dataset.userActive === '1';
                const userBranchId = editBtn.dataset.userBranchId || '';

                document.getElementById('edit_user_id').value = userId;
                document.getElementById('edit_name').value = userName;
                document.getElementById('edit_email').value = userEmail;
                document.getElementById('edit_role').value = userRole;
                document.getElementById('edit_is_active').checked = userActive;
                document.getElementById('edit_user_name_preview').textContent = userName;
                document.getElementById('edit_user_email_preview').textContent = userEmail;
                document.getElementById('edit_branch_id').value = userBranchId;

                const avatarContainer = document.getElementById('edit_user_avatar');
                const userAvatar = editBtn.closest('tr').querySelector('img')?.src || '';
                if (userAvatar) {
                    avatarContainer.innerHTML = `<img src="${userAvatar}" alt="${userName}" class="h-12 w-12 rounded-full object-cover border-2 border-white shadow-sm">`;
                } else {
                    const initial = userName.charAt(0).toUpperCase();
                    avatarContainer.innerHTML = `<span class="text-lg font-semibold text-blue-700">${initial}</span>`;
                }

                openModal(editUserModal);
            }

            // Permissions Button
            const permBtn = e.target.closest('.permissions-btn');
            if (permBtn) {
                e.preventDefault();
                const userId = permBtn.dataset.userId;
                const userName = permBtn.dataset.userName;
                const userRole = permBtn.dataset.userRole;
                const userEmail = permBtn.closest('tr').querySelector('td:first-child .text-gray-500')?.textContent || '';

                document.getElementById('perm_user_id').value = userId;
                document.getElementById('perm_user_name').textContent = userName;
                document.getElementById('perm_user_email').textContent = userEmail;
                document.getElementById('perm_user_role').textContent = userRole;

                const avatarContainer = document.getElementById('perm_user_avatar');
                const userAvatar = permBtn.closest('tr').querySelector('img')?.src || '';
                if (userAvatar) {
                    avatarContainer.innerHTML = `<img src="${userAvatar}" alt="${userName}" class="h-12 w-12 rounded-full object-cover border-2 border-white shadow-sm">`;
                } else {
                    const initial = userName.charAt(0).toUpperCase();
                    avatarContainer.innerHTML = `<span class="text-lg font-semibold text-purple-700">${initial}</span>`;
                }

                // Load user permissions
                loadUserPermissions(userId);
                openModal(permissionsModal);
            }

            // Delete User Button
            const deleteBtn = e.target.closest('.delete-user-btn');
            if (deleteBtn) {
                e.preventDefault();
                const userId = deleteBtn.dataset.userId;
                const userName = deleteBtn.dataset.userName;
                deleteUser(userId, userName);
            }
        });

        // Permission checkboxes count update
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('permission-checkbox')) {
                updatePermissionsCount();
            }
        });

        // Search functionality
        const userSearch = document.getElementById('userSearch');
        if (userSearch) {
            userSearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = document.querySelectorAll('#usersTableBody tr');

                rows.forEach(row => {
                    const userName = row.querySelector('td:first-child .text-gray-900').textContent.toLowerCase();
                    const userEmail = row.querySelector('td:first-child .text-gray-500').textContent.toLowerCase();
                    const userRole = row.querySelector('td:nth-child(2) span').textContent.toLowerCase();

                    if (userName.includes(searchTerm) || userEmail.includes(searchTerm) || userRole.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }

        // API Functions
        function createUser() {
            const formData = new FormData(document.getElementById('addUserForm'));

            // Show loading state
            const originalText = createUserBtn.innerHTML;
            createUserBtn.innerHTML = `
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mx-auto"></div>
                <span>Creating...</span>
            `;
            createUserBtn.disabled = true;

            fetch('{{ route("admin.users.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('User created successfully', 'success');
                    closeModal(addUserModal);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    throw new Error(data.message || 'Failed to create user');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification(error.message || 'An error occurred while creating the user', 'error');
            })
            .finally(() => {
                createUserBtn.innerHTML = originalText;
                createUserBtn.disabled = false;
            });
        }

        function updateUser() {
            const userId = document.getElementById('edit_user_id').value;
            const formData = new FormData(document.getElementById('editUserForm'));

            // Show loading state
            const originalText = updateUserBtn.innerHTML;
            updateUserBtn.innerHTML = `
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mx-auto"></div>
                <span>Updating...</span>
            `;
            updateUserBtn.disabled = true;

            fetch(`/admin/users/${userId}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('User updated successfully', 'success');
                    closeModal(editUserModal);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    throw new Error(data.message || 'Failed to update user');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification(error.message || 'An error occurred while updating the user', 'error');
            })
            .finally(() => {
                updateUserBtn.innerHTML = originalText;
                updateUserBtn.disabled = false;
            });
        }

        function deleteUser(userId, userName) {
            if (!confirm(`Are you sure you want to delete user "${userName}"? This action cannot be undone.`)) {
                return;
            }

            showNotification(`Deleting user "${userName}"...`, 'info');

            fetch(`/admin/users/${userId}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('User deleted successfully', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    throw new Error(data.message || 'Failed to delete user');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification(error.message || 'An error occurred while deleting the user', 'error');
            });
        }

        function loadUserPermissions(userId) {
            fetch(`/admin/users/${userId}/permissions`)
                .then(response => response.json())
                .then(data => {
                    // Clear all checkboxes first
                    document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                        checkbox.checked = false;
                    });

                    // Check the user's permissions
                    if (data.permissions) {
                        data.permissions.forEach(permission => {
                            const checkbox = document.querySelector(`.permission-checkbox[value="${permission}"]`);
                            if (checkbox) {
                                checkbox.checked = true;
                            }
                        });
                    }

                    updatePermissionsCount();
                })
                .catch(error => {
                    console.error('Error loading permissions:', error);
                    showNotification('Failed to load permissions', 'error');
                });
        }

        function updatePermissions() {
            const userId = document.getElementById('perm_user_id').value;
            const formData = new FormData(document.getElementById('permissionsForm'));

            // Show loading state
            const originalText = updatePermissionsBtn.innerHTML;
            updatePermissionsBtn.innerHTML = `
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mx-auto"></div>
                <span>Updating...</span>
            `;
            updatePermissionsBtn.disabled = true;

            fetch(`/admin/users/${userId}/permissions`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Permissions updated successfully', 'success');
                    closeModal(permissionsModal);
                } else {
                    throw new Error(data.message || 'Failed to update permissions');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification(error.message || 'An error occurred while updating permissions', 'error');
            })
            .finally(() => {
                updatePermissionsBtn.innerHTML = originalText;
                updatePermissionsBtn.disabled = false;
            });
        }

        function updatePermissionsCount() {
            const count = document.querySelectorAll('.permission-checkbox:checked').length;
            const countElement = document.getElementById('selectedPermissionsCount');
            if (countElement) {
                countElement.textContent = count;
            }
        }

        // Notification function
        function showNotification(message, type = 'info') {
            // Remove existing notifications
            const existingNotifications = document.querySelectorAll('.custom-notification');
            existingNotifications.forEach(notification => notification.remove());

            const notification = document.createElement('div');
            notification.className = `custom-notification fixed top-4 right-4 z-50 p-4 rounded-xl shadow-lg text-white transform transition-all duration-300 ${
                type === 'success' ? 'bg-green-500' :
                type === 'error' ? 'bg-red-500' :
                type === 'warning' ? 'bg-yellow-500' :
                'bg-blue-500'
            } animate-fade-in-up`;
            notification.innerHTML = `
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${
                            type === 'success' ? 'M5 13l4 4L19 7' :
                            type === 'error' ? 'M6 18L18 6M6 6l12 12' :
                            type === 'warning' ? 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z' :
                            'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
                        }"></path>
                    </svg>
                    <span class="font-medium">${message}</span>
                </div>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.classList.add('opacity-0', 'transform', 'scale-95');
                setTimeout(() => notification.remove(), 300);
            }, 4000);
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Revenue vs Expenses Chart
        const revenueExpensesCtx = document.getElementById('revenueExpensesChart').getContext('2d');
        new Chart(revenueExpensesCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [
                    {
                        label: 'Revenue',
                        data: @json($monthly_revenue),
                        backgroundColor: 'rgba(34, 197, 94, 0.8)',
                        borderColor: 'rgba(34, 197, 94, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Expenses',
                        data: @json($monthly_expenses),
                        backgroundColor: 'rgba(239, 68, 68, 0.8)',
                        borderColor: 'rgba(239, 68, 68, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'KES ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Payment Status Chart
        const paymentStatusCtx = document.getElementById('paymentStatusChart').getContext('2d');
        new Chart(paymentStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'Pending', 'Failed'],
                datasets: [{
                    data: @json(array_values($payment_status_distribution)),
                    backgroundColor: [
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(251, 191, 36, 0.8)',
                        'rgba(239, 68, 68, 0.8)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Expense Categories Chart
        const expenseCategoriesCtx = document.getElementById('expenseCategoriesChart').getContext('2d');
        new Chart(expenseCategoriesCtx, {
            type: 'pie',
            data: {
                labels: @json($expense_categories_labels),
                datasets: [{
                    data: @json($expense_categories_data),
                    backgroundColor: [
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(249, 115, 22, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(234, 179, 8, 0.8)',
                        'rgba(132, 204, 22, 0.8)',
                        'rgba(16, 185, 129, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Course Enrollment Chart
        const courseEnrollmentCtx = document.getElementById('courseEnrollmentChart').getContext('2d');
        new Chart(courseEnrollmentCtx, {
            type: 'bar',
            data: {
                labels: @json($course_enrollment_labels),
                datasets: [{
                    label: 'Students Enrolled',
                    data: @json($course_enrollment_data),
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endsection
