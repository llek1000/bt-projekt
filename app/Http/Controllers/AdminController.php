<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Check if a user is an admin
     */
    private function isAdmin($user)
    {
        return $user->roles()->where('name', 'Admin')->exists();
    }

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
        ], [
            'username.required' => 'Meno používateľa je povinné',
            'username.string' => 'Meno používateľa musí byť text',
            'username.max' => 'Meno používateľa nesmie presiahnuť 255 znakov',
            'email.required' => 'Email je povinný',
            'email.string' => 'Email musí byť text',
            'email.email' => 'Email musí mať platný formát',
            'email.max' => 'Email nesmie presiahnuť 255 znakov',
            'email.unique' => 'Tento email už existuje',
            'password.required' => 'Heslo je povinné',
            'password.string' => 'Heslo musí byť text',
            'password.min' => 'Heslo musí mať najmenej 8 znakov',
            'password.confirmed' => 'Potvrdenie hesla sa nezhoduje',
            'roles.required' => 'Role sú povinné',
            'roles.array' => 'Role musia byť v správnom formáte',
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
        $currentUser = Auth::user();


        if ($this->isAdmin($user) && $user->id !== $currentUser->id) {
            return response()->json([
                'message' => 'Nemôžete upraviť účet iného administrátora',
                'error' => 'admin_protection'
            ], 403);
        }

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
        ], [
            'username.string' => 'Meno používateľa musí byť text',
            'username.max' => 'Meno používateľa nesmie presiahnuť 255 znakov',
            'email.string' => 'Email musí byť text',
            'email.email' => 'Email musí mať platný formát',
            'email.max' => 'Email nesmie presiahnuť 255 znakov',
            'email.unique' => 'Tento email už existuje',
            'password.string' => 'Heslo musí byť text',
            'password.min' => 'Heslo musí mať najmenej 8 znakov',
            'password.confirmed' => 'Potvrdenie hesla sa nezhoduje',
            'roles.array' => 'Role musia byť v správnom formáte',
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
            if ($this->isAdmin($user) && $user->id !== $currentUser->id) {
                $hasAdminRole = in_array('Admin', $validated['roles']);
                if (!$hasAdminRole) {
                    return response()->json([
                        'message' => 'Nemôžete odobrať rolu administrátora inému administrátorovi',
                        'error' => 'admin_protection'
                    ], 403);
                }
            }

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
        $currentUser = Auth::user();


        if ($this->isAdmin($user)) {
            return response()->json([
                'message' => 'Nemôžete vymazať účet administrátora',
                'error' => 'admin_protection'
            ], 403);
        }


        if ($user->id === $currentUser->id) {
            return response()->json([
                'message' => 'Nemôžete vymazať svoj vlastný účet',
                'error' => 'self_deletion_protection'
            ], 403);
        }

        $user->delete();
        return response()->json(['message' => 'Používateľ bol úspešne vymazaný']);
    }


    public function getRoles()
    {
        return response()->json(['roles' => Role::all()]);
    }

    public function assignUserRole(Request $request, $userId)
    {
        $validated = $request->validate([
            'roleId' => 'required|exists:roles,id',
        ], [
            'roleId.required' => 'ID role je povinné',
            'roleId.exists' => 'Vybraná rola neexistuje',
        ]);

        $user = User::findOrFail($userId);
        $role = Role::findOrFail($validated['roleId']);
        $currentUser = Auth::user();


        if ($this->isAdmin($user) && $user->id !== $currentUser->id) {
            return response()->json([
                'message' => 'Nemôžete zmeniť rolu iného administrátora',
                'error' => 'admin_protection'
            ], 403);
        }


        $isAdmin = $this->isAdmin($user);
        $isNewRoleAdmin = $role->name === 'Admin';


        if ($isAdmin && !$isNewRoleAdmin) {
            return response()->json([
                'message' => 'Nemôžete degradovať účet administrátora',
                'error' => 'admin_protection'
            ], 403);
        }

        $user->roles()->sync([$role->id]);

        return response()->json([
            'message' => 'Rola bola úspešne priradená',
            'user' => $user->load('roles')
        ]);
    }
}
