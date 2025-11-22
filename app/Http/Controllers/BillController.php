<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Meter;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillController extends Controller
{
    public function index(Request $request)
    {
        // Get bills with filters
        $status = $request->get('status');
        $sort = $request->get('sort', 'newest');
        $customerId = $request->get('customer');

        $bills = Bill::with(['customer', 'meter'])
            ->when($customerId, function($query) use ($customerId) {
                return $query->where('customer_id', $customerId);
            })
            ->when($status && $status !== 'all', function($query) use ($status) {
                return $query->where('bill_status', $status);
            })
            ->when($sort, function($query) use ($sort) {
                switch ($sort) {
                    case 'oldest':
                        return $query->oldest();
                    case 'amount_high':
                        return $query->orderBy('total_amount', 'desc');
                    case 'amount_low':
                        return $query->orderBy('total_amount', 'asc');
                    default:
                        return $query->latest();
                }
            })
            ->paginate(10);

        // Get active customers for quick actions (only show one bill per customer)
        $customers = Customer::with(['meter', 'bills' => function($query) {
            $query->latest()->first();
        }])
        ->where('status', 'active')
        ->whereHas('bills')
        ->latest()
        ->paginate(10);

        return view('bills.index', compact('bills', 'customers'));
    }

    /**
     * Store a newly created bill in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'meter_id' => 'nullable|numeric',
            'billing_period_start' => 'nullable|date',
            'billing_period_end' => 'nullable|date',
            'consumption' => 'nullable|numeric',
            'base_charge' => 'nullable|numeric',
            'consumption_charge' => 'nullable|numeric',
            'tax_amount' => 'nullable|numeric',
            'late_fee' => 'nullable|numeric',
            'total_amount' => 'nullable|numeric',
            'due_date' => 'nullable|date',
            'bill_status' => 'nullable|string',
            'payment_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        // Auto-generate unique bill number
        $latestId = Bill::max('id') + 1;
        $billNumber = 'BILL-' . str_pad($latestId, 5, '0', STR_PAD_LEFT);

        $bill = Bill::create([
            ...$validated,
            'bill_number' => $billNumber,
            'created_by' => Auth::id(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Bill created successfully',
                'bill' => $bill,
            ]);
        }

        return redirect()->back()->with('success', 'Bill created successfully.');
    }

    /**
     * Display a single bill.
     */
    public function show(Bill $bill)
    {
        $bill->load(['customer', 'payments', 'creator']);

        // Set the page title
        $title = "Bill #{$bill->bill_number} - {$bill->customer->first_name} {$bill->customer->last_name}";

        // Calculate payment summary
        $paidAmount = $bill->payments->sum('amount');
        $dueAmount = $bill->total_amount - $paidAmount;
        $paymentPercentage = $bill->total_amount > 0 ? ($paidAmount / $bill->total_amount) * 100 : 0;

        return view('bills.show', compact('bill', 'title', 'paidAmount', 'dueAmount', 'paymentPercentage'));
    }

    /**
     * Update a bill.
     */
    public function update(Request $request, Bill $bill)
    {
        $validated = $request->validate([
            'billing_period_start' => 'nullable|date',
            'billing_period_end' => 'nullable|date',
            'consumption' => 'nullable|numeric',
            'base_charge' => 'nullable|numeric',
            'consumption_charge' => 'nullable|numeric',
            'tax_amount' => 'nullable|numeric',
            'late_fee' => 'nullable|numeric',
            'total_amount' => 'nullable|numeric',
            'due_date' => 'nullable|date',
            'bill_status' => 'nullable|string',
            'payment_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $bill->update($validated);

        return response()->json([
            'message' => 'Bill updated successfully',
            'bill' => $bill,
        ]);
    }

    /**
     * Remove the specified bill.
     */
    public function destroy(Bill $bill)
    {
        $bill->delete();

        return response()->json(['message' => 'Bill deleted successfully.']);
    }

    public function info(Bill $bill)
    {
        $bill->load('user', 'payments');
        $paidAmount = $bill->payments->sum('amount');
        $dueAmount = $bill->total_amount - $paidAmount;

    return response()->json([
        'user_id' => $bill->user_id,
        'user_name' => $bill->user?->name ?? 'Unknown',
        'due_amount' => $dueAmount,
    ]);
}
public function infoByCustomer($customerId)
{
    // Get the customer
    $customer = Customer::findOrFail($customerId);

    // Fetch all unpaid bills
    $unpaidBills = Bill::where('customer_id', $customerId)
        ->get()
        ->map(function ($bill) {
            $bill->due = $bill->total_amount - $bill->payments()->sum('amount');
            return $bill;
        })
        ->filter(function ($bill) {
            return $bill->due > 0;
        });

    // Total due across all bills
    $totalDue = $unpaidBills->sum('due');

    return response()->json([
        'customer_id'   => $customer->id,
        'customer_name' => $customer->first_name,
        'total_due'     => $totalDue,
        'unpaid_bills'  => $unpaidBills->values(), // optional
    ]);
}
public function infoByMeter($meter)
{
    $meterRecord = Meter::where('meter_number', $meter)->first();

    if (!$meterRecord) {
        return response()->json(['error' => 'Meter not found'], 404);
    }

    $customer = $meterRecord->customer;

    $unpaidBills = Bill::where('meter_id', $meterRecord->id)
        ->whereColumn('total_amount', '>', 'paid_amount')
        ->get();

    $totalDue = $unpaidBills->sum(fn($b) => $b->total_amount - $b->paid_amount);

    return response()->json([
        'customer' => [
            'id' => $customer->id,
            'name' => $customer->first_name . ' ' . $customer->last_name,
        ],
        'meter' => [
            'model' => $meterRecord->meter_model,
            'type' => $meterRecord->meter_type,
        ],
        'unpaid_bills' => $unpaidBills->map(function ($b) {
            return [
                'bill_number' => $b->bill_number,
                'total_amount' => $b->total_amount,
                'due' => $b->total_amount - $b->paid_amount,
            ];
        }),
        'total_due' => $totalDue,
    ]);
}



}
