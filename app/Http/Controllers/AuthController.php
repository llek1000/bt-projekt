<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        return $this->executeWithTransaction(function() use ($request) {
            $errors = $this->validateRequest($request, [
                'email' => 'required|email|exists:users,email',
                'password' => 'required',
            ], [
                'email.required' => 'Email je povinný',
                'email.email' => 'Email musí mať platný formát',
                'email.exists' => 'Používateľ s týmto emailom neexistuje',
                'password.required' => 'Heslo je povinné',
            ]);

            if ($errors) return $this->error('Neplatné údaje', $errors, 422);

            if (!Auth::attempt($request->only('email', 'password'))) {
                return $this->error('Nesprávny email alebo heslo', null, 401);
            }

            $user = User::with('roles')->where('email', $request->email)->first();
            $token = $user->createToken('auth-token')->plainTextToken;

            Log::info('Úspešné prihlásenie', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip()
            ]);

            return $this->success([
                'user' => $user,
                'token' => $token,
                'redirect_url' => $this->getRedirectUrl($user)
            ], 'Prihlásenie úspešné');
        }, 'Chyba pri prihlasovaní');
    }

    public function logout(Request $request)
    {
        try {
            // Check if user is authenticated
            if ($request->user()) {
                // If authenticated, delete the current token
                $request->user()->currentAccessToken()->delete();

                Log::info('Úspešné odhlásenie', [
                    'user_id' => $request->user()->id,
                    'ip' => $request->ip()
                ]);
            }

            // Always return success - even for unauthenticated requests
            return $this->success([], 'Odhlásenie úspešné');
        } catch (\Exception $e) {
            // Log the error but still return success
            Log::warning('Chyba pri odhlasovaní', [
                'error' => $e->getMessage(),
                'ip' => $request->ip()
            ]);
            
            // Still return success to allow client-side cleanup
            return $this->success([], 'Odhlásenie úspešné');
        }
    }

    public function me(Request $request)
    {
        try {
            $user = $request->user()->load('roles');

            return $this->success([
                'user' => $user,
                'redirect_url' => $this->getRedirectUrl($user)
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Chyba pri získavaní údajov používateľa', 'Nepodarilo sa načítať údaje používateľa');
        }
    }

    public function register(Request $request)
    {
        return $this->executeWithTransaction(function() use ($request) {
            $errors = $this->validateRequest($request, [
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

            if ($errors) return $this->error('Neplatné údaje', $errors, 422);

            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken('auth-token')->plainTextToken;

            Log::info('Úspešná registrácia', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip()
            ]);

            return $this->success([
                'user' => $user,
                'token' => $token,
                'redirect_url' => $this->getRedirectUrl($user)
            ], 'Registrácia úspešná', 201);
        }, 'Chyba pri registrácii');
    }

    private function getRedirectUrl($user)
    {
        if ($this->hasRole($user, 'admin')) return '/admin/dashboard';
        if ($this->hasRole($user, 'editor')) return '/edit/dashboard';
        return '/';
    }
}
