@extends('layouts.app')

@section('content')
@php
    use Illuminate\Support\Facades\Auth;
@endphp

@can('view students')
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
    <!-- Header Section -->
    @php
    $actionButtons = [];

    if (auth()->user()->can('add students')) {
        $actionButtons[] = [
            'text' => 'Add Student',
            'onclick' => 'openEnrollmentModal()',
            'icon' => 'fas fa-plus',
            'color' => 'bg-green-600 hover:bg-green-700'
        ];
    }
    @endphp

    @include('components.dashboard-header', [
        'title' => 'Learners Corner',
        'subtitle' => 'Manage all your learners in this corner',
        'actionButtons' => $actionButtons
    ])

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">

            <!-- Total Students -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Students</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $totalStudents }}</p>
                    </div>
                </div>
            </div>

            <!-- Active Students -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-check text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Active Students</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $activeStudents }}</p>
                    </div>
                </div>
            </div>

            <!-- Suspended Students -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-times text-red-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Suspended Students</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $suspendedStudents }}</p>
                    </div>
                </div>
            </div>

            <!-- Alumni Students -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Alumni Students</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $alumniStudents }}</p>
                    </div>
                </div>
            </div>

        </div>


        <!-- Quick Actions -->
        @can('add students')
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Quick Actions</h2>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

                <!-- Applications -->
                <a href="#"
                class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center hover:bg-blue-100 transition duration-200 group">
                    <i class="fas fa-folder-open text-blue-600 text-xl mb-2 group-hover:text-blue-700"></i>
                    <p class="text-sm font-medium text-blue-700">Applications</p>
                    <p class="text-xs text-blue-600 mt-1">View applications</p>
                </a>

                <!-- Enrollment -->
                <a href="javascript:void(0)"
                onclick="openEnrollmentModal()"
                class="bg-green-50 border border-green-200 rounded-lg p-4 text-center hover:bg-green-100 transition duration-200 group">
                    <i class="fas fa-user-check text-green-600 text-xl mb-2 group-hover:text-green-700"></i>
                    <p class="text-sm font-medium text-green-700">Enrollment</p>
                    <p class="text-xs text-green-600 mt-1">Enroll students</p>
                </a>


                <!-- Students List -->
                <a href="{{ route('students.index') }}"
                class="bg-purple-50 border border-purple-200 rounded-lg p-4 text-center hover:bg-purple-100 transition duration-200 group">
                    <i class="fas fa-users text-purple-600 text-xl mb-2 group-hover:text-purple-700"></i>
                    <p class="text-sm font-medium text-purple-700">Students</p>
                    <p class="text-xs text-purple-600 mt-1">Manage students</p>
                </a>

                <!-- Payments -->
                <a href="{{ route('payments.index') }}"
                class="bg-orange-50 border border-orange-200 rounded-lg p-4 text-center hover:bg-orange-100 transition duration-200 group">
                    <i class="fas fa-credit-card text-orange-600 text-xl mb-2 group-hover:text-orange-700"></i>
                    <p class="text-sm font-medium text-orange-700">Payments</p>
                    <p class="text-xs text-orange-600 mt-1">View payments</p>
                </a>

            </div>
        </div>
        @endcan

        <!--  Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800">Recent Learners</h2>

                </div>
            </div>

            <!-- Add this after the Section header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">

                <!-- SEARCH BAR -->
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <input type="text"
                        id="studentSearch"
                        placeholder="Search students by name, ID number, contact..."
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
                        {{ $students->total() }} learners
                    </span>

                    <!-- Filters -->
                    <div class="flex space-x-2">

                    <div class="flex items-center space-x-4">
                        <!-- Count -->
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                            {{ $students->total() }} learners
                        </span>

                        @if(auth()->user()->isAdmin())
                        <!-- Branch Filter for Admin -->
                        <select id="branchFilter" onchange="filterByBranch()"
                                class="border border-gray-300 rounded-lg px-3 py-2 text-sm
                                    focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Branches</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ $selectedBranch == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                        @endif

                        <!-- STATUS FILTER -->
                        <select id="statusFilter" onchange="filterStudents()"
                                class="border border-gray-300 rounded-lg px-3 py-2 text-sm
                                    focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="all">All Status</option>
                            <option value="active">Active</option>
                            <option value="completed">Completed</option>
                            <option value="dropped">Dropped</option>
                        </select>

                        <!-- SORT FILTER -->
                        <select id="sortFilter" onchange="filterStudents()"
                                class="border border-gray-300 rounded-lg px-3 py-2 text-sm
                                    focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="fee_high">Fee: High to Low</option>
                            <option value="fee_low">Fee: Low to High</option>
                        </select>
                    </div>



                    </div>
                </div>
            </div>

            <!-- Add search results container -->
            <div id="searchResults" class="hidden mb-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div id="searchResultsContent"></div>
            </div>

            <!-- Students Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-2">Name</th>
                            @if(auth()->user()->isAdmin())
                            <th class="px-4 py-2">Branch</th>
                            @endif
                            <th class="px-4 py-2">Course</th>
                            <th class="px-4 py-2">Reg No</th>
                            <th class="px-4 py-2">Fees</th>

                            <th class="px-4 py-2">Actions</th>
                        </tr>
                    </thead>

                    <tbody id="studentsTableBody">
                        @foreach($students as $ef)
                        @php
                            $totalPaid = $ef->payments->sum('amount');
                            $balance = $ef->total_fee - $totalPaid;
                        @endphp
                        <tr class="border-b">
                            <td class="px-4 py-2">{{ $ef->student->name ?? ' '}}</td>
                            @if(auth()->user()->isAdmin())
                            <td class="px-4 py-2">
                                {{ $ef->student->branch->name ?? '-' }}
                            </td>
                            @endif
                            <td class="px-4 py-2">{{ $ef->course->course }}</td>
                            <td class="px-4 py-2">{{ $ef->ef_no }}</td>
                            <td class="px-4 py-2">
                                <div class="flex flex-col space-y-1">
                                    <span>Total: {{ number_format($ef->total_fee, 2) }}</span>
                                    @can('view payments')
                                    <span class="text-green-600">Paid: {{ number_format($totalPaid, 2) }}</span>
                                    <span class="text-red-600">Balance: {{ number_format($balance, 2) }}</span>
                                    @endcan
                                </div>
                            </td>

                            <td class="px-4 py-2 flex gap-2">
                                <!-- Edit -->
                                @can('edit students')
                                <button onclick="openEditModal({{ $ef->id }})"
                                    class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-edit"></i> Edit
                                </button>

                                
                                @endcan
                                <a href="{{ route('admin.students.tickets', ['student' => $ef->student->id]) }}"
                                    class="text-green-600 hover:text-green-800">
                                    <i class="fas fa-ticket-alt"></i> Tickets
                                </a>
                                @can('delete students')
                                <button onclick="openDeleteModal({{ $ef->id }})"
                                    class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                                @endcan
                            </td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $students->links() }}
                </div>

            </div>


        </div>
    </div>
