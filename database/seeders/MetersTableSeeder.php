<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Meter;
use App\Models\Customer;

class MetersTableSeeder extends Seeder
{
    public function run()
    {
        Meter::truncate();

        $customers = Customer::all();

        foreach ($customers as $customer) {
            Meter::create([
                'meter_number'        => $customer->meter_number,
                'meter_type'          => 'domestic',
                'meter_model'         => 'Model-X',
                'manufacturer'        => 'AquaTech',
                'status'              => 'active',
                'customer_id'         => $customer->id,
                'installation_address'=> $customer->physical_address,
                'installation_date'   => now()->subMonths(rand(1, 12)),
                'last_maintenance_date'=> now()->subMonths(rand(1, 6)),
                'initial_reading'     => $customer->initial_meter_reading,
            ]);
        }
    }
}
