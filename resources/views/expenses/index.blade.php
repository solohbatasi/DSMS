@extends('layouts.app')

@section('content')
@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Storage;

@endphp

@can('view expenses')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    @php
    $actionButtons = [];

    if (auth()->user()->can('add expenses')) {
        $actionButtons[] = [
            'text' => 'Add Expense',
            'onclick' => 'openExpenseModal()',
            'icon' => 'fas fa-plus',
            'color' => 'bg-green-600 hover:bg-green-700'
        ];
    }
    @endphp

    @include('components.dashboard-header', [
        'title' => 'Expenses Management',
        'subtitle' => 'Manage all business expenses and transactions',
        'actionButtons' => $actionButtons
    ])

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <!-- Total Expenses -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Expenses</p>
                        <p class="text-2xl font-semibold text-gray-900">KSh {{ number_format($totalExpenses, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Today's Expenses -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-day text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Today's Expenses</p>
                        <p class="text-2xl font-semibold text-gray-900">KSh {{ number_format($todayExpenses, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Pending Expenses -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Pending Expenses</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $pendingExpenses }}</p>
                    </div>
                </div>
            </div>

            <!-- Approved Expenses -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Approved Expenses</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $approvedExpenses }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Quick Actions</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- All Expenses -->
                <a href="{{ route('expenses.index') }}"
                class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center hover:bg-blue-100 transition duration-200 group">
                    <i class="fas fa-list text-blue-600 text-xl mb-2 group-hover:text-blue-700"></i>
                    <p class="text-sm font-medium text-blue-700">All Expenses</p>
                    <p class="text-xs text-blue-600 mt-1">View all expenses</p>
                </a>

                <!-- Add Expense -->
                <a href="javascript:void(0)"
                onclick="openExpenseModal()"
                class="bg-green-50 border border-green-200 rounded-lg p-4 text-center hover:bg-green-100 transition duration-200 group">
                    <i class="fas fa-plus text-green-600 text-xl mb-2 group-hover:text-green-700"></i>
                    <p class="text-sm font-medium text-green-700">Add Expense</p>
                    <p class="text-xs text-green-600 mt-1">Record new expense</p>
                </a>

                <!-- Expense Reports -->
                <a href="#"
                class="bg-purple-50 border border-purple-200 rounded-lg p-4 text-center hover:bg-purple-100 transition duration-200 group">
                    <i class="fas fa-chart-bar text-purple-600 text-xl mb-2 group-hover:text-purple-700"></i>
                    <p class="text-sm font-medium text-purple-700">Reports</p>
                    <p class="text-xs text-purple-600 mt-1">Expense analytics</p>
                </a>

                <!-- Export Expenses -->
                <a href="#"
                class="bg-orange-50 border border-orange-200 rounded-lg p-4 text-center hover:bg-orange-100 transition duration-200 group">
                    <i class="fas fa-download text-orange-600 text-xl mb-2 group-hover:text-orange-700"></i>
                    <p class="text-sm font-medium text-orange-700">Export</p>
                    <p class="text-xs text-orange-600 mt-1">Export expenses data</p>
                </a>
            </div>
        </div>

        <!-- Recent Expenses -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Recent Expenses</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($recentExpenses as $expense)
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition duration-200">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-gray-800">{{ $expense->vendor }}</h3>
                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Approved</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">{{ $expense->category->name }}</p>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-red-600 font-semibold">KSh {{ number_format($expense->amount, 2) }}</span>
                        <span class="text-gray-500">{{ $expense->expense_date ? \Carbon\Carbon::parse($expense->expense_date)->format('M d, Y') : 'N/A' }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Expenses Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800">All Expenses</h2>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 p-6">
                <!-- SEARCH BAR -->
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <input type="text"
                        id="expenseSearch"
                        placeholder="Search by vendor, reference, category..."
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
                        {{ $expenses->total() }} expenses
                    </span>

                    <!-- Filters -->
                    <div class="flex space-x-2">
                        <!-- STATUS FILTER -->
                        <select id="statusFilter" onchange="filterExpenses()"
                                class="border border-gray-300 rounded-lg px-3 py-2 text-sm
                                    focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="all">All Status</option>
                            <option value="approved">Approved</option>
                            <option value="pending">Pending</option>
                            <option value="rejected">Rejected</option>
                        </select>

                        <!-- CATEGORY FILTER -->
                        <select id="categoryFilter" onchange="filterExpenses()"
                                class="border border-gray-300 rounded-lg px-3 py-2 text-sm
                                    focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="all">All Categories</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>

                        <!-- DATE FILTER -->
                        <select id="dateFilter" onchange="filterExpenses()"
                                class="border border-gray-300 rounded-lg px-3 py-2 text-sm
                                    focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="all">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                        </select>

                        <!-- SORT FILTER -->
                        <select id="sortFilter" onchange="filterExpenses()"
                                class="border border-gray-300 rounded-lg px-3 py-2 text-sm
                                    focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="amount_high">Amount: High to Low</option>
                            <option value="amount_low">Amount: Low to High</option>
                            <option value="vendor_asc">Vendor: A-Z</option>
                            <option value="vendor_desc">Vendor: Z-A</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Expenses Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left">Category</th>
                            <th class="px-4 py-3 text-left">Vendor</th>
                            <th class="px-4 py-3 text-left">Reference</th>
                            <th class="px-4 py-3 text-left">Amount</th>
                            <th class="px-4 py-3 text-left">Date</th>
                            <th class="px-4 py-3 text-left">Payment Method</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Attachment</th>
                            <th class="px-4 py-3 text-left">Recorded By</th>
                            <th class="px-4 py-3 text-left">Actions</th>
                        </tr>
                    </thead>

                    <tbody id="expensesTableBody">
                        @foreach($expenses as $expense)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-tag text-blue-600 text-sm"></i>
                                    </div>
                                    <span class="font-medium text-gray-800">{{ $expense->category->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-gray-800">{{ $expense->vendor }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-mono text-sm text-gray-700">{{ $expense->reference ?? 'N/A' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-semibold text-red-600">KSh {{ number_format($expense->amount, 2) }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $expense->expense_date ? \Carbon\Carbon::parse($expense->expense_date)->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $expense->payment_method ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($expense->status == 'approved')
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">Approved</span>
                                @elseif($expense->status == 'pending')
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-medium">Pending</span>
                                @else
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">Rejected</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($expense->attachment)
                                <a href="{{ Storage::url($expense->attachment) }}" target="_blank"
                                   class="text-blue-600 hover:text-blue-800 text-sm">
                                    <i class="fas fa-paperclip"></i> View
                                </a>
                                @else
                                <span class="text-gray-400 text-sm">No file</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $expense->user->name ?? 'System' }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    @can('edit expenses')
                                    <button onclick="openEditModal({{ $expense->id }})"
                                        class="text-blue-600 hover:text-blue-800 text-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    @endcan
                                    @can('delete expenses')
                                    <button onclick="openDeleteModal({{ $expense->id }})"
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
                    {{ $expenses->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Expense Modal -->
<div id="expenseModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 relative max-h-[90vh] overflow-y-auto">
        <button onclick="closeExpenseModal()" class="absolute top-3 right-3 text-gray-600 hover:text-black">
            <i class="fas fa-times text-xl"></i>
        </button>

        <h2 class="text-xl font-semibold text-gray-800 mb-4">Record New Expense</h2>

        <form id="expenseForm" method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- Category and Vendor -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="text-sm font-medium">Category *</label>
                    <select name="category_id" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium">Vendor *</label>
                    <input type="text" name="vendor" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" placeholder="Vendor name" required>
                </div>
            </div>

            <!-- Reference and Amount -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="text-sm font-medium">Reference Number</label>
                    <input type="text" name="reference" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" placeholder="Expense reference">
                </div>

                <div>
                    <label class="text-sm font-medium">Amount (KSh) *</label>
                    <input type="number" name="amount" step="0.01" min="0" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
                </div>
            </div>

            <!-- Date and Payment Method -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="text-sm font-medium">Expense Date *</label>
                    <input type="date" name="expense_date" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" value="{{ date('Y-m-d') }}" required>
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

            <!-- Status and Attachment -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="text-sm font-medium">Status *</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
                        <option value="approved">Approved</option>
                        <option value="pending">Pending</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium">Attachment</label>
                    <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Supported: JPG, PNG, PDF, DOC (Max: 2MB)</p>
                </div>
            </div>

            <div class="mb-6">
                <label class="text-sm font-medium">Remarks</label>
                <textarea name="remarks" rows="3" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" placeholder="Additional notes about this expense..."></textarea>
            </div>

            <button type="submit" class="w-full bg-green-600 text-white p-3 rounded-lg hover:bg-green-700 transition duration-200 font-semibold">
                Record Expense
            </button>
        </form>
    </div>
</div>

<!-- Edit Expense Modal -->
<div id="editExpenseModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 relative max-h-[90vh] overflow-y-auto">
        <button onclick="closeEditModal()" class="absolute top-3 right-3 text-gray-600 hover:text-black">
            <i class="fas fa-times text-xl"></i>
        </button>

        <h2 class="text-xl font-semibold text-gray-800 mb-4">Edit Expense</h2>

        <form id="editExpenseForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="expense_id" id="edit_expense_id">

            <!-- Category and Vendor -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="text-sm font-medium">Category *</label>
                    <select name="category_id" id="edit_category_id" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium">Vendor *</label>
                    <input type="text" name="vendor" id="edit_vendor" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
                </div>
            </div>

            <!-- Reference and Amount -->
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

            <!-- Date and Payment Method -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="text-sm font-medium">Expense Date *</label>
                    <input type="date" name="expense_date" id="edit_expense_date" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
                </div>

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
            </div>

            <!-- Status and Attachment -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="text-sm font-medium">Status *</label>
                    <select name="status" id="edit_status" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
                        <option value="approved">Approved</option>
                        <option value="pending">Pending</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium">Attachment</label>
                    <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Leave empty to keep current file</p>
                    <div id="currentAttachment" class="mt-2"></div>
                </div>
            </div>

            <div class="mb-4">
                <label class="text-sm font-medium">Remarks</label>
                <textarea name="remarks" id="edit_remarks" rows="3" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition duration-200 font-semibold">
                Update Expense
            </button>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <h2 class="text-lg font-semibold text-gray-800 mb-3">Confirm Delete</h2>
        <p class="text-gray-600 mb-5">Are you sure you want to delete this expense record? This action cannot be undone.</p>
        <input type="hidden" id="deleteExpenseId">
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
// Expense Modal Functions
function openExpenseModal() {
    document.getElementById('expenseModal').classList.remove('hidden');
}

function closeExpenseModal() {
    document.getElementById('expenseModal').classList.add('hidden');
    document.getElementById('expenseForm').reset();
}

// Edit Modal Functions
function openEditModal(expenseId) {
    fetch(`/expenses/${expenseId}/edit`)
        .then(res => res.json())
        .then(data => {
            const expense = data.expense;

            document.getElementById('edit_expense_id').value = expense.id;
            document.getElementById('edit_category_id').value = expense.category_id;
            document.getElementById('edit_vendor').value = expense.vendor;
            document.getElementById('edit_reference').value = expense.reference || '';
            document.getElementById('edit_amount').value = expense.amount;
            document.getElementById('edit_expense_date').value = expense.expense_date;
            document.getElementById('edit_payment_method').value = expense.payment_method;
            document.getElementById('edit_status').value = expense.status;
            document.getElementById('edit_remarks').value = expense.remarks || '';

            // Show current attachment if exists
            const currentAttachmentDiv = document.getElementById('currentAttachment');
            if (expense.attachment) {
                currentAttachmentDiv.innerHTML = `
                    <p class="text-xs text-gray-600 mb-1">Current Attachment:</p>
                    <a href="/storage/${expense.attachment}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                        <i class="fas fa-paperclip mr-1"></i>View Current File
                    </a>
                `;
            } else {
                currentAttachmentDiv.innerHTML = '<p class="text-xs text-gray-600">No attachment uploaded</p>';
            }

            document.getElementById('editExpenseModal').classList.remove('hidden');
        })
        .catch(err => {
            console.error('Error loading expense:', err);
            toastr.error("Failed to load expense details");
        });
}

function closeEditModal() {
    document.getElementById('editExpenseModal').classList.add('hidden');
}

// Delete Modal Functions
function openDeleteModal(expenseId) {
    document.getElementById('deleteExpenseId').value = expenseId;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Form Submissions
document.getElementById('expenseForm').addEventListener('submit', function (e) {
    e.preventDefault();

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
            toastr.success(data.message || "Expense recorded successfully!");
            closeExpenseModal();
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

document.getElementById('editExpenseForm').addEventListener('submit', function(e){
    e.preventDefault();

    const expenseId = document.getElementById('edit_expense_id').value;
    const formData = new FormData(this);

    fetch(`/expenses/${expenseId}`, {
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
            toastr.success(data.message || "Expense updated successfully");
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
    const expenseId = document.getElementById('deleteExpenseId').value;

    fetch(`/expenses/${expenseId}`, {
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
            toastr.success("Expense deleted successfully");
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
let searchTimeout;

function filterExpenses() {
    const q = document.getElementById("expenseSearch").value.trim();
    const status = document.getElementById("statusFilter").value;
    const category = document.getElementById("categoryFilter").value;
    const date = document.getElementById("dateFilter").value;
    const sort = document.getElementById("sortFilter").value;

    // Client-side length limit
    if (q.length > 100) {
        const tbody = document.getElementById("expensesTableBody");
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
    const tbody = document.getElementById("expensesTableBody");
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
    if (category !== 'all') params.append('category', category);
    if (date !== 'all') params.append('date', date);
    if (sort !== 'newest') params.append('sort', sort);

    const url = `/expenses/search?${params.toString()}`;

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
            renderExpenses(data.expenses, data.has_more);
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

document.getElementById("expenseSearch").addEventListener("keyup", function () {
    clearTimeout(searchTimeout);
    const query = this.value.trim();

    if (query === '' || query.length >= 2) {
        searchTimeout = setTimeout(() => filterExpenses(), 400);
    }
});

// Also trigger filter when status or sort changes
document.getElementById("statusFilter").addEventListener("change", filterExpenses);
document.getElementById("categoryFilter").addEventListener("change", filterExpenses);
document.getElementById("dateFilter").addEventListener("change", filterExpenses);
document.getElementById("sortFilter").addEventListener("change", filterExpenses);

function renderExpenses(expenses, hasMore = false) {
    const tbody = document.getElementById("expensesTableBody");

    if (!tbody) {
        console.error('expensesTableBody not found');
        return;
    }

    tbody.innerHTML = "";

    if (!expenses || expenses.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center py-8 text-gray-500">
                    <i class="fas fa-search mr-2"></i>No expenses found matching your search criteria
                </td>
            </tr>
        `;
        return;
    }

    const canEdit = {{ auth()->user()->can('edit expenses') ? 'true' : 'false' }};
    const canDelete = {{ auth()->user()->can('delete expenses') ? 'true' : 'false' }};

    expenses.forEach(expense => {
        const statusClass = expense.status === 'approved' ? 'bg-green-100 text-green-800' :
                           expense.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                           'bg-red-100 text-red-800';

        const statusText = expense.status === 'approved' ? 'Approved' :
                          expense.status === 'pending' ? 'Pending' : 'Rejected';

        tbody.innerHTML += `
            <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-3">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-tag text-blue-600 text-sm"></i>
                        </div>
                        <span class="font-medium text-gray-800">${expense.category_name}</span>
                    </div>
                </td>
                <td class="px-4 py-3">
                    <span class="text-gray-800">${expense.vendor}</span>
                </td>
                <td class="px-4 py-3">
                    <span class="font-mono text-sm text-gray-700">${expense.reference || 'N/A'}</span>
                </td>
                <td class="px-4 py-3">
                    <span class="font-semibold text-red-600">KSh ${expense.amount}</span>
                </td>
                <td class="px-4 py-3 text-sm text-gray-600">
                    ${expense.expense_date}
                </td>
                <td class="px-4 py-3 text-sm text-gray-600">
                    ${expense.payment_method || 'N/A'}
                </td>
                <td class="px-4 py-3">
                    <span class="${statusClass} px-2 py-1 rounded-full text-xs font-medium">${statusText}</span>
                </td>
                <td class="px-4 py-3">
                    ${expense.has_attachment ? `
                        <a href="/storage/${expense.attachment}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                            <i class="fas fa-paperclip"></i> View
                        </a>
                    ` : '<span class="text-gray-400 text-sm">No file</span>'}
                </td>
                <td class="px-4 py-3 text-sm text-gray-600">
                    ${expense.recorded_by}
                </td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        ${canEdit ? `
                            <button onclick="openEditModal(${expense.id})" class="text-blue-600 hover:text-blue-800 text-sm">
                                <i class="fas fa-edit"></i> Edit
                            </button>` : ''}
                        ${canDelete ? `
                            <button onclick="openDeleteModal(${expense.id})" class="text-red-600 hover:text-red-800 text-sm">
                                <i class="fas fa-trash"></i> Delete
                            </button>` : ''}
                    </div>
                </td>
            </tr>
        `;
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

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('Expenses page loaded');
});
</script>

@endsection
@endcan
