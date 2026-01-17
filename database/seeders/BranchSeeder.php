<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use Illuminate\Support\Str;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            [
                'branch_code' => '001',
                'name' => 'Eastleigh',
                'address' => '14th street',
                'contact_phone' => '+1234567890',
                'contact_email' => 'info@drivingschool.com',
                'status' => 'active'
            ],
            [
                'branch_code' => '002',
                'name' => 'Matasia',
                'address' => 'Matasia',
                'contact_phone' => '+1234567891',
                'contact_email' => 'matasia@drivingschool.com',
                'status' => 'active'
            ],
            [
                'branch_code' => '003',
                'name' => 'Kipselion',
                'address' => 'Kipseion',
                'contact_phone' => '+1234567892',
                'contact_email' => 'west@drivingschool.com',
                'status' => 'active'
            ]
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}
