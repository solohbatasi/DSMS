<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // -------------------------------------------------------
        // 1. ALL SYSTEM PERMISSIONS
        // -------------------------------------------------------
        $permissions = [

            // Users
            'add users', 'view users', 'edit users', 'delete users', 'manage roles',

            // Students
            'add students', 'view students', 'edit students', 'delete students',

            // Payments
            'add payments', 'view payments', 'edit payments', 'delete payments',

            // Expenses
            'add expenses', 'view expenses', 'edit expenses', 'delete expenses',

            // Results
            'add results', 'view results', 'edit results', 'delete results',

            // Courses
            'add courses', 'view courses', 'edit courses', 'delete courses',

            // Applications
            'add applications', 'view applications', 'edit applications', 'delete applications',

             // Permissions
            'add permissions', 'view permissions', 'edit permissions', 'delete permissions',

            // Reports
            'add reports', 'view reports', 'edit reports', 'delete reports',

            // System
            'access admin panel',
            'manage system',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name]);
        }

        // -------------------------------------------------------
        // 2. ROLES
        // -------------------------------------------------------
        $admin         = Role::firstOrCreate(['name' => 'admin']);
        $developer     = Role::firstOrCreate(['name' => 'developer']);
        $instructor    = Role::firstOrCreate(['name' => 'instructor']);
        $student       = Role::firstOrCreate(['name' => 'student']);
        $receptionist  = Role::firstOrCreate(['name' => 'receptionist']);
        $reportRole    = Role::firstOrCreate(['name' => 'report']);

        // -------------------------------------------------------
        // 3. PERMISSIONS PER ROLE
        // -------------------------------------------------------

        // Developer → ALL permissions
        $developer->syncPermissions(Permission::all());

        // Admin → almost all system management permissions
        $admin->syncPermissions([
            'add users', 'view users', 'edit users', 'delete users', 'manage roles',

            'add students', 'view students', 'edit students', 'delete students',

            'add payments', 'view payments', 'edit payments', 'delete payments',

            'add expenses', 'view expenses', 'edit expenses', 'delete expenses',

            'add results', 'view results', 'edit results', 'delete results',

            'add courses', 'view courses', 'edit courses', 'delete courses',

            'add applications', 'view applications', 'edit applications', 'delete applications',

            'add permissions', 'view permissions', 'edit permissions', 'delete permissions',

            'add reports', 'view reports', 'edit reports', 'delete reports',

            'access admin panel',
            'manage system',
        ]);

        // Instructor → manage students, results, courses
        $instructor->syncPermissions([
            'view students', 'edit students',
            'add results', 'view results', 'edit results',
            'view courses',
        ]);

        // Student → can view own info (minimal)
        $student->syncPermissions([
            'view courses',
            'view results',
            'view applications',
        ]);

        // Receptionist → applications, payments, students
        $receptionist->syncPermissions([
            'add students', 'view students',

            'add payments', 'view payments',

            'add applications', 'view applications',

            'view courses',
        ]);

        // Report user → only reports
        $reportRole->syncPermissions([
            'add reports', 'view reports', 'edit reports', 'delete reports',
        ]);

        // -------------------------------------------------------
        // 4. AUTO-ASSIGN ROLES TO DEFAULT USERS
        // -------------------------------------------------------

        if ($adminUser = User::where('email', 'admin@mail.com')->first()) {
            $adminUser->assignRole('admin');
        }

        if ($developerUser = User::where('email', 'developer@mail.com')->first()) {
            $developerUser->assignRole('developer');
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
