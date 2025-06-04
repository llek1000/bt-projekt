<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ConferenceYear;
use App\Models\EditorAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

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
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('author_name', 'like', "%{$searchTerm}%")
                  ->orWhere('content', 'like', "%{$searchTerm}%");
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
            'title.max' => 'Názov článku nemôže presiahnuť 255 znakov',
            'conference_year_id.required' => 'Ročník konferencie je povinný',
            'conference_year_id.exists' => 'Vybraný ročník konferencie neexistuje',
            'author_name.required' => 'Meno autora je povinné',
            'author_name.max' => 'Meno autora nemôže presiahnuť 255 znakov'
        ]);

        // Check if user has permission for this conference year (admin bypass)
        $user = Auth::user();
        if (!$this->hasRole($user, 'admin')) {
            $hasAssignment = EditorAssignment::where('user_id', $user->id)
                ->where('conference_year_id', $validated['conference_year_id'])
                ->exists();

            if (!$hasAssignment) {
                return response()->json([
                    'message' => 'Nemáte oprávnenie vytvárať články pre tento ročník konferencie'
                ], 403);
            }
        }

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
            'title' => 'sometimes|required|string|max:255',
            'content' => 'nullable|string',
            'conference_year_id' => 'sometimes|required|exists:conference_years,id',
            'author_name' => 'sometimes|required|string|max:255'
        ], [
            'title.required' => 'Názov článku je povinný',
            'title.max' => 'Názov článku nemôže presiahnuť 255 znakov',
            'conference_year_id.required' => 'Ročník konferencie je povinný',
            'conference_year_id.exists' => 'Vybraný ročník konferencie neexistuje',
            'author_name.required' => 'Meno autora je povinné',
            'author_name.max' => 'Meno autora nemôže presiahnuť 255 znakov'
        ]);

        // Check if user has permission for this conference year (admin bypass)
        $user = Auth::user();
        if (!$this->hasRole($user, 'admin')) {
            $conferenceYearId = $validated['conference_year_id'] ?? $article->conference_year_id;
            $hasAssignment = EditorAssignment::where('user_id', $user->id)
                ->where('conference_year_id', $conferenceYearId)
                ->exists();

            if (!$hasAssignment) {
                return response()->json([
                    'message' => 'Nemáte oprávnenie upravovať články pre tento ročník konferencie'
                ], 403);
            }
        }

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

        // Check if user has permission for this conference year (admin bypass)
        $user = Auth::user();
        if (!$this->hasRole($user, 'admin')) {
            $hasAssignment = EditorAssignment::where('user_id', $user->id)
                ->where('conference_year_id', $article->conference_year_id)
                ->exists();

            if (!$hasAssignment) {
                return response()->json([
                    'message' => 'Nemáte oprávnenie vymazávať články pre tento ročník konferencie'
                ], 403);
            }
        }

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
            ->where(function($q) use ($query) {
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
            'articles_by_year' => Article::join('conference_years', 'articles.conference_year_id', '=', 'conference_years.id')
                ->selectRaw('conference_years.year, conference_years.semester, COUNT(*) as count')
                ->groupBy('conference_years.year', 'conference_years.semester')
                ->orderBy('conference_years.year', 'desc')
                ->get(),
            'articles_by_author' => Article::selectRaw('author_name, COUNT(*) as count')
                ->groupBy('author_name')
                ->orderBy('count', 'desc')
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
            'article_ids' => 'required|array',
            'article_ids.*' => 'exists:articles,id'
        ]);

        $user = Auth::user();
        $articleIds = $validated['article_ids'];

        // Check permissions for all articles (admin bypass)
        if (!$this->hasRole($user, 'admin')) {
            $articles = Article::whereIn('id', $articleIds)->get();
            $userAssignments = EditorAssignment::where('user_id', $user->id)
                ->pluck('conference_year_id')
                ->toArray();

            foreach ($articles as $article) {
                if (!in_array($article->conference_year_id, $userAssignments)) {
                    return response()->json([
                        'message' => 'Nemáte oprávnenie vymazávať niektoré z vybraných článkov'
                    ], 403);
                }
            }
        }

        $deletedCount = Article::whereIn('id', $articleIds)->delete();

        return response()->json([
            'message' => "Úspešne vymazaných {$deletedCount} článkov",
            'deleted_count' => $deletedCount
        ]);
    }

    /**
     * Export articles to JSON
     */
    public function export(Request $request): JsonResponse
    {
        $articles = Article::with('conferenceYear')->get();

        return response()->json([
            'data' => $articles,
            'exported_at' => now()->toISOString(),
            'total_count' => $articles->count(),
            'message' => 'Články boli úspešne exportované'
        ]);
    }
}
