<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Store New User with Spatie Roles
     */


public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:4',
        'role' => 'required|string|max:50',
        'is_active' => 'required|boolean'
    ]);

    try {
        DB::transaction(function () use ($request) {

            // Create or get the role
            $role = Role::firstOrCreate(['name' => $request->role]);

            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'email_verified_at' => now(), // mark email as verified
                'password' => Hash::make($request->password),
                'role' => $role->name,
                'is_active' => $request->boolean('is_active'),
            ]);

            // Assign role using Spatie
            $user->assignRole($role);
        });

        return response()->json([
            'success' => true,
            'message' => 'User created successfully'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to create user: ' . $e->getMessage()
        ], 500);
    }
}

public function update(Request $request, User $user)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'role' => 'required|string|max:50',
        'is_active' => 'required|boolean'
    ]);

    try {
        DB::transaction(function () use ($request, $user) {

            // Create or get the role
            $role = Role::firstOrCreate(['name' => $request->role]);

            // Update user data
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $role->name,
                'is_active' => $request->boolean('is_active'),
                'email_verified_at' => $user->email_verified_at ?? now(), // verify if not verified
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            // Sync role using Spatie
            $user->syncRoles([$role]);
        });

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to update user: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Get User Permissions
     */
    public function getPermissions(User $user)
    {
        try {
            $permissions = $user->getAllPermissions()->pluck('name');

            return response()->json([
                'success' => true,
                'permissions' => $permissions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load permissions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update User Permissions
     */
    public function updatePermissions(Request $request, User $user)
    {
        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        try {
            // Sync direct permissions
            $user->syncPermissions($request->permissions ?? []);

            return response()->json([
                'success' => true,
                'message' => 'Permissions updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update permissions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete User
     */
    public function destroy(User $user)
    {
        // Prevent users from deleting themselves
        if ($user->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account'
            ], 403);
        }

        try {
            // Remove all roles and permissions
            $user->roles()->detach();
            $user->permissions()->detach();

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ], 500);
        }
    }
}