</div>

<!-- Enrollment Modal -->
<div id="enrollmentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 relative max-h-[90vh] overflow-y-auto">

        <!-- Close Button -->
        <button onclick="closeEnrollmentModal()" class="absolute top-3 right-3 text-gray-600 hover:text-black">
            <i class="fas fa-times text-xl"></i>
        </button>

        <h2 class="text-xl font-semibold text-gray-800 mb-4">Learner Enrollment</h2>

        <form id="enrollmentForm">
             <!-- BRANCH SELECTION -->
            <div class="mb-4">
                <label class="text-sm font-medium">Branch</label>
                @if(auth()->user()->isAdmin())
                    <select name="branch_id" id="branchSelect" class="w-full border rounded p-2" required>
                        <option value="">-- Select Branch --</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                @else
                    <!-- For non-admin users, show their branch as readonly -->
                    <input type="hidden" name="branch_id" value="{{ auth()->user()->branch_id }}">
                    <input type="text"
                        value="{{ auth()->user()->branch->name ?? 'No Branch' }}"
                        class="w-full border rounded p-2 bg-gray-100"
                        readonly>
                @endif
            </div>

            <!-- STUDENT BASIC INFO -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

                <div>
                    <label class="text-sm font-medium">ID / Birth / Passport Number</label>
                    <input type="text" name="id_no" class="w-full border rounded p-2" required>
                </div>

                <div>
                    <label class="text-sm font-medium">Full Name</label>
                    <input type="text" name="name" class="w-full border rounded p-2" required>
                </div>

                <div>
                    <label class="text-sm font-medium">Contact</label>
                    <input type="text" name="contact" class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="text-sm font-medium">Email</label>
                    <input type="email" name="email" class="w-full border rounded p-2">
                </div>
            </div>

            <!-- ADDRESS -->
            <div class="mb-4">
                <label class="text-sm font-medium">Address</label>
                <input type="text" name="address" class="w-full border rounded p-2">
            </div>

            <!-- EF NUMBER -->
            <div class="mb-4">
                <label class="text-sm font-medium">ADM / Reg /EF Number</label>
                <input type="text" name="ef_no" class="w-full border rounded p-2" required>
            </div>

            <!-- COURSE -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

                <div>
                    <label class="text-sm font-medium">Course</label>
                    <select name="course_id" id="courseSelect"
                            class="w-full border rounded p-2" required>
                        <option value="">-- Select Branch First --</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}"
                                    data-fee="{{ $course->total_amount }}"
                                    data-duration="{{ $course->practical_sessions }}"
                                    data-branch="{{ $course->branch_id }}">
                                {{ $course->course }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium">Fee (Auto)</label>
                    <input type="number" name="total_fee" id="courseFee"
                        class="w-full border rounded p-2">
                </div>
            </div>

            <!-- TOTAL CLASSES (Auto from DB but editable) -->
            <div class="mb-4">
                <label class="text-sm font-medium">Number of Practical Classes</label>
                <input type="number" name="total_class" id="totalClass"
                    class="w-full border rounded p-2" required>
            </div>


            <!-- Hidden Fields -->
            <input type="hidden" name="type" value="3">

            <button type="submit"
                    class="w-full bg-green-600 text-white p-3 rounded hover:bg-green-700">
                Enroll Student
            </button>

        </form>
    </div>
</div>
<div id="editStudentModal"
     class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 relative max-h-[90vh] overflow-y-auto">

        <button onclick="closeEditModal()" class="absolute top-3 right-3 text-gray-600 hover:text-black">
            <i class="fas fa-times text-xl"></i>
        </button>

        <h2 class="text-xl font-semibold mb-4">Edit Student Enrollment</h2>

        <form id="editStudentForm">

            <input type="hidden" name="ef_id" id="edit_ef_id">

            <!-- BRANCH FIELD -->
            <div class="mb-4">
                <label class="text-sm font-medium">Branch</label>
                @if(auth()->user()->isAdmin())
                    <select name="branch_id" id="edit_branch_id" class="w-full border rounded p-2">
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                @else
                    <!-- For non-admin users, show readonly -->
                    <input type="hidden" name="branch_id" id="edit_branch_id" value="">
                    <input type="text" id="edit_branch_display"
                        class="w-full border rounded p-2 bg-gray-100" readonly>
                @endif
            </div>

            <!-- BASIC INFO -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="text-sm font-medium">ID Number</label>
                    <input type="text" name="id_no" id="edit_id_no" class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="text-sm font-medium">Full Name</label>
                    <input type="text" name="name" id="edit_name" class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="text-sm font-medium">Contact</label>
                    <input type="text" name="contact" id="edit_contact" class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="text-sm font-medium">Email</label>
                    <input type="email" name="email" id="edit_email" class="w-full border rounded p-2">
                </div>
            </div>

            <!-- ADDRESS -->
            <div class="mb-4">
                <label class="text-sm font-medium">Address</label>
                <input type="text" name="address" id="edit_address" class="w-full border rounded p-2">
            </div>

            <!-- EF NUMBER -->
            <div class="mb-4">
                <label class="text-sm font-medium">EF Number</label>
                <input type="text" name="ef_no" id="edit_ef_no" class="w-full border rounded p-2">
            </div>

            <!-- COURSE -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="text-sm font-medium">Course</label>
                    <select id="edit_course_id" name="course_id" class="w-full border rounded p-2">
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}"
                                    data-fee="{{ $course->total_amount }}"
                                    data-duration="{{ $course->total_sessions }}">
                                {{ $course->course }}
                            </option>
                        @endforeach
                    </select>

                </div>

                <div>
                    <label class="text-sm font-medium">Total Fee</label>
                    <input type="number" name="total_fee" id="edit_total_fee"
                           class="w-full border rounded p-2">
                </div>
            </div>

            <!-- TOTAL CLASSES -->
            <div class="mb-4">
                <label class="text-sm font-medium">Number of Classes</label>
                <input type="number" name="total_class" id="edit_total_class"
                       class="w-full border rounded p-2">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white p-3 rounded hover:bg-blue-700">
                Save Changes
            </button>

        </form>
    </div>
