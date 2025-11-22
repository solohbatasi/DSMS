<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@mail.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'admin',
        ]);
        $admin->assignRole('admin');

        // Create developer user
        $developer = User::create([
            'name' => 'Developer',
            'email' => 'developer@mail.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'developer',
        ]);
        $developer->assignRole('developer');

        // Create manager user
        $manager = User::create([
            'name' => 'Manager',
            'email' => 'manager@mail.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'manager',
        ]);
        $manager->assignRole('manager');

        // Create CEO user
        $ceo = User::create([
            'name' => 'CEO',
            'email' => 'ceo@mail.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'ceo',
        ]);
        $ceo->assignRole('ceo');

          // Create Registar user
        $registrar = User::create([
            'name' => 'Registar',
            'email' => 'registrar@mail.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'registrar',
        ]);
        $registrar->assignRole('registrar');



          // Create Report user
        $report = User::create([
            'name' => 'Reports',
            'email' => 'report@mail.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'report',
        ]);
        $report->assignRole('report');

            // Create Chief user
        $chief = User::create([
            'name' => 'Chief',
            'email' => 'chief@mail.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'chief',
        ]);
        $chief->assignRole('chief');

        $ict = User::create([
            'name' => 'ICT',
            'email' => 'ict@mail.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'ict',
        ]);
        $ict->assignRole('ict');
    }
}
