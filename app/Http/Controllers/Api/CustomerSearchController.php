<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CustomerSearchController extends Controller
{
    public function search(Request $request)
    {
        try {
            Log::info('Customer search API called', ['search' => $request->get('search')]);

            $search = $request->get('search', '');
            
            if (empty($search)) {
                return response()->json([]);
            }

            // Enhanced search with meter relationships and recent reading check
            $customers = Customer::with(['meter', 'meterReadings' => function($query) {
                $query->latest()->limit(1);
            }])
            ->where('status', 'active')
            ->where(function($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('customer_number', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('id_number', 'like', "%{$search}%")
                      ->orWhere('plot_number', 'like', "%{$search}%")
                      ->orWhere('house_number', 'like', "%{$search}%")
                      ->orWhere('estate', 'like', "%{$search}%")
                      ->orWhereHas('meter', function($meterQuery) use ($search) {
                          $meterQuery->where('meter_number', 'like', "%{$search}%")
                                    ->orWhere('meter_type', 'like', "%{$search}%");
                      });
            })
            ->limit(20)
            ->get()
            ->map(function($customer) {
                $latestReading = $customer->meterReadings->first();
                $hasRecentReading = false;
                $recentReadingInfo = null;
                
                // Check if there's a reading in the current month
                if ($latestReading) {
                    $currentMonth = Carbon::now()->format('Y-m');
                    $readingMonth = Carbon::parse($latestReading->reading_date)->format('Y-m');
                    $hasRecentReading = ($currentMonth === $readingMonth);
                    
                    if ($hasRecentReading) {
                        $recentReadingInfo = [
                            'reading_date' => $latestReading->reading_date,
                            'current_reading' => $latestReading->current_reading,
                            'reading_period' => $latestReading->reading_period,
                            'days_ago' => Carbon::parse($latestReading->reading_date)->diffInDays(Carbon::now())
                        ];
                    }
                }
                
                return [
                    'id' => $customer->id,
                    'first_name' => $customer->first_name,
                    'last_name' => $customer->last_name,
                    'customer_number' => $customer->customer_number,
                    'phone' => $customer->phone,
                    'email' => $customer->email,
                    'plot_number' => $customer->plot_number,
                    'house_number' => $customer->house_number,
                    'estate' => $customer->estate,
                    'physical_address' => $customer->physical_address,
                    'meter' => $customer->meter ? [
                        'id' => $customer->meter->id,
                        'meter_number' => $customer->meter->meter_number,
                        'meter_type' => $customer->meter->meter_type,
                        'meter_model' => $customer->meter->meter_model,
                        'current_reading' => $customer->meter->current_reading,
                        'initial_reading' => $customer->meter->initial_reading,
                        'installation_date' => $customer->meter->installation_date,
                        'installation_address' => $customer->meter->installation_address,
                        'status' => $customer->meter->status,
                    ] : null,
                    'last_reading' => $latestReading ? [
                        'id' => $latestReading->id,
                        'current_reading' => $latestReading->current_reading,
                        'previous_reading' => $latestReading->previous_reading,
                        'consumption' => $latestReading->consumption,
                        'reading_date' => $latestReading->reading_date,
                        'reading_period' => $latestReading->reading_period,
                        'billed' => $latestReading->billed,
                    ] : null,
                    'has_recent_reading' => $hasRecentReading,
                    'recent_reading_info' => $recentReadingInfo
                ];
            });

            Log::info('Search completed', ['found' => $customers->count()]);

            return response()->json($customers);

        } catch (\Exception $e) {
            Log::error('Customer search error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Search failed: ' . $e->getMessage()
            ], 500);
        }
    }
}