</div>

<!-- DELETE CONFIRMATION MODAL -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">

        <h2 class="text-lg font-semibold text-gray-800 mb-3">Confirm Delete</h2>
        <p class="text-gray-600 mb-5">Are you sure you want to delete this learner permanently?</p>

        <input type="hidden" id="deleteEfId">

        <div class="flex justify-end gap-3">
            <button onclick="closeDeleteModal()"
                class="px-4 py-2 bg-blue-400 text-white rounded hover:bg-gray-500">
                Cancel
            </button>

            <button onclick="confirmDelete()"
                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                Delete
            </button>
        </div>

    </div>
</div>

<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function openEnrollmentModal() {
    document.getElementById('enrollmentModal').classList.remove('hidden');
}

function closeEnrollmentModal() {
    document.getElementById('enrollmentModal').classList.add('hidden');
}

// Autofill fee when course is selected
document.getElementById('courseSelect').addEventListener('change', function () {
    let fee = this.selectedOptions[0].getAttribute('data-fee');
    let duration = this.selectedOptions[0].getAttribute('data-duration');

    document.getElementById('courseFee').value = fee ? fee : '';
    document.getElementById('totalClass').value = duration ? duration : '';
});


document.getElementById('enrollmentForm').addEventListener('submit', function (e) {
    e.preventDefault();

    let formData = new FormData(this);

    fetch("{{ route('students.store') }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: formData
    })
    .then(async res => {
        let data = await res.json();

        if (res.ok && data.success) {
            toastr.success("Student enrolled successfully!");
            closeEnrollmentModal();
            // Optional small delay before reload for a nicer user experience
            setTimeout(() => location.reload(), 600);
        }
        else if (res.status === 422) {
            // Validation errors
            let errors = data.errors;
            Object.keys(errors).forEach((key) => {
                toastr.error(errors[key][0]);
            });
        }
        else {
            toastr.error(data.message || "An unexpected error occurred.");
        }
    })
    .catch(err => {
        console.error(err);
        toastr.error("Something went wrong. Please try again.");
    });
});

