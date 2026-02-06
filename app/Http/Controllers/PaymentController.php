<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Student_ef_list;
use App\Models\Student;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Base query with branch filtering
        $paymentsQuery = Payment::with(['efList.student', 'efList.course', 'efList.student.branch']);

        // Apply branch filtering based on user role and branch selection
        if ($user->isAdmin()) {
            // Admin: Check if viewing a specific branch or all branches
            $selectedBranchId = session('admin_selected_branch');

            if ($selectedBranchId) {
                // Admin is viewing a specific branch
                $paymentsQuery->whereHas('efList', function ($q) use ($selectedBranchId) {
                    $q->where('branch_id', $selectedBranchId);
                });
            }
            // If $selectedBranchId is null/empty, admin is viewing all branches (no additional filter)
        } else {
            // Non-admin users: only show payments from their assigned branch
            $paymentsQuery->whereHas('efList', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            });
        }

        // Additional branch filter from request (for admin branch switcher UI)
        if ($user->isAdmin() && $request->has('branch_id')) {
            $branchId = $request->branch_id;
            if ($branchId) {
                $paymentsQuery->whereHas('efList', function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                });
            }
        }

        // Get counts with branch filtering
        $countQuery = Payment::query();

        // Apply the same branch filtering to counts
        if ($user->isAdmin()) {
            $selectedBranchId = session('admin_selected_branch');
            if ($selectedBranchId) {
                $countQuery->whereHas('efList', function ($q) use ($selectedBranchId) {
                    $q->where('branch_id', $selectedBranchId);
                });
            }
        } else {
            $countQuery->whereHas('efList', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            });
        }

        $totalPayments = $countQuery->sum('amount');
        $todayPayments = $countQuery->clone()->whereDate('payment_date', today())->sum('amount');
        $pendingPayments = $countQuery->clone()->where('payment_status', 'pending')->count();
        $completedPayments = $countQuery->clone()->where('payment_status', 'completed')->count();

        $recentPayments = $paymentsQuery->clone()
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        $payments = $paymentsQuery->orderBy('created_at', 'desc')->paginate(10);

        // Get branches for admin filter
        $branches = $user->isAdmin() ? Branch::active()->get() : collect();

        // Get selected branch info for display
        $selectedBranch = null;
        if ($user->isAdmin()) {
            $selectedBranchId = session('admin_selected_branch');
            $selectedBranch = $selectedBranchId ? Branch::find($selectedBranchId) : null;
        }

        return view('payments.index', [
            'payments' => $payments,
            'totalPayments' => $totalPayments,
            'todayPayments' => $todayPayments,
            'pendingPayments' => $pendingPayments,
            'completedPayments' => $completedPayments,
            'recentPayments' => $recentPayments,
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
        $user = Auth::user();

        try {
            $validated = $request->validate([
                'ef_id' => 'required|exists:student_ef_lists,id',
                'reference' => 'nullable|string|max:255',
                'amount' => 'required|numeric|min:0',
                'payment_date' => 'required|date',
                'payment_method' => 'required|string|max:255',
                'payment_status' => 'required|in:completed,pending,failed',
                'remarks' => 'nullable|string'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        }

        // Get the EF list record and check branch access
        $efList = Student_ef_list::with('student')->findOrFail($request->ef_id);

        // Check if user can access this student's branch
        if (!$user->isAdmin() && $efList->branch_id != $user->branch_id) {
            return response()->json([
                'success' => false,
                'message' => 'You can only add payments for students in your branch.'
            ], 403);
        }

        try {
            DB::beginTransaction();

            // Validate payment amount doesn't exceed balance
            if ($request->amount > $efList->balance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount cannot exceed the current balance of KSh ' . number_format($efList->balance, 2)
                ], 422);
            }

            // Calculate new balance
            $newBalance = $efList->balance - $request->amount;

            // Create payment record
            $payment = Payment::create([
                'user_id' => $user->id,
                'ef_id' => $request->ef_id,
                'reference' => $request->reference,
                'amount' => $request->amount,
                'balance' => $newBalance,
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_status,
                'remarks' => $request->remarks,
                'branch_id' => $efList->branch_id // Store branch_id with payment
            ]);

            // Update EF list record
            $efList->update([
                'paid_amount' => $efList->paid_amount + $request->amount,
                'balance' => $newBalance,
                'last_payment_date' => $request->payment_date
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment store error: ' . $e->getMessage());
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
        $payment = Payment::with(['efList.student', 'efList.course', 'efList.student.branch'])->findOrFail($id);

        // Check if user can access this payment
        if (!$user->isAdmin() && $payment->efList->branch_id != $user->branch_id) {
            abort(403, 'You do not have permission to access this payment.');
        }

        return response()->json([
            'payment' => $payment
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $payment = Payment::findOrFail($id);

        // Check if user can access this payment
        if (!$user->isAdmin() && $payment->efList->branch_id != $user->branch_id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update this payment.'
            ], 403);
        }

        try {
            $validated = $request->validate([
                'reference' => 'nullable|string|max:255',
                'amount' => 'required|numeric|min:0',
                'balance' => 'required|numeric',
                'payment_date' => 'required|date',
                'payment_method' => 'required|string|max:255',
                'payment_status' => 'required|in:completed,pending,failed',
                'remarks' => 'nullable|string'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $oldAmount = $payment->amount;
            $newAmount = $request->amount;

            $payment->update([
                'reference' => $request->reference,
                'amount' => $newAmount,
                'balance' => $request->balance,
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_status,
                'remarks' => $request->remarks
            ]);

            // If amount changed, update the EF list
            if ($oldAmount != $newAmount) {
                $efList = $payment->efList;
                $amountDifference = $newAmount - $oldAmount;

                $efList->update([
                    'paid_amount' => $efList->paid_amount + $amountDifference,
                    'balance' => $efList->balance - $amountDifference
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment updated successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment update error: ' . $e->getMessage());
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
        $payment = Payment::findOrFail($id);

        // Check if user can delete this payment
        if (!$user->isAdmin() && $payment->efList->branch_id != $user->branch_id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete this payment.'
            ], 403);
        }

        try {
            DB::beginTransaction();

            // Get the EF list record
            $efList = $payment->efList;

            // Revert the payment from EF list
            $efList->update([
                'paid_amount' => $efList->paid_amount - $payment->amount,
                'balance' => $efList->balance + $payment->amount
            ]);

            $payment->delete();

            DB::commit();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete payment. ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Search payments for table filtering
     */
    public function search(Request $request)
    {
        try {
            $user = Auth::user();
            $query = $request->get('q', '');
            $status = $request->get('status', 'all');
            $date = $request->get('date', 'all');
            $sort = $request->get('sort', 'newest');

            // Start with base query with branch filtering
            $paymentsQuery = Payment::with(['efList.student', 'efList.course', 'efList.student.branch']);

            // Apply branch filtering based on user role and branch selection
            if ($user->isAdmin()) {
                // Admin: Check if viewing a specific branch or all branches
                $selectedBranchId = session('admin_selected_branch');

                if ($selectedBranchId) {
                    // Admin is viewing a specific branch
                    $paymentsQuery->whereHas('efList', function ($q) use ($selectedBranchId) {
                        $q->where('branch_id', $selectedBranchId);
                    });
                }
                // If $selectedBranchId is null/empty, admin is viewing all branches (no additional filter)
            } else {
                // Non-admin users: only show payments from their assigned branch
                $paymentsQuery->whereHas('efList', function ($q) use ($user) {
                    $q->where('branch_id', $user->branch_id);
                });
            }

            // Apply search filter
            if (!empty($query)) {
                $paymentsQuery->where(function ($q) use ($query) {
                    $q->where('reference', 'like', "%{$query}%")
                      ->orWhereHas('efList.student', function ($q) use ($query) {
                          $q->where('name', 'like', "%{$query}%")
                            ->orWhere('id_no', 'like', "%{$query}%");
                      })
                      ->orWhereHas('efList', function ($q) use ($query) {
                          $q->where('ef_no', 'like', "%{$query}%");
                      });
                });
            }

            // Apply status filter
            if ($status !== 'all') {
                $paymentsQuery->where('payment_status', $status);
            }

            // Apply date filter
            if ($date !== 'all') {
                $now = Carbon::now();
                switch ($date) {
                    case 'today':
                        $paymentsQuery->whereDate('payment_date', $now->toDateString());
                        break;
                    case 'week':
                        $paymentsQuery->whereBetween('payment_date', [
                            $now->startOfWeek()->toDateString(),
                            $now->endOfWeek()->toDateString()
                        ]);
                        break;
                    case 'month':
                        $paymentsQuery->whereBetween('payment_date', [
                            $now->startOfMonth()->toDateString(),
                            $now->endOfMonth()->toDateString()
                        ]);
                        break;
                }
            }

            // Apply sorting
            switch ($sort) {
                case 'oldest':
                    $paymentsQuery->orderBy('created_at', 'asc');
                    break;
                case 'amount_high':
                    $paymentsQuery->orderBy('amount', 'desc');
                    break;
                case 'amount_low':
                    $paymentsQuery->orderBy('amount', 'asc');
                    break;
                default: // newest
                    $paymentsQuery->orderBy('created_at', 'desc');
                    break;
            }

            $payments = $paymentsQuery->get()
                ->map(function($payment) use ($user) {
                    // Additional security check
                    if ($user->isAdmin()) {
                        $selectedBranchId = session('admin_selected_branch');
                        if ($selectedBranchId && $payment->efList->branch_id != $selectedBranchId) {
                            return null; // Skip payments from other branches when admin is viewing a specific branch
                        }
                    } elseif (!$user->isAdmin() && $payment->efList->branch_id != $user->branch_id) {
                        return null; // Skip payments from other branches for non-admin users
                    }

                    return [
                        'id' => $payment->id,
                        'student_name' => $payment->efList->student->name ?? 'Unknown',
                        'student_id' => $payment->efList->student->id_no ?? 'N/A',
                        'ef_number' => $payment->efList->ef_no,
                        'course_name' => $payment->efList->course->course ?? '-',
                        'reference' => $payment->reference,
                        'amount' => number_format($payment->amount, 2),
                        'balance' => number_format($payment->balance, 2),
                        'payment_date' => $payment->payment_date ? $payment->payment_date->format('M d, Y') : 'N/A',
                        'payment_method' => $payment->payment_method,
                        'payment_status' => $payment->payment_status,
                        'remarks' => $payment->remarks,
                        'branch_name' => $payment->efList->student->branch->name ?? 'N/A'
                    ];
                })
                ->filter() // Remove null entries
                ->values(); // Reindex array

            return response()->json([
                'success' => true,
                'payments' => $payments,
                'count' => $payments->count(),
                'branch_context' => $user->isAdmin() ? session('admin_selected_branch_name', 'All Branches') : ($user->branch->name ?? 'N/A')
            ]);

        } catch (\Exception $e) {
            Log::error('Payment search error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Search failed: ' . $e->getMessage(),
                'payments' => []
            ], 500);
        }
    }

    /**
     * Search students for payment modal
     */
    public function searchStudents(Request $request)
    {
        try {
            $user = Auth::user();
            $query = $request->get('q', '');

            if (strlen($query) < 2) {
                return response()->json([
                    'success' => true,
                    'students' => []
                ]);
            }

            // Start query with branch filtering
            $studentsQuery = Student_ef_list::with(['student', 'course', 'student.branch'])
                ->where(function ($q) use ($query) {
                    $q->where('ef_no', 'like', "%{$query}%")
                      ->orWhereHas('student', function ($q) use ($query) {
                          $q->where('name', 'like', "%{$query}%")
                            ->orWhere('id_no', 'like', "%{$query}%");
                      })
                      ->orWhereHas('course', function ($q) use ($query) {
                          $q->where('course', 'like', "%{$query}%");
                      });
                })
                ->where('status', 'active');

            // Apply branch filtering based on user role and branch selection
            if ($user->isAdmin()) {
                // Admin: Check if viewing a specific branch or all branches
                $selectedBranchId = session('admin_selected_branch');

                if ($selectedBranchId) {
                    // Admin is viewing a specific branch
                    $studentsQuery->where('branch_id', $selectedBranchId);
                }
                // If $selectedBranchId is null/empty, admin is viewing all branches (no additional filter)
            } else {
                // Non-admin users: only show students from their assigned branch
                $studentsQuery->where('branch_id', $user->branch_id);
            }

            $students = $studentsQuery->orderBy('ef_no')
                ->limit(25)
                ->get()
                ->map(function ($ef) use ($user) {
                    // Additional security check
                    if ($user->isAdmin()) {
                        $selectedBranchId = session('admin_selected_branch');
                        if ($selectedBranchId && $ef->branch_id != $selectedBranchId) {
                            return null; // Skip students from other branches when admin is viewing a specific branch
                        }
                    } elseif (!$user->isAdmin() && $ef->branch_id != $user->branch_id) {
                        return null; // Skip students from other branches for non-admin users
                    }

                    return [
                        'ef_id' => $ef->id,
                        'student_name' => $ef->student->name,
                        'id_no' => $ef->student->id_no,
                        'ef_number' => $ef->ef_no,
                        'course_name' => $ef->course->course,
                        'total_fee' => $ef->total_fee,
                        'paid_amount' => $ef->paid_amount,
                        'balance' => $ef->balance,
                        'status' => $ef->status,
                        'branch_name' => $ef->student->branch->name ?? 'N/A'
                    ];
                })
                ->filter() // Remove null entries
                ->values(); // Reindex array

            return response()->json([
                'success' => true,
                'students' => $students,
                'branch_context' => $user->isAdmin() ? session('admin_selected_branch_name', 'All Branches') : ($user->branch->name ?? 'N/A')
            ]);

        } catch (\Exception $e) {
            Log::error('Student search error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Search failed',
                'students' => []
            ], 500);
        }
    }


    /**
     * Get payments by branch (for admin reports)
     */
    public function getBranchPayments($branchId)
    {
        $user = Auth::user();

        if (!$user->isAdmin()) {
            abort(403, 'Only administrators can view branch-specific reports.');
        }

        $branch = Branch::findOrFail($branchId);

        $payments = Payment::with(['efList.student', 'efList.course'])
            ->whereHas('efList', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->orderBy('payment_date', 'desc')
            ->paginate(20);

        return view('payments.branch', [
            'payments' => $payments,
            'branch' => $branch
        ]);
    }
    /**
     * Get receipt data for printing
     */
    public function getReceiptData($id)
    {
        $user = Auth::user();

        try {
            $payment = Payment::with(['efList.student', 'efList.course', 'efList.branch'])
                ->findOrFail($id);

            // Check if user can access this payment
            if (!$user->isAdmin() && $payment->efList->branch_id != $user->branch_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to print this receipt.'
                ], 403);
            }

            // Format the data for receipt
            $receiptData = [
                'payment' => [
                    'id' => $payment->id,
                    'reference' => $payment->reference,
                    'amount' => $payment->amount,
                    'balance' => $payment->balance,
                    'payment_date' => $payment->payment_date,
                    'payment_method' => $payment->payment_method,
                    'payment_status' => $payment->payment_status
                ],
                'student' => [
                    'name' => $payment->efList->student->name ?? 'Unknown',
                    'id_no' => $payment->efList->student->id_no ?? 'N/A',
                    'ef_no' => $payment->efList->ef_no,
                    'paid_amount' => $payment->efList->paid_amount,
                    'balance' => $payment->efList->balance
                ],
                'course' => [
                    'course' => $payment->efList->course->course ?? 'Unknown Course',
                    'total_amount' => $payment->efList->course->total_amount ?? 0
                ],
                'branch' => [
                    'name' => $payment->efList->branch->name ?? 'Main Branch',
                    'location' => $payment->efList->branch->location ?? 'N/A'
                ]
            ];

            return response()->json([
                'success' => true,
                'payment' => $receiptData['payment'],
                'student' => $receiptData['student'],
                'course' => $receiptData['course'],
                'branch' => $receiptData['branch']
            ]);

        } catch (\Exception $e) {
            Log::error('Receipt data error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load receipt data.'
            ], 500);
        }
    }
}
