<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meter;
use App\Models\Customer;
use App\Models\MeterReading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MeterController extends Controller
{
    public function index()
    {
        $meters = Meter::with(['customer', 'meterReadings' => function($query) {
            $query->latest()->limit(1);
        }])->latest()->paginate(20);

        $stats = [
            'total' => Meter::count(),
            'assigned' => Meter::where('status', 'assigned')->count(),
            'unassigned' => Meter::where('status', 'available')->count(),
            'faulty' => Meter::where('status', 'faulty')->count(),
        ];

        return view('admin.meters.index', compact('meters', 'stats'));
    }

    public function available()
    {
        $meters = Meter::available()
            ->with(['customer', 'meterReadings' => function($query) {
                $query->latest()->limit(1);
            }])
            ->latest()
            ->paginate(20);

        return view('admin.meters.available', compact('meters'));
    }

    public function assigned()
    {
        $meters = Meter::assigned()
            ->with(['customer', 'meterReadings' => function($query) {
                $query->latest()->limit(1);
            }])
            ->latest()
            ->paginate(20);

        return view('admin.meters.assigned', compact('meters'));
    }

    public function byLocation(Request $request)
    {
        $location = $request->get('location', '');
        $meters = Meter::with(['customer', 'meterReadings' => function($query) {
                $query->latest()->limit(1);
            }])
            ->when($location, function($query) use ($location) {
                return $query->where('installation_address', 'like', "%{$location}%");
            })
            ->latest()
            ->paginate(20);

        return view('admin.meters.by-location', compact('meters', 'location'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'meter_number' => 'required|string|max:50|unique:meters,meter_number',
            'meter_type' => 'required|string|in:domestic,commercial,industrial,institutional,smart,mechanical',
            'meter_model' => 'nullable|string|max:100',
            'customer_id' => 'nullable|exists:customers,id',
            'installation_address' => 'nullable|string|max:500',
            'installation_date' => 'nullable|date',
            'initial_reading' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                // Determine status based on customer assignment
                $validated['status'] = $validated['customer_id'] ? 'assigned' : 'available';

                // Create meter
                $meter = Meter::create($validated);

                // If assigned to customer, update customer record and create initial reading
                if ($validated['customer_id']) {
                    $customer = Customer::find($validated['customer_id']);
                    
                    // Update customer with meter info
                    $customer->update([
                        'meter_number' => $meter->meter_number,
                        'meter_type' => $meter->meter_type,
                        'initial_meter_reading' => $validated['initial_reading'],
                        'initial_reading_date' => $validated['installation_date'] ?? now(),
                    ]);

                    // Create initial meter reading with customer_id
                    MeterReading::create([
                        'customer_id' => $customer->id, // This is provided for assigned meters
                        'meter_id' => $meter->id,
                        'current_reading' => $validated['initial_reading'],
                        'previous_reading' => 0,
                        'consumption' => $validated['initial_reading'],
                        'reading_date' => $validated['installation_date'] ?? now(),
                        'reading_type' => 'initial',
                        'reading_period' => 'Initial Installation',
                        'billed' => false,
                        'read_by' => auth()->id(),
                        'notes' => 'Initial meter reading upon installation',
                    ]);
                } else {
                    // Create initial reading for unassigned meter WITHOUT customer_id
                    MeterReading::create([
                        'meter_id' => $meter->id,
                        'current_reading' => $validated['initial_reading'],
                        'previous_reading' => 0,
                        'consumption' => $validated['initial_reading'],
                        'reading_date' => $validated['installation_date'] ?? now(),
                        'reading_type' => 'initial',
                        'reading_period' => 'Initial Installation',
                        'billed' => false,
                        'read_by' => auth()->id(),
                        'notes' => 'Initial meter reading for unassigned meter',
                    ]);
                    // Note: customer_id is not set for unassigned meters
                }
            });

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $validated['customer_id'] ? 'Meter registered and assigned to customer successfully!' : 'Meter registered successfully!',
                ]);
            }

            return redirect()->route('admin.meters.index')
                ->with('success', $validated['customer_id'] ? 'Meter registered and assigned to customer successfully!' : 'Meter registered successfully!');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error registering meter: ' . $e->getMessage());
        }
    }

    public function show(Meter $meter)
    {
        $meter->load(['customer', 'meterReadings' => function($query) {
            $query->latest()->limit(10);
        }]);

        return view('admin.meters.show', compact('meter'));
    }

    public function edit(Meter $meter)
    {
        $meterTypes = [
            'domestic' => 'Domestic',
            'commercial' => 'Commercial',
            'industrial' => 'Industrial',
            'institutional' => 'Institutional',
            'smart' => 'Smart Meter',
            'mechanical' => 'Mechanical',
        ];

        $customers = Customer::active()->get();
        $statuses = [
            'available' => 'Available',
            'assigned' => 'Assigned',
            'faulty' => 'Faulty',
            'maintenance' => 'Maintenance',
        ];

        return view('admin.meters.edit', compact('meter', 'meterTypes', 'customers', 'statuses'));
    }

    public function update(Request $request, Meter $meter)
    {
        $validated = $request->validate([
            'meter_number' => 'required|string|max:50|unique:meters,meter_number,' . $meter->id,
            'meter_type' => 'required|string|in:domestic,commercial,industrial,institutional,smart,mechanical',
            'meter_model' => 'nullable|string|max:100',
            'customer_id' => 'nullable|exists:customers,id',
            'installation_address' => 'nullable|string|max:500',
            'installation_date' => 'nullable|date',
            'status' => 'required|string|in:available,assigned,faulty,maintenance',
            'initial_reading' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $meter->update($validated);

        return redirect()->route('admin.meters.show', $meter)
            ->with('success', 'Meter updated successfully!');
    }

    public function assignToCustomer(Request $request, Meter $meter)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'installation_address' => 'required|string|max:500',
            'installation_date' => 'required|date',
            'initial_reading' => 'nullable|numeric|min:0',
        ]);

        $customer = Customer::findOrFail($validated['customer_id']);

        DB::transaction(function () use ($meter, $customer, $validated) {
            $meter->update([
                'customer_id' => $customer->id,
                'installation_address' => $validated['installation_address'],
                'installation_date' => $validated['installation_date'],
                'status' => 'assigned',
                'initial_reading' => $validated['initial_reading'] ?? $meter->initial_reading,
            ]);

            // Update customer's meter info
            $customer->update([
                'meter_number' => $meter->meter_number,
                'meter_type' => $meter->meter_type,
                'initial_meter_reading' => $validated['initial_reading'] ?? $meter->initial_reading,
                'initial_reading_date' => $validated['installation_date'],
            ]);

            // Create initial meter reading if not exists
            if (!$meter->meterReadings()->exists()) {
                MeterReading::create([
                    'customer_id' => $customer->id,
                    'meter_id' => $meter->id,
                    'current_reading' => $validated['initial_reading'] ?? $meter->initial_reading,
                    'previous_reading' => 0,
                    'consumption' => $validated['initial_reading'] ?? $meter->initial_reading,
                    'reading_date' => $validated['installation_date'],
                    'reading_type' => 'initial',
                    'reading_period' => 'Initial Installation',
                    'billed' => false,
                    'read_by' => auth()->id(),
                    'notes' => 'Initial meter reading upon customer assignment',
                ]);
            }
        });

        return redirect()->route('admin.meters.show', $meter)
            ->with('success', "Meter assigned to {$customer->full_name} successfully!");
    }

    public function unassign(Meter $meter)
    {
        if ($meter->customer) {
            $customerName = $meter->customer->full_name;
            
            DB::transaction(function () use ($meter) {
                $customer = $meter->customer;
                $meter->update([
                    'customer_id' => null,
                    'status' => 'available',
                    'installation_address' => null,
                    'installation_date' => null,
                ]);

                // Clear customer's meter info
                $customer->update([
                    'meter_number' => null,
                    'meter_type' => null,
                ]);
            });

            return redirect()->route('admin.meters.show', $meter)
                ->with('success', "Meter unassigned from {$customerName} successfully!");
        }

        return redirect()->back()->with('error', 'Meter is not assigned to any customer.');
    }

    // Update meter reading when customer reading is updated
    public function updateMeterReading(Meter $meter, Request $request)
    {
        $validated = $request->validate([
            'current_reading' => 'required|numeric|min:0',
            'reading_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $latestReading = $meter->meterReadings()->latest()->first();
        $previousReading = $latestReading ? $latestReading->current_reading : $meter->initial_reading;

        $meterReading = MeterReading::create([
            'customer_id' => $meter->customer_id,
            'meter_id' => $meter->id,
            'current_reading' => $validated['current_reading'],
            'previous_reading' => $previousReading,
            'consumption' => $validated['current_reading'] - $previousReading,
            'reading_date' => $validated['reading_date'],
            'reading_type' => 'monthly',
            'reading_period' => \Carbon\Carbon::parse($validated['reading_date'])->format('F Y'),
            'billed' => false,
            'read_by' => auth()->id(),
            'notes' => $validated['notes'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Meter reading updated successfully!',
            'reading' => $meterReading
        ]);
    }
}