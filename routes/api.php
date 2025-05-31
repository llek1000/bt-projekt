<?php

use App\Http\Controllers\Api\ConferenceYearController;
use App\Http\Controllers\Api\SubpageController;
use App\Http\Controllers\Api\ArticleController;
use Illuminate\Support\Facades\Route;


Route::apiResource('subpages', SubpageController::class);
Route::apiResource('conference-years', ConferenceYearController::class); 
Route::apiResource('articles', ArticleController::class);
Route::post('articles/upload-image', [ArticleController::class, 'uploadImageForTinyMCE']);
Route::get('/test', fn() => ['message'=>'API is working']);