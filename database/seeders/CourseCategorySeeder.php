<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CourseCategory;
use App\Models\Branch;

class CourseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        $categories = [
            [
                'name' => 'Computer',
                'description' => 'Computer and IT related courses',
                'status' => true,

            ],
            [
                'name' => 'Driving',
                'description' => 'Driving license and road safety courses',
                'status' => true,

            ],

        ];

        foreach ($categories as $category) {
            CourseCategory::create($category);
        }

        $this->command->info('Course categories seeded successfully!');
    }
}
