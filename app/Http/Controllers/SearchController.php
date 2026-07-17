<?php

namespace App\Http\Controllers;

use App\Services\AniListService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(
        private readonly AniListService $anilist
    ) {}

    /**
     * Search page with AniList global database queries.
     * GET /search
     */
    public function index(Request $request)
    {
        // Parse input filters
        $filters = [
            'query' => $request->get('query', ''),
            'genre' => $request->get('genre', ''),
            'season' => $request->get('season', ''),
            'year' => $request->get('year', ''),
            'format' => $request->get('format', ''),
            'status' => $request->get('status', ''),
            'sort' => $request->get('sort', 'popularity'),
            'page' => (int) $request->get('page', 1),
            'perPage' => 20,
        ];

        // Determine if any filters are active
        $hasFilters = !empty($filters['query']) || 
                      !empty($filters['genre']) || 
                      !empty($filters['season']) || 
                      !empty($filters['year']) || 
                      !empty($filters['format']) || 
                      !empty($filters['status']);

        // Execute search on AniList API
        $response = $this->anilist->search($filters);
        $anime = $response['Page']['media'] ?? [];
        $pageInfo = $response['Page']['pageInfo'] ?? [];

        // Fetch dropdown options for filter bar
        $genresCollection = $this->anilist->getGenres();
        $genres = $genresCollection['GenreCollection'] ?? [];

        return view('search.index', [
            'anime' => $anime,
            'pageInfo' => $pageInfo,
            'filters' => $filters,
            'hasFilters' => $hasFilters,
            'genres' => $genres,
            'seasons' => $this->anilist->getSeasons(),
            'formats' => $this->anilist->getFormats(),
            'statuses' => $this->anilist->getStatuses(),
            'metaTitle' => !empty($filters['query']) ? "Search: {$filters['query']} — AniVerse" : 'Search Anime — AniVerse',
        ]);
    }

    /**
     * Autocomplete endpoint utilizing AniList global metadata.
     */
    public function autocomplete(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $response = $this->anilist->getAutocomplete($query);
        $results = $response['Page']['media'] ?? [];

        return response()->json($results);
    }
}
