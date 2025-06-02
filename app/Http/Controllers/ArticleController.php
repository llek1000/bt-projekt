<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ConferenceYear;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ArticleController extends Controller
{
    /**
     * Display a listing of articles
     */
    public function index(Request $request): JsonResponse
    {
        $query = Article::with('conferenceYear');

        // Filter by conference year if provided
        if ($request->has('conference_year_id')) {
            $query->where('conference_year_id', $request->conference_year_id);
        }

        // Search by title or author if provided
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author_name', 'like', "%{$search}%");
            });
        }

        $articles = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $articles,
            'message' => 'Články boli úspešne načítané'
        ]);
    }

    /**
     * Store a newly created article
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'conference_year_id' => 'required|exists:conference_years,id',
            'author_name' => 'required|string|max:255'
        ], [
            'title.required' => 'Názov článku je povinný',
            'title.max' => 'Názov nemôže presiahnuť 255 znakov',
            'conference_year_id.required' => 'Ročník konferencie je povinný',
            'conference_year_id.exists' => 'Vybraný ročník konferencie neexistuje',
            'author_name.required' => 'Meno autora je povinné',
            'author_name.max' => 'Meno autora nemôže presiahnuť 255 znakov'
        ]);

        $article = Article::create($validated);
        $article->load('conferenceYear');

        return response()->json([
            'data' => $article,
            'message' => 'Článok bol úspešne vytvorený'
        ], 201);
    }

    /**
     * Display the specified article
     */
    public function show($id): JsonResponse
    {
        $article = Article::with('conferenceYear')->findOrFail($id);

        return response()->json([
            'data' => $article,
            'message' => 'Článok bol úspešne načítaný'
        ]);
    }

    /**
     * Update the specified article
     */
    public function update(Request $request, $id): JsonResponse
    {
        $article = Article::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'conference_year_id' => 'required|exists:conference_years,id',
            'author_name' => 'required|string|max:255'
        ], [
            'title.required' => 'Názov článku je povinný',
            'title.max' => 'Názov nemôže presiahnuť 255 znakov',
            'conference_year_id.required' => 'Ročník konferencie je povinný',
            'conference_year_id.exists' => 'Vybraný ročník konferencie neexistuje',
            'author_name.required' => 'Meno autora je povinné',
            'author_name.max' => 'Meno autora nemôže presiahnuť 255 znakov'
        ]);

        $article->update($validated);
        $article->load('conferenceYear');

        return response()->json([
            'data' => $article,
            'message' => 'Článok bol úspešne aktualizovaný'
        ]);
    }

    /**
     * Remove the specified article
     */
    public function destroy($id): JsonResponse
    {
        $article = Article::findOrFail($id);
        $article->delete();

        return response()->json([
            'message' => 'Článok bol úspešne vymazaný'
        ], 204);
    }

    /**
     * Get articles by conference year
     */
    public function getByConferenceYear($conferenceYearId): JsonResponse
    {
        $conferenceYear = ConferenceYear::findOrFail($conferenceYearId);
        $articles = $conferenceYear->articles()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $articles,
            'conference_year' => $conferenceYear,
            'message' => 'Články pre ročník konferencie boli úspešne načítané'
        ]);
    }

    /**
     * Get articles by author
     */
    public function getByAuthor($authorName): JsonResponse
    {
        $articles = Article::with('conferenceYear')
                          ->where('author_name', 'like', "%{$authorName}%")
                          ->orderBy('created_at', 'desc')
                          ->get();

        return response()->json([
            'data' => $articles,
            'author' => $authorName,
            'message' => 'Články od autora boli úspešne načítané'
        ]);
    }

    /**
     * Search articles
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'query' => 'required|string|min:2'
        ], [
            'query.required' => 'Hľadací dotaz je povinný',
            'query.min' => 'Hľadací dotaz musí mať aspoň 2 znaky'
        ]);

        $query = $validated['query'];

        $articles = Article::with('conferenceYear')
                          ->where(function ($q) use ($query) {
                              $q->where('title', 'like', "%{$query}%")
                                ->orWhere('author_name', 'like', "%{$query}%")
                                ->orWhere('content', 'like', "%{$query}%");
                          })
                          ->orderBy('created_at', 'desc')
                          ->get();

        return response()->json([
            'data' => $articles,
            'query' => $query,
            'count' => $articles->count(),
            'message' => 'Vyhľadávanie bolo úspešne dokončené'
        ]);
    }

    /**
     * Get article statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_articles' => Article::count(),
            'articles_by_conference_year' => Article::selectRaw('conference_year_id, COUNT(*) as count')
                                                   ->with('conferenceYear:id,year,semester')
                                                   ->groupBy('conference_year_id')
                                                   ->get(),
            'top_authors' => Article::selectRaw('author_name, COUNT(*) as article_count')
                                   ->groupBy('author_name')
                                   ->orderBy('article_count', 'desc')
                                   ->limit(10)
                                   ->get(),
            'recent_articles' => Article::with('conferenceYear')
                                       ->orderBy('created_at', 'desc')
                                       ->limit(5)
                                       ->get()
        ];

        return response()->json([
            'data' => $stats,
            'message' => 'Štatistiky článkov boli úspešne načítané'
        ]);
    }

    /**
     * Bulk delete articles
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'article_ids' => 'required|array|min:1',
            'article_ids.*' => 'exists:articles,id'
        ], [
            'article_ids.required' => 'Zoznam článkov na vymazanie je povinný',
            'article_ids.array' => 'Zoznam článkov musí byť pole',
            'article_ids.min' => 'Musíte vybrať aspoň jeden článok na vymazanie',
            'article_ids.*.exists' => 'Niektorý z vybraných článkov neexistuje'
        ]);

        $deletedCount = Article::whereIn('id', $validated['article_ids'])->delete();

        return response()->json([
            'message' => "{$deletedCount} článkov bolo úspešne vymazaných",
            'deleted_count' => $deletedCount
        ]);
    }

    /**
     * Export articles as JSON
     */
    public function export(Request $request): JsonResponse
    {
        $query = Article::with('conferenceYear');

        if ($request->has('conference_year_id')) {
            $query->where('conference_year_id', $request->conference_year_id);
        }

        $articles = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $articles,
            'export_date' => now()->toISOString(),
            'total_count' => $articles->count(),
            'message' => 'Články boli úspešne exportované'
        ]);
    }
}
