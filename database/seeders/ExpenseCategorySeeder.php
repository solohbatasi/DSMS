<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExpenseCategory;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Fuel', 'description' => 'Vehicle and generator fuel expenses'],
            ['name' => 'Rent', 'description' => 'Office or premises rent'],
            ['name' => 'Maintenance', 'description' => 'Repairs and maintenance costs'],
            ['name' => 'Salaries', 'description' => 'Staff salary payments'],
            ['name' => 'Stationery', 'description' => 'Office stationery purchases'],
            ['name' => 'Utilities', 'description' => 'Electricity, water, and internet bills'],
            ['name' => 'Transport', 'description' => 'Transport and travel expenses'],
            ['name' => 'Marketing', 'description' => 'Advertising and promotion costs'],
            ['name' => 'Insurance', 'description' => 'Company or vehicle insurance payments'],
            ['name' => 'Miscellaneous', 'description' => 'General unclassified expenses'],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}
