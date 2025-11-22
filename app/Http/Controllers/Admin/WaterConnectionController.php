<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WaterConnectionApplication;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\MeterReading;
use App\Models\Customer;
use App\Models\Meter;

class WaterConnectionController extends Controller
{
    public function create()
    {
        return view('services.water-connection-application');
    }

    public function store(Request $request)
    {
        // Validate form input
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'gender' => 'required|string|in:Male,Female,Other',
            'kra_pin' => 'required|string|max:20',
            'kra_pin_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'national_id' => 'required|string|max:20',
            'national_id_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'plot_number' => 'required|string|max:50',
            'house_number' => 'required|string|max:50',
            'estate' => 'nullable|string|max:100',
            'expected_users' => 'nullable|integer|min:1',
            'property_owner' => 'required|string|max:100',
            'title_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'signature' => 'nullable|string|max:100',
            'date' => 'required|date',
        ]);

        try {
            // Handle file uploads
            $fileFields = [];
            foreach (['kra_pin_file', 'national_id_file', 'title_document'] as $fileField) {
                if ($request->hasFile($fileField)) {
                    // Store in storage/app/public/water-applications
                    $fileFields[$fileField] = $request->file($fileField)->store('water-applications', 'public');
                }
            }

            // Save water connection application only
            WaterConnectionApplication::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'gender' => $validated['gender'],
                'kra_pin' => $validated['kra_pin'],
                'kra_pin_file' => $fileFields['kra_pin_file'] ?? null,
                'national_id' => $validated['national_id'],
                'national_id_file' => $fileFields['national_id_file'] ?? null,
                'plot_number' => $validated['plot_number'],
                'house_number' => $validated['house_number'],
                'estate' => $validated['estate'] ?? null,
                'expected_users' => $validated['expected_users'] ?? null,
                'property_owner' => $validated['property_owner'],
                'title_document' => $fileFields['title_document'] ?? null,
                'signature' => $validated['signature'] ?? null,
                'date' => $validated['date'],
                'status' => 'pending',
            ]);

           return redirect()->route('water-connection.apply')
            ->with('success', 'Application submitted successfully! Your application is under review.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error submitting application: ' . $e->getMessage());
        }
    }

    // Admin methods to view applications
    public function index(Request $request)
    {
        $status = $request->get('status');
        $search = $request->get('search');
        
        $applications = WaterConnectionApplication::when($status, function($query, $status) {
                return $query->where('status', $status);
            })
            ->when($search, function($query) use ($search) {
                return $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('national_id', 'like', "%{$search}%")
                    ->orWhere('kra_pin', 'like', "%{$search}%")
                    ->orWhere('plot_number', 'like', "%{$search}%")
                    ->orWhere('house_number', 'like', "%{$search}%")
                    ->orWhere('estate', 'like', "%{$search}%")
                    ->orWhere('property_owner', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20);

        return view('admin.water-applications.index', compact('applications', 'status', 'search'));
    }

    public function show(WaterConnectionApplication $application)
    {
        return view('admin.water-applications.show', compact('application'));
    }

    // Simple approve/decline methods
    public function approve(Request $request, WaterConnectionApplication $application)
{
    $validated = $request->validate([
        'meter_number' => 'required|string|max:50|unique:customers,meter_number',
        'meter_type' => 'required|string|in:domestic,commercial,industrial,institutional',
        'connection_type' => 'required|string|in:residential,commercial,industrial,public',
        'initial_meter_reading' => 'required|numeric|min:0',
        'connection_date' => 'required|date',
        'notes' => 'nullable|string|max:1000',
    ]);

    try {
        DB::transaction(function () use ($application, $validated) {
            // Create Customer
            $customer = Customer::create([
                'first_name' => $application->first_name,
                'last_name' => $application->last_name,
                'email' => $application->email,
                'phone' => $application->phone,
                'id_number' => $application->national_id,
                'physical_address' => $application->plot_number . ', ' . $application->house_number . ($application->estate ? ', ' . $application->estate : ''),
                'plot_number' => $application->plot_number,
                'house_number' => $application->house_number,
                'estate' => $application->estate,
                'meter_number' => $validated['meter_number'],
                'meter_type' => $validated['meter_type'],
                'connection_type' => $validated['connection_type'],
                'initial_meter_reading' => $validated['initial_meter_reading'],
                'connection_date' => $validated['connection_date'],
                'status' => 'active',
                'kra_pin' => $application->kra_pin,
                'property_owner' => $application->property_owner,
                'expected_users' => $application->expected_users,
                'notes' => $validated['notes'] ?? 'Approved water connection application',
            ]);

            // Create or Assign Meter
            $meter = Meter::create([
                'meter_number' => $validated['meter_number'],
                'meter_type' => $validated['meter_type'],
                'customer_id' => $customer->id,
                'installation_address' => $application->plot_number . ', ' . $application->house_number . ($application->estate ? ', ' . $application->estate : ''),
                'installation_date' => $validated['connection_date'],
                'initial_reading' => $validated['initial_meter_reading'],
                'status' => 'assigned',
                'notes' => 'Assigned during application approval',
            ]);

            // Create initial meter reading
            MeterReading::create([
                'customer_id' => $customer->id,
                'meter_id' => $meter->id,
                'current_reading' => $validated['initial_meter_reading'],
                'previous_reading' => 0,
                'consumption' => $validated['initial_meter_reading'],
                'reading_date' => $validated['connection_date'],
                'reading_type' => 'initial',
                'reading_period' => 'Initial Installation',
                'billed' => false,
                'read_by' => auth()->id(),
                'notes' => 'Initial meter reading upon connection approval',
            ]);

            // Update application status and link to customer
            $application->update([
                'status' => 'approved',
                'customer_id' => $customer->id,
            ]);
        });

        return redirect()->route('admin.water-applications.index')
            ->with('success', 'Application approved successfully! Customer created and meter ' . $validated['meter_number'] . ' assigned.');

    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Error approving application: ' . $e->getMessage());
    }
}

    public function decline(Request $request, WaterConnectionApplication $application)
    {
        $validated = $request->validate([
            'decline_reason' => 'required|string|max:1000',
        ]);

        $application->update([
            'status' => 'declined',
            'decline_reason' => $validated['decline_reason'],
        ]);

        return redirect()->route('admin.water-applications.index')
            ->with('success', 'Application declined successfully.');
    }
}