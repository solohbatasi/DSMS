<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MeterReading;
use App\Models\Customer;
use App\Models\Meter;
use App\Models\Bill;
use App\Services\OCRService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class MeterReadingController extends Controller
{
    public function index()
    {
        $readings = MeterReading::with('customer', 'reader')
            ->latest()
            ->paginate(20);
            
        return view('admin.meter-readings.index', compact('readings'));
    }

    public function create(Request $request)
    {
        $customerId = $request->get('customer');
        $customer = null;
        $lastReading = null;
        $meter = null;

        if ($customerId) {
            $customer = Customer::with('meter')->findOrFail($customerId);
            $meter = $customer->meter;
            $lastReading = MeterReading::where('customer_id', $customerId)
                ->latest()
                ->first();
        }

        return view('admin.meter-readings.create', compact('customer', 'lastReading', 'meter'));
    }

    public function store(Request $request)
    {


        $readingPeriod = Carbon::parse($request->reading_date)->format('F Y');
        $existingReading = MeterReading::where('customer_id', $request->customer_id)
            ->where('reading_period', $readingPeriod)
            ->first();

        if ($existingReading && !$request->has('force_duplicate')) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "A meter reading for {$readingPeriod} already exists. Are you sure you want to create another reading for the same period?",
                    'requires_confirmation' => true,
                    'existing_reading' => [
                        'id' => $existingReading->id,
                        'reading_date' => $existingReading->reading_date,
                        'current_reading' => $existingReading->current_reading,
                        'reading_period' => $existingReading->reading_period
                    ]
                ], 422);
            }
            
            throw new \Exception("Meter reading for {$readingPeriod} has already been recorded.");
        }


        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'current_reading' => 'required|numeric|min:0',
            'reading_date' => 'required|date',
            'reading_image' => 'nullable|image|max:2048',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Get customer and meter
                $customer = Customer::findOrFail($request->customer_id);
                $meter = $customer->meter;

                if (!$meter) {
                    throw new \Exception('Customer does not have a meter assigned.');
                }

                // Check for duplicate reading in the same period
                $readingPeriod = Carbon::parse($request->reading_date)->format('F Y');
                $existingReading = MeterReading::where('customer_id', $request->customer_id)
                    ->where('reading_period', $readingPeriod)
                    ->first();

                if ($existingReading) {
                    throw new \Exception('Meter reading for ' . $readingPeriod . ' has already been recorded. Please wait until next month to record again.');
                }

                // Get previous reading
                $previousReading = MeterReading::where('customer_id', $request->customer_id)
                    ->latest()
                    ->first();

                $previousReadingValue = $previousReading ? $previousReading->current_reading : ($meter->initial_reading ?? 0);

                // Validate current reading
                if ($request->current_reading < $previousReadingValue) {
                    throw new \Exception('Current reading cannot be less than previous reading (' . number_format($previousReadingValue, 2) . ' m³).');
                }

                // Calculate consumption
                $consumption = $request->current_reading - $previousReadingValue;

                // Handle image upload
                $imagePath = null;
                if ($request->hasFile('reading_image')) {
                    $imagePath = $request->file('reading_image')->store('meter-readings', 'public');
                }

                // Create reading
                $reading = MeterReading::create([
                    'customer_id' => $request->customer_id,
                    'current_reading' => $request->current_reading,
                    'previous_reading' => $previousReadingValue,
                    'consumption' => $consumption,
                    'reading_date' => $request->reading_date,
                    'reading_type' => 'monthly',
                    'reading_period' => $readingPeriod,
                    'billed' => false,
                    'reading_image' => $imagePath,
                    'notes' => $request->notes,
                    'read_by' => auth()->id(),
                ]);

                // Update meter's current reading
                $meter->update([
                    'current_reading' => $request->current_reading
                ]);

                // AUTO-GENERATE BILL
                $this->generateBill($reading, $customer, $consumption);
            });

            return redirect()->route('admin.customers.show', $request->customer_id)
                ->with('success', 'Meter reading recorded and bill generated successfully!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Generate bill automatically after meter reading
     */
    private function generateBill(MeterReading $reading, Customer $customer, $consumption)
    {
        // Calculate billing amounts
        $baseCharge = 100; // Fixed base charge
        $consumptionRate = 50; // Rate per cubic meter
        $consumptionCharge = $consumption * $consumptionRate;
        $taxRate = 0.16; // 16% VAT
        $taxAmount = ($baseCharge + $consumptionCharge) * $taxRate;
        $totalAmount = $baseCharge + $consumptionCharge + $taxAmount;

        // Generate bill number
        $latestBill = Bill::latest()->first();
        $billNumber = 'BILL-' . str_pad(($latestBill ? $latestBill->id : 0) + 1, 6, '0', STR_PAD_LEFT);

        // Create bill
        $bill = Bill::create([
            'user_id' => $customer->id, // Using customer as user for now
            'meter_id' => $customer->meter->id,
            'bill_number' => $billNumber,
            'billing_period_start' => Carbon::parse($reading->reading_date)->startOfMonth(),
            'billing_period_end' => Carbon::parse($reading->reading_date)->endOfMonth(),
            'consumption' => $consumption,
            'base_charge' => $baseCharge,
            'consumption_charge' => $consumptionCharge,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'due_date' => Carbon::parse($reading->reading_date)->addDays(30),
            'bill_status' => 'unpaid',
            'notes' => 'Auto-generated from meter reading #' . $reading->id,
            'created_by' => auth()->id(),
        ]);

        // Mark reading as billed
        $reading->update([
            'billed' => true,
            'billed_by' => auth()->id(),
            'billed_at' => now(),
        ]);

        return $bill;
    }

    /**
     * API endpoint for OCR processing
     */
    public function processOCR(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048',
        ]);

        try {
            $imagePath = $request->file('image')->store('temp-ocr', 'public');
            $fullImagePath = storage_path('app/public/' . $imagePath);
            
            $ocrService = new OCRService();
            $reading = $ocrService->extractMeterReading($fullImagePath);
            
            // Clean up temporary file
            Storage::disk('public')->delete(str_replace('temp-ocr/', '', $imagePath));
            
            return response()->json([
                'success' => true,
                'detected_reading' => $reading,
                'message' => $reading ? 'Reading detected successfully' : 'No readable meter numbers found'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('OCR API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error processing image: ' . $e->getMessage()
            ], 500);
        }
    }
}