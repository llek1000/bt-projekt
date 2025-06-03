<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConferenceYear;
use Illuminate\Http\Request;

class ConferenceYearController extends Controller
{
    public function index()
    {
        return ConferenceYear::all();
    }

    public function show($id)
    {
        return ConferenceYear::findOrFail($id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|integer|unique:conference_years,year',
            'description' => 'nullable|string',
        ]);

        $conferenceYear = ConferenceYear::create($validated);
        return response()->json($conferenceYear, 201);
    }

    public function update(Request $request, $id)
    {
        $conferenceYear = ConferenceYear::findOrFail($id);
        $validated = $request->validate([
            'year' => 'required|integer|unique:conference_years,year,' . $id,
            'description' => 'nullable|string',
        ]);

        $conferenceYear->update($validated);
        return response()->json($conferenceYear);
    }

    public function destroy($id)
    {
        $conferenceYear = ConferenceYear::findOrFail($id);
        $conferenceYear->delete();
        return response()->json(null, 204);
    }

    public function subpages($id)
    {
        return ConferenceYear::findOrFail($id)->subpages;
    }

    public function editors($id)
    {
        return ConferenceYear::findOrFail($id)->editors;
    }
}