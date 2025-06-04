<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ConferenceYearController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\EditorAssignmentController;
use App\Http\Controllers\SubpageController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\FileController;

// Auth routes (Guest only)
Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login');
    Route::post('/register', [AuthController::class, 'register']);
});

// Public routes (Read-only access for everyone)
Route::prefix('conference-years')->group(function () {
    Route::get('/', [ConferenceYearController::class, 'index']);
    Route::get('/{id}', [ConferenceYearController::class, 'show']);
    Route::get('/active/current', [ConferenceYearController::class, 'active']);
    Route::get('/year/{year}', [ConferenceYearController::class, 'getByYear']);
    Route::get('/years/available', [ConferenceYearController::class, 'availableYears']);
    Route::get('/{id}/statistics', [ConferenceYearController::class, 'statistics']);
});

Route::prefix('articles')->group(function () {
    Route::get('/', [ArticleController::class, 'index']);
    Route::get('/{id}', [ArticleController::class, 'show']);
    Route::get('/conference-year/{conferenceYearId}', [ArticleController::class, 'getByConferenceYear']);
    Route::get('/author/{authorName}', [ArticleController::class, 'getByAuthor']);
    Route::post('/search', [ArticleController::class, 'search']);
    Route::get('/stats/overview', [ArticleController::class, 'statistics']);
    Route::get('/export/json', [ArticleController::class, 'export']);
});

// Public file download
Route::get('/files/{file}/download', [FileController::class, 'download'])->name('files.download');

// Logout route - can be called even with invalid token
Route::post('/logout', [AuthController::class, 'logout']);

// Protected routes (Authentication required)
Route::middleware('auth:sanctum')->group(function () {
    // User routes
    Route::get('/user', function (Request $request) {
        return response()->json([
            'user' => $request->user()->load('roles')
        ]);
    });

    // File upload for TinyMCE - available for authenticated users
    Route::post('/upload-file', [FileController::class, 'uploadForEditor'])->name('files.upload');
    Route::get('/files/my-files', [FileController::class, 'listForEditor']);

    // Legacy image upload endpoint
    Route::post('/upload-image', [UploadController::class, 'upload']);

    // Editor routes (Editor and Admin access)
    Route::prefix('editor')->middleware('checkrole:admin,editor')->group(function () {
        // Article management for editors
        Route::prefix('articles')->group(function () {
            Route::post('/', [ArticleController::class, 'store']);
            Route::put('/{id}', [ArticleController::class, 'update']);
            Route::delete('/{id}', [ArticleController::class, 'destroy']);
            Route::post('/bulk-delete', [ArticleController::class, 'bulkDelete']);
        });

        // File management
        Route::delete('/files/{file}', [FileController::class, 'destroy']);

        // Editor assignments - get current user's assignments
        Route::get('my-assignments', [EditorAssignmentController::class, 'myAssignments']);
    });

    // Admin routes (Admin only)
    Route::middleware(['checkrole:admin'])->prefix('admin')->group(function () {
        // Conference Years Management
        Route::apiResource('conference-years', ConferenceYearController::class);

        // User Management
        Route::get('users', [AdminController::class, 'getUsers']);
        Route::post('users', [AdminController::class, 'createUser']);
        Route::put('users/{id}', [AdminController::class, 'updateUser']);
        Route::delete('users/{id}', [AdminController::class, 'deleteUser']);
        Route::get('roles', [AdminController::class, 'getRoles']);
        Route::post('users/{userId}/roles', [AdminController::class, 'assignUserRole']);

        // Editor assignments
        Route::get('years/{year}/editors', [EditorAssignmentController::class, 'index']);
        Route::post('years/{year}/editors', [EditorAssignmentController::class, 'store']);
        Route::delete('years/{year}/editors/{assignment}', [EditorAssignmentController::class, 'destroy']);
        Route::get('assignments', [EditorAssignmentController::class, 'getAllAssignments']);

        // File management for admin
        Route::get('files', [FileController::class, 'getAllFiles']);
        Route::delete('files/{file}', [FileController::class, 'destroy']);
        Route::post('files/bulk-delete', [FileController::class, 'bulkDeleteFiles']);
        Route::get('files/statistics', [FileController::class, 'getFileStatistics']);

        Route::get('system/info', [AdminController::class, 'getSystemInfo']);
    });
});
