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
            if ($request->user()) {
                $request->user()->currentAccessToken()->delete();

                Log::info('Úspešné odhlásenie', [
                    'user_id' => $request->user()->id,
                    'ip' => $request->ip()
                ]);

                return $this->success([], 'Odhlásenie úspešné');
            }

            return $this->success([], 'Už ste odhlásený');

        } catch (\Exception $e) {
            Log::error('Chyba pri odhlasovaní', [
                'error' => $e->getMessage(),
                'ip' => $request->ip()
            ]);

            return $this->error('Chyba pri odhlasovaní', null, 500);
        }
    }

    private function getRedirectUrl($user)
    {
        if ($this->hasRole($user, 'admin')) return '/admin/dashboard';
        if ($this->hasRole($user, 'editor')) return '/edit/dashboard';
        return '/';
    }
}
