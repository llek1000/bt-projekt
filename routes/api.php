<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EventController;



// Auth routes
Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:50,1')
        ->name('login');
    Route::post('/register', [AuthController::class, 'register']);
});

// Protected routes (authentication required)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return response()->json([
            'user' => $request->user()
        ]);
    });

    // Protected event management routes


    // Admin routes
    Route::prefix('admin')->middleware('checkrole:admin')->group(function () {
        // User Management
        Route::get('/users', [AdminController::class, 'getUsers']);
        Route::post('/users', [AdminController::class, 'createUser']);
        Route::put('/users/{id}', [AdminController::class, 'updateUser']);
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);

        // Role Management
        Route::get('/roles', [AdminController::class, 'getRoles']);
        Route::post('/users/{userId}/roles', [AdminController::class, 'assignUserRole']);
    });
});
