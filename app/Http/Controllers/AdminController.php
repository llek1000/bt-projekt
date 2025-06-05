<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\ConferenceYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Get all users
     */
    public function getUsers()
    {
        return $this->executeWithTransaction(function() {
            $users = User::with('roles')->get();
            return $this->success(['users' => $users]);
        }, 'Nepodarilo sa načítať používateľov');
    }

    /**
     * Create a new user
     */
    public function createUser(Request $request)
    {
        return $this->executeWithTransaction(function() use ($request) {
            $errors = $this->validateRequest($request, [
                'username' => 'required|string|max:255|unique:users',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8',
                'password_confirmation' => 'required|same:password',
                'roles' => 'required|array',
            ], [
                'username.required' => 'Meno používateľa je povinné',
                'username.unique' => 'Meno používateľa už existuje',
                'email.required' => 'Email je povinný',
                'email.email' => 'Email nie je v správnom formáte',
                'email.unique' => 'Email už existuje',
                'password.required' => 'Heslo je povinné',
                'password.min' => 'Heslo musí mať aspoň 8 znakov',
                'password_confirmation.same' => 'Heslá sa nezhodujú',
                'roles.required' => 'Role sú povinné',
                'roles.array' => 'Role musia byť v správnom formáte',
            ]);

            if ($errors) return $this->error('Neplatné údaje', $errors, 422);

            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            if (!$user) {
                throw new \Exception('Nepodarilo sa vytvoriť používateľa');
            }

            if ($request->roles) {
                foreach ($request->roles as $roleName) {
                    $role = Role::where('name', $roleName)->first();
                    if ($role) {
                        $user->roles()->attach($role->id);
                    }
                }
            }

            return $this->success(['user' => $user->load('roles')], 'Používateľ bol vytvorený', 201);
        }, 'Chyba pri vytváraní používateľa');
    }

    /**
     * Update an existing user
     */
    public function updateUser(Request $request, $id)
    {
        return $this->executeWithTransaction(function() use ($request, $id) {
            $user = User::findOrFail($id);
            $currentUser = Auth::user();

            if ($this->hasRole($user, 'admin') && $user->id !== $currentUser->id) {
                return $this->error('Nemôžete upraviť účet iného administrátora', 'admin_protection', 403);
            }

            $errors = $this->validateRequest($request, [
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

            if ($errors) return $this->error('Neplatné údaje', $errors, 422);

            if ($request->has('username')) {
                $user->username = $request->username;
            }

            if ($request->has('email')) {
                $user->email = $request->email;
            }

            if ($request->has('password')) {
                $user->password = Hash::make($request->password);
            }

            if (!$user->save()) {
                throw new \Exception('Nepodarilo sa aktualizovať používateľa');
            }

            if ($request->has('roles')) {
                $roles = Role::whereIn('name', $request->roles)->get();
                if ($roles->isEmpty()) {
                    throw new \Exception('Vybrané role neboli nájdené');
                }
                $user->roles()->sync($roles);
            }

            return $this->success(['user' => $user->load('roles')], 'Používateľ bol aktualizovaný');
        }, 'Chyba pri aktualizácii používateľa');
    }

    /**
     * Delete a user
     */
    public function deleteUser($id)
    {
        return $this->executeWithTransaction(function() use ($id) {
            $user = User::findOrFail($id);
            $currentUser = Auth::user();

            // POUŽITIE hasRole NAMIESTO isAdmin
            if ($this->hasRole($user, 'admin')) {
                return $this->error('Nemôžete vymazať účet administrátora', 'admin_protection', 403);
            }

            if ($user->id === $currentUser->id) {
                return $this->error('Nemôžete vymazať svoj vlastný účet', 'self_deletion_protection', 403);
            }

            if (!$user->delete()) {
                throw new \Exception('Nepodarilo sa vymazať používateľa');
            }

            return $this->success([], 'Používateľ bol úspešne vymazaný');
        }, 'Chyba pri mazaní používateľa');
    }

    public function getRoles()
    {
        return $this->executeWithTransaction(function() {
            $roles = Role::all();
            return $this->success(['roles' => $roles]);
        }, 'Nepodarilo sa načítať role');
    }

    public function assignUserRole(Request $request, $userId)
    {
        return $this->executeWithTransaction(function() use ($request, $userId) {
            $errors = $this->validateRequest($request, [
                'roleId' => 'required|exists:roles,id',
            ], [
                'roleId.required' => 'ID role je povinné',
                'roleId.exists' => 'Vybraná rola neexistuje',
            ]);

            if ($errors) return $this->error('Neplatné údaje', $errors, 422);

            $user = User::findOrFail($userId);
            $role = Role::findOrFail($request->roleId);
            $currentUser = Auth::user();

            // POUŽITIE hasRole NAMIESTO isAdmin
            if ($this->hasRole($user, 'admin') && $user->id !== $currentUser->id) {
                return $this->error('Nemôžete zmeniť rolu iného administrátora', 'admin_protection', 403);
            }

            $user->roles()->sync([$role->id]);

            $roleExists = $user->roles()->where('roles.id', $role->id)->exists();
            if (!$roleExists) {
                throw new \Exception('Nepodarilo sa priradiť rolu');
            }

            return $this->success([
                'user' => $user->load('roles')
            ], 'Rola bola úspešne priradená');
        }, 'Chyba pri priraďovaní role');
    }

    /**
     * Get all users with editor role
     */
    public function getEditors()
    {
        return $this->executeWithTransaction(function() {
            $editors = User::with('roles')
                ->whereHas('roles', function($query) {
                    $query->where('name', 'editor');
                })
                ->get();

            return $this->success(['editors' => $editors]);
        }, 'Chyba pri načítavaní editorov');
    }

    /**
     * Get conference years with their editors
     */
    public function getYearsWithEditors()
    {
        return $this->executeWithTransaction(function() {
            $yearsWithEditors = ConferenceYear::with([
                'editorAssignments.user.roles'
            ])
            ->orderBy('year', 'desc')
            ->orderBy('semester', 'desc')
            ->get()
            ->map(function ($year) {
                return [
                    'id' => $year->id,
                    'year' => $year->year,
                    'semester' => $year->semester,
                    'is_active' => $year->is_active,
                    'created_at' => $year->created_at,
                    'updated_at' => $year->updated_at,
                    'editors' => $year->editorAssignments->map(function ($assignment) {
                        return [
                            'assignment_id' => $assignment->id,
                            'user_id' => $assignment->user_id,
                            'username' => $assignment->user->username,
                            'email' => $assignment->user->email,
                            'roles' => $assignment->user->roles->pluck('name')->toArray(),
                            'assigned_at' => $assignment->created_at
                        ];
                    })
                ];
            });

            return $this->success(['data' => $yearsWithEditors]);
        }, 'Chyba pri načítavaní ročníkov s editormi');
    }

    public function getSystemInfo()
    {
        return $this->executeWithTransaction(function() {
            $info = [
                'users_count' => User::count(),
                'conference_years_count' => ConferenceYear::count(),
                'active_years_count' => ConferenceYear::where('is_active', true)->count(),
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version()
            ];

            return $this->success(['data' => $info]);
        }, 'Chyba pri načítavaní systémových informácií');
    }

    public function getAllFiles()
    {
        return $this->executeWithTransaction(function() {
            $files = \App\Models\File::with('uploader')
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->success(['data' => $files]);
        }, 'Chyba pri načítavaní súborov');
    }
}
