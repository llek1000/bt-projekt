<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ConferenceYearController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\DepartmentController;



// Auth routes
Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:50,1')
        ->name('login');
    Route::post('/register', [AuthController::class, 'register']);
});

// Conference Years routes
Route::prefix('conference-years')->group(function () {
    Route::get('/', [ConferenceYearController::class, 'index']);
    Route::post('/', [ConferenceYearController::class, 'store']);
    Route::get('/{id}', [ConferenceYearController::class, 'show']);
    Route::put('/{id}', [ConferenceYearController::class, 'update']);
    Route::delete('/{id}', [ConferenceYearController::class, 'destroy']);
    Route::get('/active/current', [ConferenceYearController::class, 'active']);
    Route::patch('/{id}/set-active', [ConferenceYearController::class, 'setActive']);
    Route::get('/year/{year}', [ConferenceYearController::class, 'getByYear']);
    Route::get('/years/available', [ConferenceYearController::class, 'availableYears']);
    Route::get('/{id}/statistics', [ConferenceYearController::class, 'statistics']);
});

// Articles routes
Route::prefix('articles')->group(function () {
    Route::get('/', [ArticleController::class, 'index']);
    Route::post('/', [ArticleController::class, 'store']);
    Route::get('/{id}', [ArticleController::class, 'show']);
    Route::put('/{id}', [ArticleController::class, 'update']);
    Route::delete('/{id}', [ArticleController::class, 'destroy']);
    Route::get('/conference-year/{conferenceYearId}', [ArticleController::class, 'getByConferenceYear']);
    Route::get('/author/{authorName}', [ArticleController::class, 'getByAuthor']);
    Route::post('/search', [ArticleController::class, 'search']);
    Route::get('/stats/overview', [ArticleController::class, 'statistics']);
    Route::post('/bulk-delete', [ArticleController::class, 'bulkDelete']);
    Route::get('/export/json', [ArticleController::class, 'export']);
});

// Departments routes
Route::prefix('departments')->group(function () {
    Route::get('/', [DepartmentController::class, 'index']);
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
