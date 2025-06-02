<?php

namespace App\Http\Controllers;

use App\Models\ConferenceYear;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class ConferenceYearController extends Controller
{
    /**
     * Display a listing of conference years
     */
    public function index(): JsonResponse
    {
        $conferenceYears = ConferenceYear::orderBy('year', 'desc')
                                       ->orderBy('semester', 'desc')
                                       ->get();

        return response()->json([
            'data' => $conferenceYears,
            'message' => 'Ročníky konferencie boli úspešne načítané'
        ]);
    }

    /**
     * Store a newly created conference year
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'year' => [
                'required',
                'string',
                'size:4',
                'regex:/^\d{4}$/'
            ],
            'semester' => 'required|in:Winter,Summer',
            'is_active' => 'boolean'
        ], [
            'year.required' => 'Rok je povinný',
            'year.size' => 'Rok musí mať presne 4 číslice',
            'year.regex' => 'Rok musí byť platné 4-miestne číslo',
            'semester.required' => 'Semester je povinný',
            'semester.in' => 'Semester musí byť buď Winter alebo Summer'
        ]);

        // Check for duplicate year/semester combination
        $exists = ConferenceYear::where('year', $validated['year'])
                                ->where('semester', $validated['semester'])
                                ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Ročník konferencie s týmto rokom a semestrom už existuje',
                'errors' => [
                    'combination' => ['Táto kombinácia rok/semester už existuje']
                ]
            ], 422);
        }

        $conferenceYear = ConferenceYear::create($validated);

        return response()->json([
            'data' => $conferenceYear,
            'message' => 'Ročník konferencie bol úspešne vytvorený'
        ], 201);
    }

    /**
     * Display the specified conference year
     */
    public function show($id): JsonResponse
    {
        $conferenceYear = ConferenceYear::findOrFail($id);

        return response()->json([
            'data' => $conferenceYear,
            'message' => 'Ročník konferencie bol úspešne načítaný'
        ]);
    }

    /**
     * Update the specified conference year
     */
    public function update(Request $request, $id): JsonResponse
    {
        $conferenceYear = ConferenceYear::findOrFail($id);

        $validated = $request->validate([
            'year' => [
                'required',
                'string',
                'size:4',
                'regex:/^\d{4}$/',
                Rule::unique('conference_years')->where(function ($query) use ($request) {
                    return $query->where('semester', $request->semester);
                })->ignore($id)
            ],
            'semester' => 'required|in:Winter,Summer',
            'is_active' => 'boolean'
        ], [
            'year.required' => 'Rok je povinný',
            'year.size' => 'Rok musí mať presne 4 číslice',
            'year.regex' => 'Rok musí byť platné 4-miestne číslo',
            'year.unique' => 'Táto kombinácia rok/semester už existuje',
            'semester.required' => 'Semester je povinný',
            'semester.in' => 'Semester musí byť buď Winter alebo Summer'
        ]);

        $conferenceYear->update($validated);

        return response()->json([
            'data' => $conferenceYear->fresh(),
            'message' => 'Ročník konferencie bol úspešne aktualizovaný'
        ]);
    }

    /**
     * Remove the specified conference year
     */
    public function destroy($id): JsonResponse
    {
        $conferenceYear = ConferenceYear::findOrFail($id);

        // Check if conference year has articles
        if ($conferenceYear->articles()->exists()) {
            return response()->json([
                'message' => 'Nemožno vymazať ročník konferencie s existujúcimi článkami',
                'errors' => [
                    'articles' => ['Tento ročník konferencie má priradené články a nemôže byť vymazaný']
                ]
            ], 422);
        }

        $conferenceYear->delete();

        return response()->json([
            'message' => 'Ročník konferencie bol úspešne vymazaný'
        ], 204);
    }

    /**
     * Get the active conference year
     */
    public function active(): JsonResponse
    {
        $activeConferenceYear = ConferenceYear::where('is_active', true)->first();

        if (!$activeConferenceYear) {
            return response()->json([
                'data' => null,
                'message' => 'Nebol nájdený žiadny aktívny ročník konferencie'
            ]);
        }

        return response()->json([
            'data' => $activeConferenceYear,
            'message' => 'Aktívny ročník konferencie bol úspešne načítaný'
        ]);
    }

    /**
     * Set a conference year as active
     */
    public function setActive($id): JsonResponse
    {
        $conferenceYear = ConferenceYear::findOrFail($id);

        // Deactivate all other conference years
        ConferenceYear::where('is_active', true)->update(['is_active' => false]);

        // Activate the selected conference year
        $conferenceYear->update(['is_active' => true]);

        return response()->json([
            'data' => $conferenceYear->fresh(),
            'message' => 'Ročník konferencie bol úspešne nastavený ako aktívny'
        ]);
    }

    /**
     * Get conference years by year
     */
    public function getByYear($year): JsonResponse
    {
        $conferenceYears = ConferenceYear::where('year', $year)
                                       ->orderBy('semester')
                                       ->get();

        return response()->json([
            'data' => $conferenceYears,
            'message' => "Ročníky konferencie pre rok {$year} boli úspešne načítané"
        ]);
    }

    /**
     * Get available years
     */
    public function availableYears(): JsonResponse
    {
        $years = ConferenceYear::select('year')
                              ->distinct()
                              ->orderBy('year', 'desc')
                              ->pluck('year');

        return response()->json([
            'data' => $years,
            'message' => 'Dostupné roky boli úspešne načítané'
        ]);
    }

    /**
     * Get conference year statistics
     */
    public function statistics($id): JsonResponse
    {
        $conferenceYear = ConferenceYear::findOrFail($id);

        $stats = [
            'total_articles' => $conferenceYear->articles()->count(),
            'articles_by_author' => $conferenceYear->articles()
                                                  ->selectRaw('author_name, COUNT(*) as count')
                                                  ->groupBy('author_name')
                                                  ->get(),
            'recent_articles' => $conferenceYear->articles()
                                               ->orderBy('created_at', 'desc')
                                               ->limit(5)
                                               ->get(),
        ];

        return response()->json([
            'data' => [
                'conference_year' => $conferenceYear,
                'statistics' => $stats
            ],
            'message' => 'Štatistiky ročníka konferencie boli úspešne načítané'
        ]);
    }
}
