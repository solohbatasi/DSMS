<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin account
        $admin = User::firstOrCreate([
            'email' => 'admin@mail.com',
        ], [
            'name' => 'Admin User',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'admin',
        ]);
        $admin->assignRole('admin');

        // Developer account
        $developer = User::firstOrCreate([
            'email' => 'developer@mail.com',
        ], [
            'name' => 'Developer User',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'developer',
        ]);
        $developer->assignRole('developer');

        // Receptionist
        $receptionist = User::firstOrCreate([
            'email' => 'reception@mail.com',
        ], [
            'name' => 'Reception Desk',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'receptionist',
        ]);
        $receptionist->assignRole('receptionist');

          // Receptionist
        $instructor = User::firstOrCreate([
            'email' => 'instructor@mail.com',
        ], [
            'name' => 'instructor',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'instructor',
        ]);
        $instructor->assignRole('instructor');

        // Reporting officer
        $report = User::firstOrCreate([
            'email' => 'report@mail.com',
        ], [
            'name' => 'Report Officer',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'report',
        ]);
        $report->assignRole('report');
    }
}
