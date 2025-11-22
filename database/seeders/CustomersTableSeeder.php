<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use Illuminate\Support\Str;

class CustomersTableSeeder extends Seeder
{
    public function run()
    {
        Customer::truncate();

        for ($i = 1; $i <= 10; $i++) {
            Customer::create([
                'customer_number'  => 'CUST-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'first_name'       => 'Customer' . $i,
                'last_name'        => 'Test',
                'email'            => "customer$i@test.com",
                'phone'            => '07' . rand(10000000, 99999999),
                'id_number'        => rand(10000000, 99999999),
                'physical_address' => 'Nakuru, Kenya',
                'plot_number'      => 'Plot-' . rand(1, 99),
                'house_number'     => 'H-' . rand(1, 99),
                'estate'           => 'Freehold',
                'meter_number'     => 'MTR-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'meter_type'       => 'domestic',
                'connection_type'  => 'residential',
                'initial_meter_reading' => rand(0, 10),
                'connection_date'  => now()->subMonths(rand(1, 12)),
                'status'           => 'active',
                'kra_pin'          => 'A' . Str::upper(Str::random(8)),
                'property_owner'   => 'Owner ' . $i,
                'expected_users'   => rand(1, 10),
            ]);
        }
    }
}
