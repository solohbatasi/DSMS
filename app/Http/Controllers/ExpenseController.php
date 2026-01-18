<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Base query with branch filtering
        $expensesQuery = Expense::with(['category', 'user']);

        // Apply branch filtering based on user role and branch selection
        if ($user->isAdmin()) {
            // Admin: Check if viewing a specific branch or all branches
            $selectedBranchId = session('admin_selected_branch');

            if ($selectedBranchId) {
                // Admin is viewing a specific branch
                $expensesQuery->where('branch_id', $selectedBranchId);
            }
            // If $selectedBranchId is null/empty, admin is viewing all branches (no additional filter)
        } else {
            // Non-admin users: only show expenses from their assigned branch
            $expensesQuery->where('branch_id', $user->branch_id);
        }

        // Additional branch filter from request (for admin branch switcher UI)
        if ($user->isAdmin() && $request->has('branch_id')) {
            $branchId = $request->branch_id;
            if ($branchId) {
                $expensesQuery->where('branch_id', $branchId);
            }
        }

        // Get counts with branch filtering
        $countQuery = Expense::query();

        // Apply the same branch filtering to counts
        if ($user->isAdmin()) {
            $selectedBranchId = session('admin_selected_branch');
            if ($selectedBranchId) {
                $countQuery->where('branch_id', $selectedBranchId);
            }
        } else {
            $countQuery->where('branch_id', $user->branch_id);
        }

        $totalExpenses = $countQuery->sum('amount');
        $todayExpenses = $countQuery->clone()->whereDate('expense_date', today())->sum('amount');
        $pendingExpenses = $countQuery->clone()->where('status', 'pending')->count();
        $approvedExpenses = $countQuery->clone()->where('status', 'approved')->count();

        $recentExpenses = $expensesQuery->clone()
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        $expenses = $expensesQuery->orderBy('created_at', 'desc')->paginate(10);

        $categories = ExpenseCategory::orderBy('name')->get();

        // Get branches for admin filter
        $branches = $user->isAdmin() ? Branch::active()->get() : collect();

        // Get selected branch info for display
        $selectedBranch = null;
        if ($user->isAdmin()) {
            $selectedBranchId = session('admin_selected_branch');
            $selectedBranch = $selectedBranchId ? Branch::find($selectedBranchId) : null;
        }

        return view('expenses.index', [
            'expenses' => $expenses,
            'totalExpenses' => $totalExpenses,
            'todayExpenses' => $todayExpenses,
            'pendingExpenses' => $pendingExpenses,
            'approvedExpenses' => $approvedExpenses,
            'recentExpenses' => $recentExpenses,
            'categories' => $categories,
            'branches' => $branches,
            'selectedBranch' => $request->branch_id ?: ($selectedBranch ? $selectedBranch->id : null),
            'viewingBranch' => $selectedBranch
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'category_id' => 'required|exists:expense_categories,id',
                'reference' => 'nullable|string|max:255',
                'vendor' => 'required|string|max:255',
                'expense_date' => 'required|date',
                'payment_method' => 'required|string|max:255',
                'status' => 'required|in:approved,pending,rejected',
                'amount' => 'required|numeric|min:0',
                'remarks' => 'nullable|string',
                'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048'
            ]);

            // Add branch validation for non-admin users
            if (!$user->isAdmin()) {
                $validated['branch_id'] = $user->branch_id;
            } else {
                // Admin can specify branch or use selected branch
                $selectedBranchId = session('admin_selected_branch');
                if ($selectedBranchId) {
                    $validated['branch_id'] = $selectedBranchId;
                } else {
                    // If admin is viewing all branches, they must specify a branch
                    $request->validate([
                        'branch_id' => 'required|exists:branches,id'
                    ]);
                    $validated['branch_id'] = $request->branch_id;
                }
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('expense-attachments', 'public');
            }

            $expense = Expense::create([
                'user_id' => auth()->id(),
                'branch_id' => $validated['branch_id'] ?? null,
                'category_id' => $request->category_id,
                'reference' => $request->reference,
                'vendor' => $request->vendor,
                'expense_date' => $request->expense_date,
                'payment_method' => $request->payment_method,
                'status' => $request->status,
                'amount' => $request->amount,
                'remarks' => $request->remarks,
                'attachment' => $attachmentPath
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Expense recorded successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Expense store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Try again.',
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = Auth::user();
        $expense = Expense::with(['category', 'branch'])->findOrFail($id);

        // Check if user has permission to edit this expense
        if (!$user->isAdmin() && $expense->branch_id != $user->branch_id) {
            abort(403, 'You do not have permission to edit this expense.');
        }

        return response()->json([
            'expense' => $expense
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $expense = Expense::findOrFail($id);

        // Check if user has permission to update this expense
        if (!$user->isAdmin() && $expense->branch_id != $user->branch_id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update this expense.'
            ], 403);
        }

        try {
            $validated = $request->validate([
                'category_id' => 'required|exists:expense_categories,id',
                'reference' => 'nullable|string|max:255',
                'vendor' => 'required|string|max:255',
                'expense_date' => 'required|date',
                'payment_method' => 'required|string|max:255',
                'status' => 'required|in:approved,pending,rejected',
                'amount' => 'required|numeric|min:0',
                'remarks' => 'nullable|string',
                'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $updateData = [
                'category_id' => $request->category_id,
                'reference' => $request->reference,
                'vendor' => $request->vendor,
                'expense_date' => $request->expense_date,
                'payment_method' => $request->payment_method,
                'status' => $request->status,
                'amount' => $request->amount,
                'remarks' => $request->remarks,
            ];

            // Only admin can change branch
            if ($user->isAdmin() && $request->has('branch_id')) {
                $updateData['branch_id'] = $request->branch_id;
            }

            if ($request->hasFile('attachment')) {
                // Delete old attachment if exists
                if ($expense->attachment) {
                    \Storage::disk('public')->delete($expense->attachment);
                }
                $updateData['attachment'] = $request->file('attachment')->store('expense-attachments', 'public');
            }

            $expense->update($updateData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Expense updated successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Expense update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $expense = Expense::findOrFail($id);

        // Check if user has permission to delete this expense
        if (!$user->isAdmin() && $expense->branch_id != $user->branch_id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete this expense.'
            ], 403);
        }

        try {
            DB::beginTransaction();

            // Delete attachment if exists
            if ($expense->attachment) {
                \Storage::disk('public')->delete($expense->attachment);
            }

            $expense->delete();

            DB::commit();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Expense delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete expense. ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Search expenses for table filtering
     */
    public function search(Request $request)
    {
        try {
            $user = Auth::user();
            $query = $request->get('q', '');
            $status = $request->get('status', 'all');
            $date = $request->get('date', 'all');
            $category = $request->get('category', 'all');
            $sort = $request->get('sort', 'newest');

            // Start with base query
            $expensesQuery = Expense::with(['category', 'user', 'branch']);

            // Apply branch filtering based on user role and branch selection
            if ($user->isAdmin()) {
                // Admin: Check if viewing a specific branch or all branches
                $selectedBranchId = session('admin_selected_branch');

                if ($selectedBranchId) {
                    // Admin is viewing a specific branch
                    $expensesQuery->where('branch_id', $selectedBranchId);
                }
                // If $selectedBranchId is null/empty, admin is viewing all branches (no additional filter)
            } else {
                // Non-admin users: only show expenses from their assigned branch
                $expensesQuery->where('branch_id', $user->branch_id);
            }

            // Apply search filter
            if (!empty($query)) {
                $expensesQuery->where(function ($q) use ($query) {
                    $q->where('reference', 'like', "%{$query}%")
                      ->orWhere('vendor', 'like', "%{$query}%")
                      ->orWhere('remarks', 'like', "%{$query}%")
                      ->orWhereHas('category', function ($q) use ($query) {
                          $q->where('name', 'like', "%{$query}%");
                      });
                });
            }

            // Apply status filter
            if ($status !== 'all') {
                $expensesQuery->where('status', $status);
            }

            // Apply category filter
            if ($category !== 'all') {
                $expensesQuery->where('category_id', $category);
            }

            // Apply date filter
            if ($date !== 'all') {
                $now = Carbon::now();
                switch ($date) {
                    case 'today':
                        $expensesQuery->whereDate('expense_date', $now->toDateString());
                        break;
                    case 'week':
                        $expensesQuery->whereBetween('expense_date', [
                            $now->startOfWeek()->toDateString(),
                            $now->endOfWeek()->toDateString()
                        ]);
                        break;
                    case 'month':
                        $expensesQuery->whereBetween('expense_date', [
                            $now->startOfMonth()->toDateString(),
                            $now->endOfMonth()->toDateString()
                        ]);
                        break;
                }
            }

            // Apply sorting
            switch ($sort) {
                case 'oldest':
                    $expensesQuery->orderBy('created_at', 'asc');
                    break;
                case 'amount_high':
                    $expensesQuery->orderBy('amount', 'desc');
                    break;
                case 'amount_low':
                    $expensesQuery->orderBy('amount', 'asc');
                    break;
                case 'vendor_asc':
                    $expensesQuery->orderBy('vendor', 'asc');
                    break;
                case 'vendor_desc':
                    $expensesQuery->orderBy('vendor', 'desc');
                    break;
                default: // newest
                    $expensesQuery->orderBy('created_at', 'desc');
                    break;
            }

            $expenses = $expensesQuery->limit(100)
                ->get()
                ->map(function($expense) use ($user) {
                    // Additional security check
                    if ($user->isAdmin()) {
                        $selectedBranchId = session('admin_selected_branch');
                        if ($selectedBranchId && $expense->branch_id != $selectedBranchId) {
                            return null; // Skip expenses from other branches when admin is viewing a specific branch
                        }
                    } elseif (!$user->isAdmin() && $expense->branch_id != $user->branch_id) {
                        return null; // Skip expenses from other branches for non-admin users
                    }

                    return [
                        'id' => $expense->id,
                        'category_name' => $expense->category->name,
                        'reference' => $expense->reference,
                        'vendor' => $expense->vendor,
                        'amount' => number_format($expense->amount, 2),
                        'expense_date' => $expense->expense_date ? $expense->expense_date->format('M d, Y') : 'N/A',
                        'payment_method' => $expense->payment_method,
                        'status' => $expense->status,
                        'remarks' => $expense->remarks,
                        'attachment' => $expense->attachment,
                        'recorded_by' => $expense->user->name ?? 'System',
                        'branch_name' => $expense->branch->name ?? 'N/A',
                        'has_attachment' => !empty($expense->attachment),
                        'can_edit' => $user->isAdmin() || $expense->branch_id == $user->branch_id,
                        'can_delete' => $user->isAdmin() || $expense->branch_id == $user->branch_id
                    ];
                })
                ->filter() // Remove null entries
                ->values(); // Reindex array

            return response()->json([
                'success' => true,
                'expenses' => $expenses,
                'count' => $expenses->count(),
                'has_more' => $expenses->count() >= 100,
                'branch_context' => $user->isAdmin() ? session('admin_selected_branch_name', 'All Branches') : ($user->branch->name ?? 'N/A')
            ]);

        } catch (\Exception $e) {
            \Log::error('Expense search error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Search failed. Please try again.',
                'expenses' => []
            ], 500);
        }
    }
}
