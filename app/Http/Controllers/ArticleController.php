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
            'message' => 'Articles retrieved successfully'
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
            'title.required' => 'Article title is required',
            'title.max' => 'Title cannot exceed 255 characters',
            'conference_year_id.required' => 'Conference year is required',
            'conference_year_id.exists' => 'Selected conference year does not exist',
            'author_name.required' => 'Author name is required',
            'author_name.max' => 'Author name cannot exceed 255 characters'
        ]);

        $article = Article::create($validated);
        $article->load('conferenceYear');

        return response()->json([
            'data' => $article,
            'message' => 'Article created successfully'
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
            'message' => 'Article retrieved successfully'
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
        ]);

        $article->update($validated);
        $article->load('conferenceYear');

        return response()->json([
            'data' => $article,
            'message' => 'Article updated successfully'
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
            'message' => 'Article deleted successfully'
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
            'message' => 'Articles for conference year retrieved successfully'
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
            'message' => 'Articles by author retrieved successfully'
        ]);
    }

    /**
     * Search articles
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'query' => 'required|string|min:2'
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
            'message' => 'Search completed successfully'
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
            'message' => 'Article statistics retrieved successfully'
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
        ]);

        $deletedCount = Article::whereIn('id', $validated['article_ids'])->delete();

        return response()->json([
            'message' => "{$deletedCount} articles deleted successfully",
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
            'message' => 'Articles exported successfully'
        ]);
    }
}
