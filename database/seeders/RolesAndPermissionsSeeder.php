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
        // ✅ Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ✅ Define permissions
        $permissions = [
            // User management
            'add users',
            'view users',
            'edit users',
            'delete users',
            'manage roles',

            // Students management
            'add students',
            'view students',
            'edit students',
            'delete students',

            // Permissions management
            'view permissions',
            'edit permissions',

            // Payments management
            'add payments',
            'view payments',
            'edit payments',
            'delete payments',

            // expenses management
            'add expenses',
            'view expenses',
            'edit expenses',
            'delete expenses',

            // Results management
            'add results',
            'view results',
            'edit results',
            'delete results',

            // applications management
            'add applications',
            'view applications',
            'edit applications',
            'delete applications',

            // reports management
            'add reports',
            'view reports',
            'edit reports',
            'delete reports',

            // System
            'access admin panel',
            'manage system',
        ];

        // ✅ Create permissions safely (avoiding duplicates)
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ✅ Create roles safely
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $ictRole = Role::firstOrCreate(['name' => 'ict']);
        $developerRole = Role::firstOrCreate(['name' => 'developer']);
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $ceoRole = Role::firstOrCreate(['name' => 'ceo']);
        $chiefRole = Role::firstOrCreate(['name' => 'chief']);
        $billerRole = Role::firstOrCreate(['name' => 'biller']);
        $registrarRole = Role::firstOrCreate(['name' => 'registrar']);
        $reportRole = Role::firstOrCreate(['name' => 'report']);

        // ✅ Assign permissions per role
        $developerRole->syncPermissions(Permission::all());

        $adminRole->syncPermissions([
            // User management
            'add users',
            'view users',
            'edit users',
            'delete users',
            'manage roles',

            // Students management
            'add students',
            'view students',
            'edit students',
            'delete students',

            // Permissions management
            'view permissions',
            'edit permissions',

            // Payments management
            'add payments',
            'view payments',
            'edit payments',
            'delete payments',

            // expenses management
            'add expenses',
            'view expenses',
            'edit expenses',
            'delete expenses',

            // Students management
            'add students',
            'view students',
            'edit students',
            'delete students',

            // Results management
            'add results',
            'view results',
            'edit results',
            'delete results',

            // applications management
            'add applications',
            'view applications',
            'edit applications',
            'delete applications',

            // reports management
            'add reports',
            'view reports',
            'edit reports',
            'delete reports',
        ]);

        $ictRole->syncPermissions([

        ]);

        $managerRole->syncPermissions([
            // User management
            'add users',
            'view users',

             // Students management
            'add students',
            'view students',


            // Payments management
            'add payments',
            'view payments',

            // applications management
            'add applications',
            'view applications',

            // reports management
            'add reports',
            'view reports',

        ]);

        $ceoRole->syncPermissions([
            // User management
            'add users',
            'view users',

            // Students management
            'add students',
            'view students',



            // Payments management
            'add payments',
            'view payments',

            // applications management
            'add applications',
            'view applications',

            // reports management
            'add reports',
            'view reports',

        ]);

        $chiefRole->syncPermissions([
            // User management
            'add users',
            'view users',


             // Students management
            'add students',
            'view students',

            // applications management
            'add applications',
            'view applications',

            // reports management
            'add reports',
            'view reports',

        ]);


        $registrarRole->syncPermissions([

            // Students management
            'add students',
            'view students',
            'edit students',



            // applications management
            'add applications',
            'view applications',

        ]);

        $reportRole->syncPermissions([

            // reports management
            'add reports',
            'view reports',
            'edit reports',
            'delete reports',

        ]);

        // ✅ Optional: Automatically assign admin role to the first admin user
        $adminUser = User::where('email', 'admin@mail.com')->first();
        if ($adminUser && !$adminUser->hasRole('admin')) {
            $adminUser->assignRole('admin');
        }

        $developerUser = User::where('email', 'developer@mail.com')->first();
        if ($developerUser && !$developerUser->hasRole('developer')) {
            $developerUser->assignRole('developer');
        }

        // ✅ Refresh Spatie cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
