<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bill;
use App\Models\Meter;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Str;

class BillsTableSeeder extends Seeder
{
    public function run()
    {
        Bill::truncate();

        $meters = Meter::all();
        $customers = Customer::all();
        $users = User::all();

        foreach ($meters as $meter) {
            for ($i = 1; $i <= 3; $i++) {
                $consumption = rand(5, 40);
                $base = 300;
                $charge = $consumption * 45;
                $tax = $charge * 0.16;
                $late = rand(0, 1) ? 0 : 150;

                $total = $base + $charge + $tax + $late;

                Bill::create([
                    'customer_id' => $customers->random()->id,
                    'meter_id' => $meter->id,
                    'bill_number' => 'BILL-' . Str::upper(Str::random(8)),
                    'billing_period_start' => now()->subMonths($i)->startOfMonth(),
                    'billing_period_end'   => now()->subMonths($i)->endOfMonth(),
                    'consumption'          => $consumption,
                    'base_charge'          => $base,
                    'consumption_charge'   => $charge,
                    'tax_amount'           => $tax,
                    'late_fee'             => $late,
                    'total_amount'         => $total,
                    'due_date'             => now()->addDays(14),
                    'bill_status'          => 'unpaid',
                    'created_by'           => $users->random()->id,
                ]);
            }
        }
    }
}
