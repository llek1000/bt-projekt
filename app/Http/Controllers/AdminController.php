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
    public function getUsers()
    {
        try {
            $users = User::with('roles')->get();
            return $this->success(['users' => $users]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Chyba pri načítavaní používateľov', 'Nepodarilo sa načítať používateľov');
        }
    }

    public function createUser(Request $request)
    {
        return $this->executeWithTransaction(function() use ($request) {
            $errors = $this->validateRequest($request, [
                'username' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'roles' => 'required|array',
            ], [
                'username.required' => 'Meno používateľa je povinné',
                'email.required' => 'Email je povinný',
                'email.unique' => 'Tento email už existuje',
                'password.required' => 'Heslo je povinné',
                'password.confirmed' => 'Potvrdenie hesla sa nezhoduje',
                'roles.required' => 'Role sú povinné',
            ]);

            if ($errors) return $this->error('Neplatné údaje', $errors, 422);

            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $roles = Role::whereIn('name', $request->roles)->get();
            $user->roles()->attach($roles);

            return $this->success(['user' => $user->load('roles')], null, 201);
        }, 'Nepodarilo sa vytvoriť používateľa');
    }

    public function updateUser(Request $request, $id)
    {
        return $this->executeWithTransaction(function() use ($request, $id) {
            $user = User::findOrFail($id);
            $currentUser = Auth::user();

            // Admin protection logic
            if ($this->hasRole($user, 'Admin') && $user->id !== $currentUser->id) {
                return $this->error('Nemôžete upraviť účet iného administrátora', 'admin_protection', 403);
            }

            $errors = $this->validateRequest($request, [
                'username' => 'sometimes|string|max:255',
                'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'password' => 'sometimes|string|min:8|confirmed',
                'roles' => 'sometimes|array',
            ]);

            if ($errors) return $this->error('Neplatné údaje', $errors, 422);

            // Update user fields
            if ($request->has('username')) $user->username = $request->username;
            if ($request->has('email')) $user->email = $request->email;
            if ($request->has('password')) $user->password = Hash::make($request->password);

            $user->save();

            // Update roles if provided
            if ($request->has('roles')) {
                $roles = Role::whereIn('name', $request->roles)->get();
                $user->roles()->sync($roles);
            }

            return $this->success(['user' => $user->load('roles')]);
        }, 'Nepodarilo sa aktualizovať používateľa');
    }

    public function deleteUser($id)
    {
        return $this->executeWithTransaction(function() use ($id) {
            $user = User::findOrFail($id);
            $currentUser = Auth::user();

            if ($this->hasRole($user, 'Admin')) {
                return $this->error('Nemôžete vymazať účet administrátora', 'admin_protection', 403);
            }

            if ($user->id === $currentUser->id) {
                return $this->error('Nemôžete vymazať svoj vlastný účet', 'self_deletion_protection', 403);
            }

            $user->delete();
            return $this->success([], 'Používateľ bol úspešne vymazaný');
        }, 'Nepodarilo sa vymazať používateľa');
    }

    public function getRoles()
    {
        try {
            $roles = Role::all();
            return $this->success(['roles' => $roles]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Chyba pri načítavaní rolí', 'Nepodarilo sa načítať role');
        }
    }
}