</script>

<script>
// OPEN EDIT MODAL
// Branch filter for admin
function filterByBranch() {
    const branchId = document.getElementById('branchFilter').value;
    const url = new URL(window.location);

    if (branchId) {
        url.searchParams.set('branch_id', branchId);
    } else {
        url.searchParams.delete('branch_id');
    }

    window.location.href = url.toString();
}

// Update course options based on selected branch (for enrollment modal)
document.getElementById('branchSelect')?.addEventListener('change', function() {
    const branchId = this.value;
    const courseSelect = document.getElementById('courseSelect');

    if (!branchId) {
        // Reset course options
        courseSelect.innerHTML = '<option value="">-- Select Branch First --</option>';
        document.getElementById('courseFee').value = '';
        document.getElementById('totalClass').value = '';
        return;
    }

    // Fetch courses for selected branch
    fetch(`/students/branch/${branchId}/courses`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                courseSelect.innerHTML = '<option value="">-- choose course --</option>';
                data.courses.forEach(course => {
                    const option = document.createElement('option');
                    option.value = course.id;
                    option.textContent = course.course;
                    option.setAttribute('data-fee', course.total_amount);
                    option.setAttribute('data-duration', course.practical_sessions);
                    courseSelect.appendChild(option);
                });
            }
        })
        .catch(err => {
            console.error('Failed to load courses:', err);
            toastr.error('Failed to load courses for selected branch');
        });
});

// Update edit modal to populate branch info
function openEditModal(efId) {
    fetch(`/students/${efId}/edit`)
        .then(res => res.json())
        .then(data => {
            // EF fields
            document.getElementById('edit_ef_id').value = data.ef.id;
            document.getElementById('edit_ef_no').value = data.ef.ef_no;
            document.getElementById('edit_total_fee').value = data.ef.total_fee;
            document.getElementById('edit_total_class').value = data.ef.total_class;

            // Select correct course
            document.getElementById('edit_course_id').value = data.ef.course_id;

            // STUDENT fields
            document.getElementById('edit_id_no').value = data.student.id_no;
            document.getElementById('edit_name').value = data.student.name;
            document.getElementById('edit_contact').value = data.student.contact ?? '';
            document.getElementById('edit_email').value = data.student.email ?? '';
            document.getElementById('edit_address').value = data.student.address ?? '';

            // BRANCH field
            const branchId = data.student.branch_id;
            const branchSelect = document.getElementById('edit_branch_id');
            const branchDisplay = document.getElementById('edit_branch_display');

            if (branchSelect) {
                branchSelect.value = branchId;
            }

            if (branchDisplay && data.branches) {
                const branch = data.branches.find(b => b.id == branchId);
                branchDisplay.value = branch ? branch.name : 'Unknown';
            }

            document.getElementById('editStudentModal').classList.remove('hidden');
        })
        .catch(err => {
            console.error(err);
            toastr.error("Failed to load student details");
        });
}
document.getElementById('edit_course_id').addEventListener('change', function () {
    let option = this.selectedOptions[0];

    let fee = option.getAttribute('data-fee');
    let duration = option.getAttribute('data-duration');

    document.getElementById('edit_total_fee').value = fee ? fee : '';
    document.getElementById('edit_total_class').value = duration ? duration : '';
});

