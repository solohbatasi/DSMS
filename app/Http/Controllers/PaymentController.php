<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Bill;
use App\Models\User;
use App\Models\Meter;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
Use Carbon\Carbon;
class PaymentController extends Controller
{
    /**
     * Display a listing of payments.
     */
    public function index(Request $request)
    {
        $query = Payment::with(['user','customer','bill']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('payment_no', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
        }

        // Status filter
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('payment_status', $request->status);
        }

        // Sorting
        $query->orderBy('created_at', 'desc');

        $payments = $query->paginate(10)->withQueryString();

        // For modal dropdowns
        $bills = Bill::with('user')->get();
        $users = User::all();
        $customers = Customer::all();

        return view('payments.index', compact('payments', 'bills', 'users','customers'));
    }

    /**
     * Show the form for creating a new payment (optional, using modal here).
     */
    public function create()
    {
        $bills = Bill::with('user')->get();
        $users = User::all();
        return view('payments.create', compact('bills', 'users'));
    }

    /**
     * Store a newly created payment in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'meter_no' => 'required|exists:meters,meter_number',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'transaction_reference' => 'nullable|string|max:255',
        ]);

        // Find the meter record by meter_number
        $meter = Meter::where('meter_number', $request->meter_no)->firstOrFail();

        // Create the payment linked to the meter
        $payment = Payment::create([
            'meter_id' => $meter->id, // correct column
            'user_id' => auth()->id(),
            'payment_no' => $request->payment_no ?? 'PAY-' . Str::upper(Str::random(6)),
            'amount' => $request->amount,
            'payment_date' => Carbon::now()->toDateString(),
            'payment_status' => 'completed',
            'payment_method' => $request->payment_method,
            'transaction_reference' => $request->transaction_reference,
        ]);

        /** -----------------------------
         *  APPLY PAYMENT TO BILLS
         * ----------------------------- */
        $remaining = $request->amount;

        // Fetch unpaid bills sorted by oldest
        $unpaidBills = Bill::where('meter_id', $meter->id)
            ->whereColumn('total_amount', '>', 'paid_amount')
            ->orderBy('billing_period_start', 'asc') // OR orderBy('id', 'asc')
            ->get();

        foreach ($unpaidBills as $bill) {

            if ($remaining <= 0) {
                break;
            }

            $billDue = $bill->total_amount - $bill->paid_amount;

            if ($remaining >= $billDue) {
                // Fully pay this bill
                $bill->paid_amount = $bill->total_amount;
                $bill->bill_status = 'paid';
                $bill->payment_date = Carbon::now()->toDateString();
                $bill->save();

                $remaining -= $billDue;
            } else {
                // Partially pay this bill
                $bill->paid_amount += $remaining;
                $bill->bill_status = 'partial';
                $bill->save();

                $remaining = 0;
            }
        }

        return redirect()->route('payments.index')->with('success', 'Payment created successfully.');
    }


    /**
     * Display the specified payment.
     */
    public function show(Payment $payment)
    {
        $payment->load(['user', 'bill']);
        return view('payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified payment.
     */
    public function edit(Payment $payment)
    {
        $bills = Bill::with('user')->get();
        $users = User::all();
        return view('payments.edit', compact('payment', 'bills', 'users'));
    }

    /**
     * Update the specified payment in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'bill_id' => 'required|exists:bills,id',
            'user_id' => 'required|exists:users,id',
            'payment_no' => 'required|unique:payments,payment_no,' . $payment->id,
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'transaction_reference' => 'nullable|string|max:255',
            'payment_status' => 'required|in:pending,completed,failed',
            'notes' => 'nullable|string',
        ]);

        $payment->update([
            'bill_id' => $request->bill_id,
            'user_id' => $request->user_id,
            'payment_no' => $request->payment_no,
            'payment_date' => $request->payment_date,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'transaction_reference' => $request->transaction_reference,
            'payment_status' => $request->payment_status,
            'notes' => $request->notes,
        ]);

        return redirect()->route('payments.index')->with('success', 'Payment updated successfully.');
    }

    /**
     * Remove the specified payment from storage.
     */
    public function destroy(Payment $payment)
    {
        $payment->delete();
        return redirect()->route('payments.index')->with('success', 'Payment deleted successfully.');
    }
}
