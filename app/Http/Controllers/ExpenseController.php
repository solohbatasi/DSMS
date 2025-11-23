<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $totalExpenses = Expense::sum('amount');
        $todayExpenses = Expense::whereDate('expense_date', today())->sum('amount');
        $pendingExpenses = Expense::where('status', 'pending')->count();
        $approvedExpenses = Expense::where('status', 'approved')->count();

        $recentExpenses = Expense::with(['category', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        $expenses = Expense::with(['category', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $categories = ExpenseCategory::orderBy('name')->get();

        return view('expenses.index', [
            'expenses' => $expenses,
            'totalExpenses' => $totalExpenses,
            'todayExpenses' => $todayExpenses,
            'pendingExpenses' => $pendingExpenses,
            'approvedExpenses' => $approvedExpenses,
            'recentExpenses' => $recentExpenses,
            'categories' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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

            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('expense-attachments', 'public');
            }

            $expense = Expense::create([
                'user_id' => auth()->id(),
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
        $expense = Expense::with(['category'])->findOrFail($id);

        return response()->json([
            'expense' => $expense
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);

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
        $expense = Expense::findOrFail($id);

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
            $query = $request->get('q', '');
            $status = $request->get('status', 'all');
            $date = $request->get('date', 'all');
            $category = $request->get('category', 'all');
            $sort = $request->get('sort', 'newest');

            // Start with base query
            $expensesQuery = Expense::with(['category', 'user']);

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
                ->map(function($expense) {
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
                        'has_attachment' => !empty($expense->attachment)
                    ];
                });

            return response()->json([
                'success' => true,
                'expenses' => $expenses,
                'count' => $expenses->count(),
                'has_more' => $expenses->count() >= 100
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
