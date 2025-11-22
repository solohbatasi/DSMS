<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoursesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('courses')->insert([
            [
                'course' => 'B1 Automatic Vehicle',
                'description' => 'This Package is for learning to drive automa',
                'training_duration' => '21',
                'total_amount' => 35000,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'course' => 'B2 Manual Vehicle',
                'description' => 'This Package is for learning to drive a',
                'training_duration' => '19',
                'total_amount' => 40000,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'course' => 'B3 Proffessional',
                'description' => 'Minimum requirements: Age: 21 minimum Vehicle description and restriction',
                'training_duration' => '19',
                'total_amount' => 25000,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'course' => 'B',
                'description' => 'Both Manual & Automatic Light vehicle',
                'training_duration' => '30',
                'total_amount' => 30000,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'course' => 'A1',
                'description' => 'This package is for Motorcycle training',
                'training_duration' => '20',
                'total_amount' => 30000,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