function closeEditModal() {
    document.getElementById('editStudentModal').classList.add('hidden');
}


// SUBMIT EDIT FORM
document.getElementById('editStudentForm').addEventListener('submit', function(e){
    e.preventDefault();

    let efId = document.getElementById('edit_ef_id').value;
    let formData = new FormData(this);

    fetch(`/students/${efId}`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "X-HTTP-Method-Override": "PUT"
        },
        body: formData
    })
    .then(async res => {
        let data = await res.json();

        if (res.ok && data.success) {
            toastr.success("Student updated successfully");
            setTimeout(() => location.reload(), 600);
        }
        else toastr.error(data.message);
    })
    .catch(() => toastr.error("Update failed"));
});


// OPEN MODAL
function openDeleteModal(efId) {
    document.getElementById('deleteEfId').value = efId;
    document.getElementById('deleteModal').classList.remove('hidden');
}

// CLOSE MODAL
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// CONFIRM DELETE
function confirmDelete() {
    let efId = document.getElementById('deleteEfId').value;

    fetch(`/students/${efId}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        }
    })
    .then(async res => {
        let data = await res.json();

        if (res.ok) {
            toastr.success("Student deleted successfully");
            closeDeleteModal();

            // Remove row smoothly (if you added row IDs)
            let row = document.getElementById(`row-${efId}`);
            if (row) row.remove();

            setTimeout(() => location.reload(), 600);
        } else {
            toastr.error(data.message || "Delete failed");
        }
    })
    .catch(() => toastr.error("Server error"));
}
// Get permissions from Blade
const canEdit = {{ auth()->user()->can('edit students') ? 'true' : 'false' }};
const canDelete = {{ auth()->user()->can('delete students') ? 'true' : 'false' }};
const canViewPayment = {{ auth()->user()->can('view payments') ? 'true' : 'false' }};
const canViewFees = {{ auth()->user()->can('view fees') ? 'true' : 'false' }};

function filterStudents() {
    const q = document.getElementById("studentSearch").value.trim();
    const status = document.getElementById("statusFilter").value;
    const sort = document.getElementById("sortFilter").value;

    fetch(`/students/search?q=${encodeURIComponent(q)}&status=${status}&sort=${sort}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) renderStudents(data.students, canEdit, canDelete);
        })
        .catch(() => toastr.error("Filter failed"));
}

// Debounce search input
let searchTimeout;
document.getElementById("studentSearch").addEventListener("keyup", function () {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => filterStudents(), 400);
});

// Render students into table
function renderStudents(students, canEdit, canDelete) {
    const tbody = document.getElementById("studentsTableBody");
    tbody.innerHTML = "";

    if (students.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-4 text-gray-500">
                    No matching students found
                </td>
            </tr>
        `;
        return;
    }

    students.forEach(s => {
        tbody.innerHTML += `
            <tr class="border-b">
                <td class="px-4 py-2">${s.student_name}</td>
                <td class="px-4 py-2">${s.course_name}</td>
                <td class="px-4 py-2">${s.ef_no}</td>
                <td class="px-4 py-2">
                    <div class="flex flex-col space-y-1">
                        <span>Total: ${s.total_fee}</span>
                        ${canViewPayment ? `
                        <span class="text-green-600">Paid: ${s.total_paid}</span>
                        <span class="text-red-600">Balance: ${s.balance}</span>
                        ` : ''}
                    </div>
                </td>
                <td class="px-4 py-2 flex gap-2">
                    ${canEdit ? `
                        <button onclick="openEditModal(${s.id})" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-edit"></i> Edit
                        </button>` : ''}

                    ${canEdit ? `
                        <a href="/admin/students/${s.id}/tickets" class="text-green-600 hover:text-green-800">
                            <i class="fas fa-ticket-alt"></i> Tickets
                        </a>` : ''}

                    ${canDelete ? `
                        <button onclick="openDeleteModal(${s.id})" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i> Delete
                        </button>` : ''}
                </td>
            </tr>
        `;
    });
}




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
