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

// Guest only
Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login');
});

// Public
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
    Route::post('/search', [ArticleController::class, 'search']);
    Route::get('/export/json', [ArticleController::class, 'export']);
});

// Public file
Route::get('/files/{file}/download', [FileController::class, 'download'])->name('files.download');



// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // User routes
    Route::get('/user', function (Request $request) {
        return response()->json([
            'user' => $request->user()->load('roles')
        ]);
    });
    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
    // File upload for TinyMCE
    Route::post('/upload-file', [FileController::class, 'uploadForEditor'])->name('files.upload');
    Route::get('/files/my-files', [FileController::class, 'listForEditor']);


    Route::prefix('editor')->middleware('checkrole:admin,editor')->group(function () {
        // Article management for editors
        Route::prefix('articles')->group(function () {
            Route::post('/', [ArticleController::class, 'store']);
            Route::put('/{id}', [ArticleController::class, 'update']);
            Route::delete('/{id}', [ArticleController::class, 'destroy']);
            Route::post('/bulk-delete', [ArticleController::class, 'bulkDelete']);
        });


        Route::delete('/files/{file}', [FileController::class, 'destroy']);
        Route::get('my-assignments', [EditorAssignmentController::class, 'myAssignments']);
    });

    // Admin routes
    Route::middleware(['auth:sanctum', 'checkrole:admin'])->prefix('admin')->group(function () {

        Route::apiResource('conference-years', ConferenceYearController::class);
        Route::get('users', [AdminController::class, 'getUsers']);
        Route::post('users', [AdminController::class, 'createUser']);
        Route::put('users/{id}', [AdminController::class, 'updateUser']);
        Route::delete('users/{id}', [AdminController::class, 'deleteUser']);
        Route::get('roles', [AdminController::class, 'getRoles']);
        Route::post('users/{userId}/assign-role', [AdminController::class, 'assignUserRole']);

        // Editor Assignment Management
        Route::prefix('years/{year}/editors')->group(function () {
            Route::get('/', [EditorAssignmentController::class, 'index']);
            Route::post('/', [EditorAssignmentController::class, 'store']);
            Route::delete('/{assignment}', [EditorAssignmentController::class, 'destroy']);
            Route::get('/available', [EditorAssignmentController::class, 'availableUsers']);
        });

        // Editor related routes
        Route::get('editors', [AdminController::class, 'getEditors']);
        Route::get('years-with-editors', [AdminController::class, 'getYearsWithEditors']); // Nová route
        Route::get('assignments', [EditorAssignmentController::class, 'all']);
        Route::get('assignments/user/{userId}', [EditorAssignmentController::class, 'byUser']);

        // System info
        Route::get('system/info', [AdminController::class, 'getSystemInfo']);

        // File Management (Admin can see all files)
        Route::get('files', [AdminController::class, 'getAllFiles']);
    });
});
