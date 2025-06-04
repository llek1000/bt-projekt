<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
        try {
            $users = User::with('roles')->get();
            return response()->json(['users' => $users]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Nepodarilo sa načítať používateľov',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new user
     */
    public function createUser(Request $request)
    {
        try {
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

            DB::beginTransaction();

            $user = User::create([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            if (!$user) {
                throw new \Exception('Nepodarilo sa vytvoriť používateľa');
            }

            if (isset($validated['roles'])) {
                $roles = Role::whereIn('name', $validated['roles'])->get();
                if ($roles->isEmpty()) {
                    throw new \Exception('Vybrané role neboli nájdené');
                }
                $user->roles()->attach($roles);
            }

            DB::commit();

            return response()->json(['user' => $user->load('roles')], 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Neplatné údaje',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Nepodarilo sa vytvoriť používateľa',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing user
     */
    public function updateUser(Request $request, $id)
    {
        try {
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

            DB::beginTransaction();

            if (isset($validated['username'])) {
                $user->username = $validated['username'];
            }

            if (isset($validated['email'])) {
                $user->email = $validated['email'];
            }

            if (isset($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            if (!$user->save()) {
                throw new \Exception('Nepodarilo sa aktualizovať používateľa');
            }

            if (isset($validated['roles'])) {
                if ($this->isAdmin($user) && $user->id !== $currentUser->id) {
                    $hasAdminRole = in_array('Admin', $validated['roles']);
                    if (!$hasAdminRole) {
                        DB::rollBack();
                        return response()->json([
                            'message' => 'Nemôžete odobrať rolu administrátora inému administrátorovi',
                            'error' => 'admin_protection'
                        ], 403);
                    }
                }

                $roles = Role::whereIn('name', $validated['roles'])->get();
                if ($roles->isEmpty()) {
                    throw new \Exception('Vybrané role neboli nájdené');
                }
                $user->roles()->sync($roles);
            }

            DB::commit();

            return response()->json(['user' => $user->load('roles')]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Používateľ nebol nájdený'
            ], 404);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Neplatné údaje',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Nepodarilo sa aktualizovať používateľa',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a user
     */
    public function deleteUser($id)
    {
        try {
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

            if (!$user->delete()) {
                throw new \Exception('Nepodarilo sa vymazať používateľa');
            }

            return response()->json(['message' => 'Používateľ bol úspešne vymazaný']);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Používateľ nebol nájdený'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Nepodarilo sa vymazať používateľa',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getRoles()
    {
        try {
            $roles = Role::all();
            return response()->json(['roles' => $roles]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Nepodarilo sa načítať role',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function assignUserRole(Request $request, $userId)
    {
        try {
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

            DB::beginTransaction();

            $user->roles()->sync([$role->id]);

            if (!$user->roles()->where('id', $role->id)->exists()) {
                throw new \Exception('Nepodarilo sa priradiť rolu');
            }

            DB::commit();

            return response()->json([
                'message' => 'Rola bola úspešne priradená',
                'user' => $user->load('roles')
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Používateľ alebo rola neboli nájdené'
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Neplatné údaje',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Nepodarilo sa priradiť rolu',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
