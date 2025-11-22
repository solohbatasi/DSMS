<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WaterConnectionApplication;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $estate = $request->get('estate');
        $status = $request->get('status');
        
        // Get unique estates for the filter dropdown
        $estates = Customer::whereNotNull('estate')
            ->distinct()
            ->pluck('estate')
            ->sort();

        // Get pending applications
        $pendingApplications = WaterConnectionApplication::where('status', 'pending')
            ->when($search, function($query) use ($search) {
                return $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('national_id', 'like', "%{$search}%")
                      ->orWhere('plot_number', 'like', "%{$search}%")
                      ->orWhere('house_number', 'like', "%{$search}%")
                      ->orWhere('estate', 'like', "%{$search}%")
                      ->orWhere('id', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        // Get active customers with enhanced filtering - FIXED SEARCH
        $activeCustomers = Customer::with('meter') // Eager load meter relationship
            ->when($status, function($query) use ($status) {
                return $query->where('status', $status);
            }, function($query) {
                // Default to active if no status filter
                return $query->where('status', 'active');
            })
            ->when($search, function($query) use ($search) {
                return $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('customer_number', 'like', "%{$search}%")
                      ->orWhere('plot_number', 'like', "%{$search}%")
                      ->orWhere('house_number', 'like', "%{$search}%")
                      ->orWhere('estate', 'like', "%{$search}%")
                      ->orWhere('id_number', 'like', "%{$search}%") // Added ID number search
                      ->orWhere('kra_pin', 'like', "%{$search}%") // Added KRA PIN search
                      ->orWhereHas('meter', function($meterQuery) use ($search) {
                          $meterQuery->where('meter_number', 'like', "%{$search}%")
                                    ->orWhere('meter_type', 'like', "%{$search}%")
                                    ->orWhere('meter_model', 'like', "%{$search}%");
                      });
                });
            })
            ->when($estate, function($query) use ($estate) {
                return $query->where('estate', $estate);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString(); // Preserve all query parameters in pagination links

        return view('admin.customers.index', compact(
            'pendingApplications', 
            'activeCustomers', 
            'search',
            'estate',
            'status',
            'estates'
        ));
    }

    public function show(Customer $customer)
    {
        // Load the customer with all relationships
        $customer->load([
            'meter', 
            'waterApplication', 
            'meterReadings.reader',
            'bills' => function($query) {
                $query->with('payments')->latest();
            },
            'payments'
        ]);
        
        // Get reading statistics
        $readingStats = [
            'total_readings' => $customer->meterReadings->count(),
            'last_reading' => $customer->meterReadings->first(),
            'average_consumption' => $customer->meterReadings->avg('consumption'),
            'total_consumption' => $customer->meterReadings->sum('consumption'),
        ];

        // Get billing statistics
        $billingStats = [
            'total_bills' => $customer->bills->count(),
            'paid_bills' => $customer->bills->where('bill_status', 'paid')->count(),
            'unpaid_bills' => $customer->bills->where('bill_status', 'unpaid')->count(),
            'total_billed' => $customer->bills->sum('total_amount'),
            'total_paid' => $customer->payments->sum('amount'),
            'outstanding_balance' => $customer->outstanding_balance,
            'account_balance' => $customer->account_balance,
        ];

        // Get recent activity (combined bills and payments)
        $recentActivity = $customer->bills->take(5)->map(function($bill) {
            return [
                'type' => 'bill',
                'date' => $bill->created_at,
                'amount' => $bill->total_amount,
                'description' => 'Bill #' . $bill->bill_number,
                'status' => $bill->bill_status,
                'icon' => 'file-invoice',
                'color' => $bill->bill_status === 'paid' ? 'green' : ($bill->bill_status === 'partial' ? 'yellow' : 'red')
            ];
        })->merge(
            $customer->payments->take(5)->map(function($payment) {
                return [
                    'type' => 'payment',
                    'date' => $payment->payment_date,
                    'amount' => $payment->amount,
                    'description' => 'Payment - ' . $payment->payment_method,
                    'status' => 'completed',
                    'icon' => 'credit-card',
                    'color' => 'green'
                ];
            })
        )->sortByDesc('date')->take(8);

        return view('admin.customers.show', compact(
            'customer', 
            'readingStats', 
            'billingStats',
            'recentActivity'
        ));
    }
}