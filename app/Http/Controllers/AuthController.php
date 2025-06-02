<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
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
        $token = $user->createToken('auth-token')->plainTextToken;


        $redirectUrl = $this->getRedirectUrl($user);

        return response()->json([
            'success' => true,
            'message' => 'Prihlásenie úspešné',
            'user' => $user,
            'token' => $token,
            'redirect_url' => $redirectUrl
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Odhlásenie úspešné'
        ]);
    }


    private function getRedirectUrl($user)
    {

        if ($user->roles()->where('name', 'Admin')->exists()) {
            return '/admin/dashboard';
        }


        if ($user->roles()->where('name', 'Editor')->exists()) {
            return '/editor/dashboard';
        }


        return '/dashboard';
    }


    public function me(Request $request)
    {
        $user = $request->user()->load('roles');
        $redirectUrl = $this->getRedirectUrl($user);

        return response()->json([
            'success' => true,
            'user' => $user,
            'redirect_url' => $redirectUrl
        ]);
    }


    public function refresh(Request $request)
    {
        $user = $request->user();


        $request->user()->currentAccessToken()->delete();


        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Token úspešne obnovený',
            'token' => $token,
            'user' => $user->load('roles')
        ]);
    }
}
