<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-conference-years', function () {
    return App\Models\ConferenceYear::all();
});