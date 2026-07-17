<?php

namespace App\Http\Controllers;

use App\Services\AnikotoService;
use Illuminate\Http\Request;

class AnikotoController extends Controller
{
    public function __construct(
        private readonly AnikotoService $anikoto
    ) {}

    /**
     * Search / look up anime by slug.
     * GET /anikoto/search?q=one-piece-odmau
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');

        if (empty($query)) {
            return view('anikoto.search', [
                'result' => null,
                'query' => '',
                'error' => null,
            ]);
        }

        $result = $this->anikoto->searchAnime($query);

        return view('anikoto.search', [
            'result' => $result,
            'query' => $query,
            'error' => $result === null ? 'Anime not found or the Anikoto API is unavailable.' : null,
        ]);
    }

    /**
     * Anime detail page with info and episode list.
     * GET /anikoto/anime/{slug}
     */
    public function show(string $slug)
    {
        $details = $this->anikoto->getFullAnimeDetails($slug);
        $info = $details['info'];

        if (!$info) {
            abort(404, 'Anime not found');
        }

        return view('anikoto.show', [
            'anime' => $info,
            'episodes' => $details['episodes'] ?? [],
            'dataId' => $details['dataId'],
            'id' => $details['dataId'],
            'slug' => $slug,
            'metaTitle' => ($info['title'] ?? 'Unknown') . ' — AniVerse',
            'metaDescription' => \Illuminate\Support\Str::limit($info['synopsis'] ?? '', 160),
            'metaImage' => $info['poster'] ?? '',
        ]);
    }

    /**
     * Episode list (JSON for AJAX or full page).
     * GET /anikoto/episodes/{id}
     */
    public function episodes(Request $request, string $id)
    {
        $episodes = $this->anikoto->getEpisodes($id);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => $episodes !== null,
                'episodes' => $episodes ?? [],
            ]);
        }

        return view('anikoto.episodes', [
            'episodes' => $episodes ?? [],
            'dataId' => $id,
        ]);
    }

    /**
     * Video player page for a specific episode.
     * GET /anikoto/watch/{slug}/{episode}
     */
    public function watch(string $slug, string $episode)
    {
        $watchData = $this->anikoto->getWatchData($slug, $episode);
        $info = $watchData['info'];

        if (!$info) {
            abort(404, 'Anime not found');
        }

        $episodes = $watchData['episodes'];
        $player = $watchData['player'];

        // Find next/previous episode numbers
        $prevEpisode = null;
        $nextEpisode = null;
        $epNumbers = array_map(fn($item) => (string)$item['num'], $episodes);
        $currentIndex = array_search((string)$episode, $epNumbers);

        if ($currentIndex !== false) {
            if ($currentIndex > 0) {
                $nextEpisode = $epNumbers[$currentIndex - 1];
            }
            if ($currentIndex < count($epNumbers) - 1) {
                $prevEpisode = $epNumbers[$currentIndex + 1];
            }
        }

        if ($currentIndex !== false && count($epNumbers) > 1) {
            $first = (float)$epNumbers[0];
            $last = (float)end($epNumbers);
            if ($first < $last) {
                $prevEpisode = ($currentIndex > 0) ? $epNumbers[$currentIndex - 1] : null;
                $nextEpisode = ($currentIndex < count($epNumbers) - 1) ? $epNumbers[$currentIndex + 1] : null;
            }
        }

        return view('anikoto.watch', [
            'info' => $info,
            'episodes' => $episodes,
            'player' => $player,
            'slug' => $slug,
            'episode' => $episode,
            'prevEpisode' => $prevEpisode,
            'nextEpisode' => $nextEpisode,
            'related' => [],
            'recommended' => [],
            'metaTitle' => ($info['title'] ?? 'Unknown') . " Episode {$episode} — AniVerse",
        ]);
    }

    /**
     * Anime schedule page.
     * GET /anikoto/schedule?date=2026-07-17
     */
    public function schedule(Request $request)
    {
        $date = $request->get('date', date('Y-m-d'));
        $schedule = $this->anikoto->getSchedule($date);

        return view('anikoto.schedule', [
            'schedule' => $schedule ?? [],
            'date' => $date,
            'error' => $schedule === null ? 'Unable to fetch schedule. The Anikoto API may be unavailable.' : null,
        ]);
    }

    /**
     * API health check (JSON).
     * GET /anikoto/health
     */
    public function health()
    {
        return response()->json([
            'anikoto_api' => $this->anikoto->isHealthy() ? 'online' : 'offline',
            'timestamp' => now()->toISOString(),
        ]);
    }
}
