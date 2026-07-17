<?php

namespace App\Http\Controllers;

use App\Services\AnikotoService;
use Illuminate\Http\Request;

class WatchController extends Controller
{
    public function __construct(
        private readonly AnikotoService $anikoto
    ) {}

    /**
     * Watch Page (Full details, episode list, responsive player)
     * GET /watch/{id}/{episode}
     */
    public function watch(Request $request, string|int $id, string|int $episode)
    {
        $response = $this->anikoto->series($id);

        if (!$response || !isset($response['data']['anime'])) {
            abort(404, 'Anime series not found on the Anikoto API.');
        }

        $anime = $response['data']['anime'];
        $episodes = $response['data']['episodes'] ?? [];

        // Find current episode object
        $currentEpisodeObj = null;
        foreach ($episodes as $ep) {
            if ((string)($ep['number'] ?? '') === (string)$episode) {
                $currentEpisodeObj = $ep;
                break;
            }
        }

        if (!$currentEpisodeObj) {
            abort(404, "Episode {$episode} not found for this series.");
        }

        // Get sub/dub embed URLs
        $embedSub = $this->anikoto->getEmbedUrl($anime, $currentEpisodeObj, 'sub');
        $embedDub = $this->anikoto->getEmbedUrl($anime, $currentEpisodeObj, 'dub');

        // Check if both sub and dub embed URLs are completely missing
        $embedUrl = $embedSub ?? $embedDub;

        // Navigation calculations (find next / previous episode numbers)
        $prevEpisode = null;
        $nextEpisode = null;
        $epNumbers = array_map(fn($item) => (string)($item['number'] ?? ''), $episodes);
        
        // Remove empty strings
        $epNumbers = array_filter($epNumbers);
        
        // Find position of current episode
        $currentIndex = array_search((string)$episode, $epNumbers);

        if ($currentIndex !== false) {
            // Ascending order check: usually sorted 1, 2, 3...
            // If they are sorted ascending:
            $prevEpisode = ($currentIndex > 0) ? $epNumbers[$currentIndex - 1] : null;
            $nextEpisode = ($currentIndex < count($epNumbers) - 1) ? $epNumbers[$currentIndex + 1] : null;
        }

        return view('anikoto.watch', [
            'anime' => $anime,
            'episodes' => $episodes,
            'currentEpisode' => $currentEpisodeObj,
            'episodeNum' => $episode,
            'embedSub' => $embedSub,
            'embedDub' => $embedDub,
            'embedUrl' => $embedUrl,
            'prevEpisode' => $prevEpisode,
            'nextEpisode' => $nextEpisode,
            'id' => $id,
            'metaTitle' => ($anime['title'] ?? 'Watch') . " Episode {$episode} — AniVerse",
            'metaDescription' => "Watch " . ($anime['title'] ?? 'Anime') . " Episode {$episode} on AniVerse. English subbed and dubbed options.",
            'metaImage' => $anime['poster'] ?? '',
        ]);
    }
}
