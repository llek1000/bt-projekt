<?php

use App\Http\Controllers\Api\ConferenceYearController;
use App\Http\Controllers\Api\SubpageController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('conference-years', ConferenceYearController::class);
    Route::apiResource('subpages', SubpageController::class);
    Route::post('upload-image', [SubpageController::class, 'uploadImage']);
    Route::post('upload-file', [SubpageController::class, 'uploadFile']);
});

Route::get('/test', function () {
    return ['message' => 'API is working'];
});