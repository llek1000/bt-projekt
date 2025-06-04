<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\ConferenceYear;
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
        return $user->roles()->where('roles.name', 'Admin')->exists();
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
                foreach ($validated['roles'] as $roleName) {
                    $role = Role::where('name', $roleName)->first();
                    if ($role) {
                        $user->roles()->attach($role->id);
                    }
                }
            }

            DB::commit();

            return response()->json(['user' => $user->load('roles')], 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
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

            // Single admin protection check for any modification of other admin
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

            DB::beginTransaction();

            $user->roles()->sync([$role->id]);

            // Use qualified column name to avoid ambiguity
            $roleExists = $user->roles()->where('roles.id', $role->id)->exists();
            if (!$roleExists) {
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

    /**
     * Get all users with editor role
     */
    public function getEditors()
    {
        try {
            $editors = User::with('roles')
                ->whereHas('roles', function($query) {
                    $query->where('name', 'editor');
                })
                ->get();
                
            return response()->json(['editors' => $editors]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Chyba pri načítavaní editorov: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get conference years with their assigned editors
     */
    public function getYearsWithEditors()
    {
        try {
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

            return response()->json(['data' => $yearsWithEditors]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Chyba pri načítavaní ročníkov s editormi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get system info
     */
    public function getSystemInfo()
    {
        try {
            $info = [
                'users_count' => User::count(),
                'conference_years_count' => ConferenceYear::count(),
                'active_years_count' => ConferenceYear::where('is_active', true)->count(),
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version()
            ];

            return response()->json(['data' => $info]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Chyba pri načítavaní systémových informácií: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all files in system (Admin only)
     */
    public function getAllFiles()
    {
        try {
            $files = \App\Models\File::with('uploader')
                ->orderBy('created_at', 'desc')
                ->get();
                
            return response()->json(['data' => $files]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Chyba pri načítavaní súborov: ' . $e->getMessage()
            ], 500);
        }
    }
}
