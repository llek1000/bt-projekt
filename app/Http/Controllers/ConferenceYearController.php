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
            'message' => 'Conference years retrieved successfully'
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
            'year.required' => 'Year is required',
            'year.size' => 'Year must be exactly 4 digits',
            'year.regex' => 'Year must be a valid 4-digit number',
            'semester.required' => 'Semester is required',
            'semester.in' => 'Semester must be either Winter or Summer'
        ]);

        // Check for duplicate year/semester combination
        $exists = ConferenceYear::where('year', $validated['year'])
                                ->where('semester', $validated['semester'])
                                ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Conference year with this year and semester already exists',
                'errors' => [
                    'combination' => ['This year/semester combination already exists']
                ]
            ], 422);
        }

        // If setting this as active, deactivate others
        if (isset($validated['is_active']) && $validated['is_active']) {
            ConferenceYear::where('is_active', true)->update(['is_active' => false]);
        }

        $conferenceYear = ConferenceYear::create($validated);

        return response()->json([
            'data' => $conferenceYear,
            'message' => 'Conference year created successfully'
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
            'message' => 'Conference year retrieved successfully'
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
            'year.required' => 'Year is required',
            'year.size' => 'Year must be exactly 4 digits',
            'year.regex' => 'Year must be a valid 4-digit number',
            'year.unique' => 'This year/semester combination already exists',
            'semester.required' => 'Semester is required',
            'semester.in' => 'Semester must be either Winter or Summer'
        ]);

        // If setting this as active, deactivate others
        if (isset($validated['is_active']) && $validated['is_active']) {
            ConferenceYear::where('is_active', true)
                         ->where('id', '!=', $id)
                         ->update(['is_active' => false]);
        }

        $conferenceYear->update($validated);

        return response()->json([
            'data' => $conferenceYear->fresh(),
            'message' => 'Conference year updated successfully'
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
                'message' => 'Cannot delete conference year with existing articles',
                'errors' => [
                    'articles' => ['This conference year has associated articles and cannot be deleted']
                ]
            ], 422);
        }

        $conferenceYear->delete();

        return response()->json([
            'message' => 'Conference year deleted successfully'
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
                'message' => 'No active conference year found'
            ]);
        }

        return response()->json([
            'data' => $activeConferenceYear,
            'message' => 'Active conference year retrieved successfully'
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
            'message' => 'Conference year set as active successfully'
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
            'message' => "Conference years for {$year} retrieved successfully"
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
            'message' => 'Available years retrieved successfully'
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
            // Remove these status-based queries since status field doesn't exist
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
            'message' => 'Conference year statistics retrieved successfully'
        ]);
    }
}
