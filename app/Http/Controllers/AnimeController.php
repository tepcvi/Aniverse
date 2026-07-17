<?php

namespace App\Http\Controllers;

use App\Services\AniListService;
use Illuminate\Http\Request;

class AnimeController extends Controller
{
    public function __construct(
        private readonly AniListService $anilist,
        private readonly \App\Services\AnikotoService $anikoto
    ) {}

    public function show(string|int $id)
    {
        // 1. Check if ID is a known AniList ID in our mappings database to prevent collision
        $resolvedId = $this->anikoto->resolveAnikotoId($id);

        if ($resolvedId) {
            $response = $this->anikoto->series($resolvedId);
        } else {
            // Otherwise, try direct fetch assuming it's an Anikoto ID
            $response = $this->anikoto->series($id);

            // Fallback for newly added AniList IDs (freshness guard)
            if (!$response || !isset($response['data']['anime'])) {
                $response = $this->resolveAnikotoSeriesByAniListId($id);
            }
        }

        if (!$response || !isset($response['data']['anime'])) {
            abort(404, 'Anime details not found on the Anikoto API.');
        }

        $anime = $response['data']['anime'];
        $episodes = $response['data']['episodes'] ?? [];

        return view('anikoto.show', [
            'anime' => $anime,
            'episodes' => $episodes,
            'id' => $anime['id'] ?? $id, // Use resolved Anikoto ID for watch links
            'metaTitle' => ($anime['title'] ?? 'Anime Details') . ' — AniVerse',
            'metaDescription' => \Illuminate\Support\Str::limit($anime['description'] ?? '', 160),
            'metaImage' => $anime['poster'] ?? '',
        ]);
    }

    /**
     * Helper to search and map an AniList ID to its corresponding Anikoto series details.
     */
    private function resolveAnikotoSeriesByAniListId(string|int $aniListId): ?array
    {
        try {
            $aniListData = $this->anilist->getAnimeDetails((int)$aniListId);
            $media = $aniListData['Media'] ?? null;
            if (!$media) {
                return null;
            }

            $malId = $media['idMal'] ?? null;
            $anikotoId = $this->anikoto->resolveAnikotoId($aniListId, $malId);

            if ($anikotoId) {
                return $this->anikoto->series($anikotoId);
            }

            // Title-based fallback
            $titles = [
                $media['title']['english'] ?? '',
                $media['title']['romaji'] ?? '',
                $media['title']['native'] ?? '',
            ];

            foreach (array_filter($titles) as $title) {
                $results = $this->anikoto->search($title);
                foreach ($results as $result) {
                    if ((!empty($result['ani_id']) && (string)$result['ani_id'] === (string)$aniListId) ||
                        (!empty($result['mal_id']) && !empty($media['idMal']) && (string)$result['mal_id'] === (string)$media['idMal']) ||
                        strtolower($result['title'] ?? '') === strtolower($title)) {
                        
                        return $this->anikoto->series($result['id']);
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Failed resolving AniList ID in AnimeController: ' . $e->getMessage());
        }

        return null;
    }

    public function trending(Request $request)
    {
        $page = (int) $request->get('page', 1);
        $data = $this->anilist->getTrending($page, 24);

        return view('anime.trending', [
            'anime' => $data['Page']['media'] ?? [],
            'pageInfo' => $data['Page']['pageInfo'] ?? [],
            'currentPage' => $page,
        ]);
    }

    public function popular(Request $request)
    {
        $page = (int) $request->get('page', 1);
        $data = $this->anilist->getPopular($page, 24);

        return view('anime.popular', [
            'anime' => $data['Page']['media'] ?? [],
            'pageInfo' => $data['Page']['pageInfo'] ?? [],
            'currentPage' => $page,
        ]);
    }

    public function topRated(Request $request)
    {
        $page = (int) $request->get('page', 1);
        $data = $this->anilist->getTopRated($page, 24);

        return view('anime.top-rated', [
            'anime' => $data['Page']['media'] ?? [],
            'pageInfo' => $data['Page']['pageInfo'] ?? [],
            'currentPage' => $page,
        ]);
    }

    public function seasonal(Request $request)
    {
        $season = $request->get('season', $this->anilist->getCurrentSeason());
        $year = (int) $request->get('year', date('Y'));
        $page = (int) $request->get('page', 1);

        $data = $this->anilist->getSeasonal($season, $year, $page, 24);

        return view('anime.seasonal', [
            'anime' => $data['Page']['media'] ?? [],
            'pageInfo' => $data['Page']['pageInfo'] ?? [],
            'currentPage' => $page,
            'currentSeason' => strtoupper($season),
            'currentYear' => $year,
            'seasons' => $this->anilist->getSeasons(),
        ]);
    }

    public function latest(Request $request)
    {
        $page = (int) $request->get('page', 1);
        $data = $this->anilist->getRecentlyReleased($page, 24);

        return view('anime.latest', [
            'anime' => $data['Page']['media'] ?? [],
            'pageInfo' => $data['Page']['pageInfo'] ?? [],
            'currentPage' => $page,
        ]);
    }
}
