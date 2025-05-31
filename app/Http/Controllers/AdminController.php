<?php


namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Get all users
     */
    public function getUsers()
    {
        return response()->json(['users' => User::with('roles')->get()]);
    }

    /**
     * Create a new user
     */
    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array',
        ]);

        $user = User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        if (isset($validated['roles'])) {
            $roles = Role::whereIn('name', $validated['roles'])->get();
            $user->roles()->attach($roles);
        }

        return response()->json(['user' => $user->load('roles')], 201);
    }

    /**
     * Update an existing user
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'username' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'sometimes|string|min:8|confirmed',
            'roles' => 'sometimes|array',
        ]);

        if (isset($validated['username'])) {
            $user->username = $validated['username'];
        }

        if (isset($validated['email'])) {
            $user->email = $validated['email'];
        }

        if (isset($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        if (isset($validated['roles'])) {
            $roles = Role::whereIn('name', $validated['roles'])->get();
            $user->roles()->sync($roles);
        }

        return response()->json(['user' => $user->load('roles')]);
    }

    /**
     * Delete a user
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    /**
     * Get all roles
     */
    public function getRoles()
    {
        return response()->json(['roles' => Role::all()]);
    }

    /**
     * Assign a role to a user
     */
    public function assignUserRole(Request $request, $userId)
    {
        $validated = $request->validate([
            'roleId' => 'required|exists:roles,id',
        ]);

        $user = User::findOrFail($userId);
        $role = Role::findOrFail($validated['roleId']);


        // Check if user is an admin and is being demoted
        $isAdmin = $user->roles()->where('name', 'Admin')->exists();
        $isNewRoleAdmin = $role->name === 'Admin';

        // Prevent demoting admin users
        if ($isAdmin && !$isNewRoleAdmin) {
            return response()->json([
                'message' => 'Cannot demote an administrator account',
                'error' => 'admin_protection'
            ], 403);
        }

        $user->roles()->sync([$role->id]);

        return response()->json([
            'message' => 'Role assigned successfully',
            'user' => $user->load('roles')
        ]);
    }
}
