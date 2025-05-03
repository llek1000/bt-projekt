<?php

use Illuminate\Support\Facades\Route;
use App\Models\ConferenceYear; 

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-conference-years', function () {
    return ConferenceYear::all();
});
