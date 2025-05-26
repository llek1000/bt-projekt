<?php

use App\Http\Controllers\Api\ConferenceYearController;
use App\Http\Controllers\Api\SubpageController;
use App\Http\Controllers\Api\ArticleController;
use Illuminate\Support\Facades\Route;


Route::apiResource('subpages', SubpageController::class);
Route::post('upload-image', [SubpageController::class, 'uploadImage']);
Route::post('upload-file', [SubpageController::class, 'uploadFile']);
Route::post('upload-image-tinymce', [SubpageController::class, 'uploadImageForTinyMCE']);
Route::apiResource('conference-years', ConferenceYearController::class); 
Route::apiResource('articles', ArticleController::class);
Route::get('/test', fn() => ['message'=>'API is working']);