<?php

namespace App\Http\Controllers;

use App\Services\AnikotoService;
use App\Services\AniListService;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function __construct(
        private readonly AnikotoService $anikoto,
        private readonly AniListService $anilist
    ) {}

    /**
     * Watch Page (Full metadata, recommendations, server options, episode navigation)
     * GET /watch/{slug}/{episode}
     */
    public function watch(string $slug, string $episode)
    {
        $watchData = $this->anikoto->getWatchData($slug, $episode);
        $info = $watchData['info'];

        if (!$info) {
            abort(404, 'Anime details not found');
        }

        $episodes = $watchData['episodes'];
        $player = $watchData['player'];

        // Get related and recommended anime from AniList (matching on title)
        $related = [];
        $recommended = [];
        try {
            $searchTitle = $info['title'] ?? '';
            if (!empty($searchTitle)) {
                $searchResult = $this->anilist->search([
                    'query' => $searchTitle,
                    'page' => 1,
                    'perPage' => 1
                ]);
                $media = $searchResult['Page']['media'][0] ?? null;
                if ($media && !empty($media['id'])) {
                    $details = $this->anilist->getAnimeDetails($media['id']);
                    $relations = $details['Media']['relations']['edges'] ?? [];
                    
                    // Extract related items
                    foreach ($relations as $edge) {
                        if (isset($edge['node'])) {
                            $related[] = $edge['node'];
                        }
                    }

                    // Extract recommendations
                    $recs = $details['Media']['recommendations']['edges'] ?? [];
                    foreach ($recs as $edge) {
                        if (isset($edge['node']['mediaRecommendation'])) {
                            $recommended[] = $edge['node']['mediaRecommendation'];
                        }
                    }

                    // Extract characters
                    $characters = $details['Media']['characters']['edges'] ?? [];
                }
            }
        } catch (\Exception $e) {
            // Gracefully ignore and log AniList fallback failures
            \Log::warning('AniList related mapping failed in PlayerController: ' . $e->getMessage());
        }

        // Limit results to 6 items
        $related = array_slice($related, 0, 6);
        $recommended = array_slice($recommended, 0, 6);
        $characters = array_slice($characters ?? [], 0, 12);

        // Find next/previous episode numbers
        $prevEpisode = null;
        $nextEpisode = null;
        $epNumbers = array_map(fn($item) => (string)$item['num'], $episodes);
        $currentIndex = array_search((string)$episode, $epNumbers);

        if ($currentIndex !== false) {
            if ($currentIndex > 0) {
                $nextEpisode = $epNumbers[$currentIndex - 1]; // episode lists are usually descending or ascending, let's look at order
            }
            if ($currentIndex < count($epNumbers) - 1) {
                $prevEpisode = $epNumbers[$currentIndex + 1];
            }
        }

        // If sorting is ascending, flip:
        // Let's verify by comparing numerical values
        if ($currentIndex !== false && count($epNumbers) > 1) {
            $first = (float)$epNumbers[0];
            $last = (float)end($epNumbers);
            if ($first < $last) {
                // Ascending order
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
            'related' => $related,
            'recommended' => $recommended,
            'characters' => $characters,
            'metaTitle' => ($info['title'] ?? 'Watch') . " Episode {$episode} - Stream Online - AniVerse",
        ]);
    }

    /**
     * Isolated Player Page (Renders clean embed frame without navigation/layout overhead)
     * GET /player/{slug}/{episode}
     */
    public function player(Request $request, string $slug, string $episode)
    {
        $servers = $this->anikoto->getStreamingServers($slug, $episode);
        $selectedServerId = $request->get('server', 'anikoto');

        $activeServer = null;
        foreach ($servers as $srv) {
            if ($srv['id'] === $selectedServerId) {
                $activeServer = $srv;
                break;
            }
        }

        // Default fallback if server not found or empty url
        if (!$activeServer || empty($activeServer['url'])) {
            $activeServer = $servers[0] ?? null;
        }

        return view('anikoto.player_frame', [
            'slug' => $slug,
            'episode' => $episode,
            'servers' => $servers,
            'activeServer' => $activeServer,
        ]);
    }
}
