<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
                'password' => 'required',
            ], [
                'email.required' => 'Email je povinný',
                'email.email' => 'Email musí mať platný formát',
                'email.exists' => 'Používateľ s týmto emailom neexistuje',
                'password.required' => 'Heslo je povinné',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Neplatné údaje',
                    'errors' => $validator->errors()
                ], 422);
            }

            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nesprávny email alebo heslo'
                ], 401);
            }

            $user = User::with('roles')->where('email', $request->email)->first();

            if (!$user) {
                throw new \Exception('Nepodarilo sa načítať údaje používateľa');
            }

            $token = $user->createToken('auth-token')->plainTextToken;

            if (!$token) {
                throw new \Exception('Nepodarilo sa vytvoriť prístupový token');
            }

            $redirectUrl = $this->getRedirectUrl($user);

            Log::info('Úspešné prihlásenie', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Prihlásenie úspešné',
                'user' => $user,
                'token' => $token,
                'redirect_url' => $redirectUrl
            ]);

        } catch (\Exception $e) {
            Log::error('Chyba pri prihlasovaní', [
                'email' => $request->email ?? 'neznámy',
                'error' => $e->getMessage(),
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Nepodarilo sa prihlásiť',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                throw new \Exception('Používateľ nie je prihlásený');
            }

            $currentToken = $request->user()->currentAccessToken();

            if (!$currentToken) {
                throw new \Exception('Neplatný prístupový token');
            }

            $currentToken->delete();

            Log::info('Úspešné odhlásenie', [
                'user_id' => $user->id,
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Odhlásenie úspešné'
            ]);

        } catch (\Exception $e) {
            Log::error('Chyba pri odhlasovaní', [
                'error' => $e->getMessage(),
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Nepodarilo sa odhlásiť',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function getRedirectUrl($user)
    {
        try {
            if (!$user) {
                throw new \Exception('Používateľ neexistuje');
            }

            if ($user->roles()->where('name', 'Admin')->exists()) {
                return '/admin/dashboard';
            }

            if ($user->roles()->where('name', 'Editor')->exists()) {
                return '/editor/dashboard';
            }

            return '/';

        } catch (\Exception $e) {
            Log::error('Chyba pri získavaní redirect URL', [
                'user_id' => $user->id ?? 'neznámy',
                'error' => $e->getMessage()
            ]);

            return '/'; // Fallback na hlavnú stránku
        }
    }

    public function me(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                throw new \Exception('Používateľ nie je prihlásený');
            }

            $userWithRoles = $user->load('roles');

            if (!$userWithRoles) {
                throw new \Exception('Nepodarilo sa načítať údaje používateľa');
            }

            $redirectUrl = $this->getRedirectUrl($userWithRoles);

            return response()->json([
                'success' => true,
                'user' => $userWithRoles,
                'redirect_url' => $redirectUrl
            ]);

        } catch (\Exception $e) {
            Log::error('Chyba pri získavaní údajov používateľa', [
                'error' => $e->getMessage(),
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Nepodarilo sa načítať údaje používateľa',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function refresh(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                throw new \Exception('Používateľ nie je prihlásený');
            }

            $currentToken = $request->user()->currentAccessToken();

            if (!$currentToken) {
                throw new \Exception('Neplatný prístupový token');
            }

            // Vymazať starý token
            $currentToken->delete();

            // Vytvoriť nový token
            $newToken = $user->createToken('auth-token')->plainTextToken;

            if (!$newToken) {
                throw new \Exception('Nepodarilo sa vytvoriť nový token');
            }

            Log::info('Token úspešne obnovený', [
                'user_id' => $user->id,
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Token úspešne obnovený',
                'token' => $newToken,
                'user' => $user->load('roles')
            ]);

        } catch (\Exception $e) {
            Log::error('Chyba pri obnovovaní tokenu', [
                'error' => $e->getMessage(),
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Nepodarilo sa obnoviť token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ], [
                'username.required' => 'Meno používateľa je povinné',
                'username.unique' => 'Toto meno používateľa už existuje',
                'email.required' => 'Email je povinný',
                'email.email' => 'Email musí mať platný formát',
                'email.unique' => 'Tento email už existuje',
                'password.required' => 'Heslo je povinné',
                'password.min' => 'Heslo musí mať najmenej 8 znakov',
                'password.confirmed' => 'Potvrdenie hesla sa nezhoduje',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Neplatné údaje',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            if (!$user) {
                throw new \Exception('Nepodarilo sa vytvoriť používateľa');
            }

            $token = $user->createToken('auth-token')->plainTextToken;

            if (!$token) {
                throw new \Exception('Nepodarilo sa vytvoriť prístupový token');
            }

            Log::info('Úspešná registrácia', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Registrácia úspešná',
                'user' => $user,
                'token' => $token,
                'redirect_url' => $this->getRedirectUrl($user)
            ], 201);

        } catch (\Exception $e) {
            Log::error('Chyba pri registrácii', [
                'email' => $request->email ?? 'neznámy',
                'error' => $e->getMessage(),
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Nepodarilo sa zaregistrovať',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
