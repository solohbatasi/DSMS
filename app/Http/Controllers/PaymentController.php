<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Student_ef_list;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $totalPayments = Payment::sum('amount');
        $todayPayments = Payment::whereDate('payment_date', today())->sum('amount');
        $pendingPayments = Payment::where('payment_status', 'pending')->count();
        $completedPayments = Payment::where('payment_status', 'completed')->count();

        $recentPayments = Payment::with(['efList.student', 'efList.course'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        $payments = Payment::with(['efList.student', 'efList.course'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('payments.index', [
            'payments' => $payments,
            'totalPayments' => $totalPayments,
            'todayPayments' => $todayPayments,
            'pendingPayments' => $pendingPayments,
            'completedPayments' => $completedPayments,
            'recentPayments' => $recentPayments
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Not used - we use modal instead
        return redirect()->route('payments.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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

        try {
            DB::beginTransaction();

            // Get the EF list record
            $efList = Student_ef_list::findOrFail($request->ef_id);

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
                'user_id' => auth()->id(),
                'ef_id' => $request->ef_id,
                'reference' => $request->reference,
                'amount' => $request->amount,
                'balance' => $newBalance,
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_status,
                'remarks' => $request->remarks
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
            \Log::error('Payment store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Try again.',
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Redirect to index since we don't have a show page
        return redirect()->route('payments.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $payment = Payment::with(['efList.student', 'efList.course'])->findOrFail($id);

        return response()->json([
            'payment' => $payment
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

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
            \Log::error('Payment update error: ' . $e->getMessage());
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
        $payment = Payment::findOrFail($id);

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
            \Log::error('Payment delete error: ' . $e->getMessage());
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
            $query = $request->get('q', '');
            $status = $request->get('status', 'all');
            $date = $request->get('date', 'all');
            $sort = $request->get('sort', 'newest');

            // Start with base query
            $paymentsQuery = Payment::with(['efList.student', 'efList.course']);

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
                ->map(function($payment) {
                    return [
                        'id' => $payment->id,
                        'student_name' => $payment->efList->student->name,
                        'student_id' => $payment->efList->student->id_no,
                        'ef_number' => $payment->efList->ef_no,
                        'course_name' => $payment->efList->course->course,
                        'reference' => $payment->reference,
                        'amount' => number_format($payment->amount, 2),
                        'balance' => number_format($payment->balance, 2),
                        'payment_date' => $payment->payment_date ? $payment->payment_date->format('M d, Y') : 'N/A',
                        'payment_method' => $payment->payment_method,
                        'payment_status' => $payment->payment_status,
                        'remarks' => $payment->remarks
                    ];
                });

            return response()->json([
                'success' => true,
                'payments' => $payments,
                'count' => $payments->count()
            ]);

        } catch (\Exception $e) {
            \Log::error('Payment search error: ' . $e->getMessage());
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
            $query = $request->get('q', '');

            if (strlen($query) < 2) {
                return response()->json([
                    'success' => true,
                    'students' => []
                ]);
            }

            $students = Student_ef_list::with(['student', 'course'])
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
                ->where('status', 'active')
                ->orderBy('ef_no')
                ->limit(25)
                ->get()
                ->map(function ($ef) {
                    return [
                        'ef_id' => $ef->id,
                        'student_name' => $ef->student->name,
                        'id_no' => $ef->student->id_no,
                        'ef_number' => $ef->ef_no,
                        'course_name' => $ef->course->course,
                        'total_fee' => $ef->total_fee,
                        'paid_amount' => $ef->paid_amount,
                        'balance' => $ef->balance,
                        'status' => $ef->status
                    ];
                });

            return response()->json([
                'success' => true,
                'students' => $students
            ]);

        } catch (\Exception $e) {
            \Log::error('Student search error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Search failed',
                'students' => []
            ], 500);
        }
    }
}
