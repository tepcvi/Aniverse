<?php

namespace App\Http\Controllers;

use App\Services\AnikotoService;
use Illuminate\Http\Request;

class EpisodeController extends Controller
{
    public function __construct(
        private readonly AnikotoService $anikoto
    ) {}

    /**
     * Get episode list for an anime.
     * GET /anime/{slug}/episodes
     */
    public function index(string $slug)
    {
        $info = $this->anikoto->getAnimeDetails($slug);
        if (!$info) {
            abort(404, 'Anime details not found');
        }

        $episodes = $this->anikoto->getEpisodeList($slug);

        return view('anikoto.episodes', [
            'info' => $info,
            'episodes' => $episodes,
            'slug' => $slug,
            'metaTitle' => ($info['title'] ?? 'Unknown') . ' - Episode List - AniVerse',
        ]);
    }

    /**
     * Get details for a specific episode (JSON API endpoint).
     * GET /api/episodes/{slug}/{episode}
     */
    public function show(string $slug, string $episode)
    {
        $ep = $this->anikoto->getEpisodeDetails($slug, $episode);

        if (!$ep) {
            return response()->json([
                'success' => false,
                'message' => "Episode {$episode} not found."
            ], 404);
        }

        return response()->json([
            'success' => true,
            'episode' => $ep
        ]);
    }
}
