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
        try {
            $conferenceYears = ConferenceYear::orderBy('year', 'desc')
                          ->orderBy('semester', 'asc')
                          ->get();

            return $this->success(['data' => $conferenceYears], 'Ročníky konferencie boli úspešne načítané');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Conference years listing failed', 'Načítanie ročníkov konferencie zlyhalo');
        }
    }

    /**
     * Store a newly created conference year
     */
    public function store(Request $request): JsonResponse
    {
        return $this->executeWithTransaction(function() use ($request) {
            $errors = $this->validateRequest($request, [
                'year' => 'required|string|max:4',
                'semester' => 'required|string|in:Winter,Summer',
                'is_active' => 'boolean'
            ], [
                'year.required' => 'Rok je povinný',
                'year.max' => 'Rok nemôže presiahnuť 4 znaky',
                'semester.required' => 'Semester je povinný',
                'semester.in' => 'Semester musí byť buď Winter alebo Summer'
            ]);

            if ($errors) {
                return $this->error('Neplatné údaje', $errors, 422);
            }

            // Check for duplicate year/semester combination
            $exists = ConferenceYear::where('year', $request->year)
                ->where('semester', $request->semester)
                ->exists();

            if ($exists) {
                return $this->error('Kombinácia roku a semestra už existuje', null, 422);
            }

            $conferenceYear = ConferenceYear::create([
                'year' => $request->year,
                'semester' => $request->semester,
                'is_active' => $request->has('is_active') ? $request->is_active : false
            ]);

            return $this->success(['data' => $conferenceYear], 'Ročník konferencie bol úspešne vytvorený', 201);
        }, 'Vytvorenie ročníka konferencie zlyhalo');
    }

    /**
     * Display the specified conference year
     */
    public function show($id): JsonResponse
    {
        try {
            $conferenceYear = ConferenceYear::findOrFail($id);

            return $this->success(['data' => $conferenceYear], 'Ročník konferencie bol úspešne načítaný');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Conference year show failed', 'Načítanie ročníka konferencie zlyhalo');
        }
    }

    /**
     * Update the specified conference year
     */
    public function update(Request $request, $id): JsonResponse
    {
        return $this->executeWithTransaction(function() use ($request, $id) {
            $conferenceYear = ConferenceYear::findOrFail($id);
            
            $errors = $this->validateRequest($request, [
                'year' => [
                    'sometimes',
                    'required',
                    'string',
                    'size:4',
                    'regex:/^\d{4}$/',
                    Rule::unique('conference_years')->where(function ($query) use ($request, $id) {
                        return $query->where('year', $request->year)
                                   ->where('semester', $request->semester)
                                   ->where('id', '!=', $id);
                    })->ignore($id)
                ],
                'semester' => [
                    'sometimes', 
                    'required', 
                    'string', 
                    'in:Winter,Summer'
                ],
                'is_active' => 'sometimes|boolean'
            ], [
                'year.required' => 'Rok je povinný',
                'year.size' => 'Rok musí mať presne 4 číslice',
                'year.regex' => 'Rok musí byť platné 4-miestne číslo',
                'semester.required' => 'Semester je povinný',
                'semester.in' => 'Semester musí byť buď Winter alebo Summer',
                'semester.unique' => 'Kombinácia roku a semestra už existuje'
            ]);

            if ($errors) {
                return $this->error('Neplatné údaje', $errors, 422);
            }

            // Remove this block to allow multiple active years
            // if ($request->has('is_active') && $request->is_active) {
            //     ConferenceYear::where('id', '!=', $id)->update(['is_active' => false]);
            // }

            $conferenceYear->update($request->only(['year', 'semester', 'is_active']));

            return $this->success(['data' => $conferenceYear->fresh()], 'Ročník konferencie bol úspešne aktualizovaný');
        }, 'Aktualizácia ročníka konferencie zlyhala');
    }

    /**
     * Remove the specified conference year
     */
    public function destroy($id): JsonResponse
    {
        return $this->executeWithTransaction(function() use ($id) {
            $conferenceYear = ConferenceYear::findOrFail($id);

            // Check if conference year has articles
            if ($conferenceYear->articles()->exists()) {
                return $this->error('Nemožno vymazať ročník konferencie s existujúcimi článkami', [
                    'articles' => ['Tento ročník konferencie má priradené články a nemôže byť vymazaný']
                ], 422);
            }

            $conferenceYear->delete();

            return $this->success([], 'Ročník konferencie bol úspešne vymazaný', 204);
        }, 'Vymazanie ročníka konferencie zlyhalo');
    }

    /**
     * Get the active conference year
     */
    public function active(): JsonResponse
    {
        try {
            $activeConferenceYear = ConferenceYear::where('is_active', true)->first();

            if (!$activeConferenceYear) {
                return $this->success(['data' => null], 'Nebol nájdený žiadny aktívny ročník konferencie');
            }

            return $this->success(['data' => $activeConferenceYear], 'Aktívny ročník konferencie bol úspešne načítaný');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Active conference year failed', 'Načítanie aktívneho ročníka konferencie zlyhalo');
        }
    }

    /**
     * Set a conference year as active
     */
    public function setActive($id): JsonResponse
    {
        return $this->executeWithTransaction(function() use ($id) {
            $conferenceYear = ConferenceYear::findOrFail($id);

            // Remove this line to allow multiple active years
            // ConferenceYear::where('is_active', true)->update(['is_active' => false]);

            // Just activate this year without affecting others
            $conferenceYear->update(['is_active' => true]);

            return $this->success(['data' => $conferenceYear->fresh()], 'Ročník konferencie bol úspešne nastavený ako aktívny');
        }, 'Nastavenie aktívneho ročníka konferencie zlyhalo');
    }

    /**
     * Get conference years by year
     */
    public function getByYear($year): JsonResponse
    {
        try {
            $conferenceYears = ConferenceYear::where('year', $year)
                          ->orderBy('semester', 'asc')
                          ->get();

            return $this->success(['data' => $conferenceYears], "Ročníky konferencie pre rok {$year} boli úspešne načítané");
        } catch (\Exception $e) {
            return $this->handleException($e, 'Conference years by year failed', "Načítanie ročníkov konferencie pre rok {$year} zlyhalo");
        }
    }

    /**
     * Get available years
     */
    public function availableYears(): JsonResponse
    {
        try {
            $years = ConferenceYear::select('year')
                ->orderBy('year', 'desc')
                ->distinct()
                ->pluck('year');
                
            return $this->success(['data' => $years], 'Dostupné roky boli úspešne načítané');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Available years failed', 'Načítanie dostupných rokov zlyhalo');
        }
    }

    /**
     * Get conference year statistics
     */
    public function statistics($id): JsonResponse
    {
        try {
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

            return $this->success([
                'data' => [
                    'conference_year' => $conferenceYear,
                    'statistics' => $stats
                ]
            ], 'Štatistiky ročníka konferencie boli úspešne načítané');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Conference year statistics failed', 'Načítanie štatistík ročníka konferencie zlyhalo');
        }
    }
}
