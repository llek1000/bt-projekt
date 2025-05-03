<?php
use App\Models\ConferenceYear;
use App\Models\Subpage;
use App\Models\User;

Route::get('/conference-years', function () {
    return ConferenceYear::all();
});

Route::get('/conference-years/{id}/subpages', function ($id) {
    return ConferenceYear::findOrFail($id)->subpages;
});

Route::get('/conference-years/{id}/editors', function ($id) {
    return ConferenceYear::findOrFail($id)->editors;
});