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
    $user = Auth::user();

    // Only admin can create users
    if (!$user->isAdmin()) {
        return response()->json([
            'success' => false,
            'message' => 'Only administrators can create users.'
        ], 403);
    }

    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:4',
        'role' => 'required|string|max:50',
        'branch_id' => ['nullable', 'exists:branches,id'],
        'is_active' => 'required|boolean'
    ]);

    // Validate branch assignment based on role
    if ($request->role === 'admin') {
        // Admin can have null branch or any branch
        $branchId = $request->branch_id;
    } elseif (in_array($request->role, ['branch_manager', 'instructor', 'staff', 'receptionist'])) {
        // Branch-specific roles require a branch
        if (!$request->branch_id) {
            return response()->json([
                'success' => false,
                'message' => 'Branch is required for this role.'
            ], 422);
        }
        $branchId = $request->branch_id;
    } else {
        // Other roles (like student) might have different logic
        $branchId = $request->branch_id;
    }

    try {
        DB::transaction(function () use ($request, $branchId) {
            // Create or get the role
            $role = Role::firstOrCreate(['name' => $request->role]);

            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'email_verified_at' => now(),
                'password' => Hash::make($request->password),
                'branch_id' => $branchId,
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
    $currentUser = Auth::user();

    // Only admin can update users
    if (!$currentUser->isAdmin()) {
        return response()->json([
            'success' => false,
            'message' => 'Only administrators can update users.'
        ], 403);
    }

    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'role' => 'required|string|max:50',
        'branch_id' => ['nullable', 'exists:branches,id'],
        'is_active' => 'required|boolean'
    ]);

    // Validate branch assignment based on role
    if ($request->role === 'admin') {
        // Admin can have null branch or any branch
        $branchId = $request->branch_id;
    } elseif (in_array($request->role, ['branch_manager', 'instructor', 'staff', 'receptionist'])) {
        // Branch-specific roles require a branch
        if (!$request->branch_id) {
            return response()->json([
                'success' => false,
                'message' => 'Branch is required for this role.'
            ], 422);
        }
        $branchId = $request->branch_id;
    } else {
        $branchId = $request->branch_id;
    }

    try {
        DB::transaction(function () use ($request, $user, $branchId) {
            // Create or get the role
            $role = Role::firstOrCreate(['name' => $request->role]);

            // Update user data
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $role->name,
                'branch_id' => $branchId,
                'is_active' => $request->boolean('is_active'),
                'email_verified_at' => $user->email_verified_at ?? now(),
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
            // Get the user's primary role
            $role = $user->roles->first();

            if (!$role) {
                return response()->json([
                    'success' => false,
                    'message' => 'This user has no role assigned.'
                ], 400);
            }

            // Sync permissions on the role instead of the user
            $role->syncPermissions($request->permissions ?? []);

            return response()->json([
                'success' => true,
                'message' => 'Role permissions updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update role permissions: ' . $e->getMessage()
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
