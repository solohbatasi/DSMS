<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Student;
use App\Models\Student_ef_list;
use App\Models\User;
use App\Models\Course;
use App\Models\Payment;
use Faker\Factory as Faker;

class MassiveStudentSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        $courses = Course::all(); // get all available courses
        if ($courses->isEmpty()) {
            $this->command->info('No courses found. Seed courses first.');
            return;
        }

        $batchSize = 50; // insert 500 at a time to avoid memory overload
        $totalStudents = 20000;
        $studentData = [];
        $efData = [];
        $userData = [];
        $paymentData = [];
        $now = now();

        for ($i = 1; $i <= $totalStudents; $i++) {
            $address = $faker->optional()->address;
            $id_no = $faker->unique()->numerify('#######');
            $name = $faker->name();
            $contact = $faker->optional()->numerify('07########');
            $email = $faker->boolean(820) ? $faker->unique()->safeEmail() : null;
            $studentData[] = [
                'id_no' => $id_no,
                'name' => $name,
                'contact' => $contact,
                'email' => $email,
                'address' => $address,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            // pick a random course
            $course = $courses->random();
            $total_fee = $course->total_amount;
            $total_class = $course->training_duration;

            $ef_no = $faker->unique()->numerify('#######');

            $efData[] = [
                'student_id' => $i, // since auto-increment ID will match sequential insert
                'ef_no' => $ef_no,
                'course_id' => $course->id,
                'total_fee' => $total_fee,
                'total_class' => $total_class,
                'type' => '3',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ];

            // User record for login
            $userData[] = [
                'name' => $name,
                'email' => $email ?? $id_no.'@example.com',
                'password' => Hash::make($id_no ?? 'Password123'),
                'role' => 'student',
                'created_at' => $now,
                'updated_at' => $now,
            ];

            // Optional: create one payment for each EF
            $paymentData[] = [
                'ef_id' => $i, // same as EF list ID, sequential
                'amount' => $total_fee,
                'remarks' => 'Initial payment',
                'payment_status' => 'paid',
                'payment_method' => $faker->randomElement(['Cash', 'MPESA', 'Bank Transfer']),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            // Insert in batches
            if ($i % $batchSize === 0 || $i === $totalStudents) {
                DB::table('students')->insert($studentData);
                DB::table('student_ef_lists')->insert($efData);
                DB::table('users')->insert($userData);
                DB::table('payments')->insert($paymentData);

                // reset arrays
                $studentData = [];
                $efData = [];
                $userData = [];
                $paymentData = [];

                $this->command->info("Inserted $i students...");
            }
        }

        $this->command->info('Massive student seeding completed!');
    }
}

