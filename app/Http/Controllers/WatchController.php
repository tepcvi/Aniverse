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

        // Alternative streaming source: Anivexa API (walterwhite-69 aggregator)
        $anivexaMulti = ['sub' => [], 'dub' => []];
        try {
            $anivexaService = app(\App\Services\AnivexaService::class);
            $anilistId = $anime['id'] ?? ($id ?? null);
            if ($anilistId) {
                $anivexaMulti = $anivexaService->getMultiServers($anilistId, $episode);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Anivexa multi-source fallback failed: ' . $e->getMessage());
        }

        $embedVidcloudSub   = $anivexaMulti['sub']['vidcloud'] ?? null;
        $embedVidcloudDub   = $anivexaMulti['dub']['vidcloud'] ?? null;
        $embedUpcloudSub    = $anivexaMulti['sub']['upcloud'] ?? null;
        $embedUpcloudDub    = $anivexaMulti['dub']['upcloud'] ?? null;
        $embedMegacloudSub  = $anivexaMulti['sub']['megacloud'] ?? null;
        $embedMegacloudDub  = $anivexaMulti['dub']['megacloud'] ?? null;
        $embedGogoSub       = $anivexaMulti['sub']['gogoanime'] ?? null;
        $embedGogoDub       = $anivexaMulti['dub']['gogoanime'] ?? null;
        $embedFilemoonSub   = $anivexaMulti['sub']['filemoon'] ?? null;
        $embedFilemoonDub   = $anivexaMulti['dub']['filemoon'] ?? null;
        $embedStreamtapeSub = $anivexaMulti['sub']['streamtape'] ?? null;
        $embedStreamtapeDub = $anivexaMulti['dub']['streamtape'] ?? null;
        $embedDoodSub       = $anivexaMulti['sub']['doodstream'] ?? null;
        $embedDoodDub       = $anivexaMulti['dub']['doodstream'] ?? null;
        $embedStreamSbSub   = $anivexaMulti['sub']['streamsb'] ?? null;
        $embedStreamSbDub   = $anivexaMulti['dub']['streamsb'] ?? null;
        $embedMp4uploadSub  = $anivexaMulti['sub']['mp4upload'] ?? null;
        $embedMp4uploadDub  = $anivexaMulti['dub']['mp4upload'] ?? null;
        $embedMiruroSub     = $anivexaMulti['sub']['miruro'] ?? null;
        $embedMiruroDub     = $anivexaMulti['dub']['miruro'] ?? null;

        // Legacy Anivexa general aliases
        $embedAnivexaSub = $embedVidcloudSub ?? $embedUpcloudSub ?? $embedMegacloudSub ?? $embedGogoSub ?? $embedFilemoonSub ?? $embedStreamtapeSub ?? $embedDoodSub ?? $embedStreamSbSub ?? $embedMp4uploadSub ?? $embedMiruroSub;
        $embedAnivexaDub = $embedVidcloudDub ?? $embedUpcloudDub ?? $embedMegacloudDub ?? $embedGogoDub ?? $embedFilemoonDub ?? $embedStreamtapeDub ?? $embedDoodDub ?? $embedStreamSbDub ?? $embedMp4uploadDub ?? $embedMiruroDub;

        // Check if primary embed URLs are missing, fallback to best available
        $embedUrl = $embedSub ?? $embedDub ?? $embedAnivexaSub ?? $embedAnivexaDub;

        // Navigation calculations (find next / previous episode numbers)
        $prevEpisode = null;
        $nextEpisode = null;
        $epNumbers = array_map(fn($item) => (string)($item['number'] ?? ''), $episodes);
        
        // Remove empty strings
        $epNumbers = array_filter($epNumbers);
        
        // Find position of current episode
        $currentIndex = array_search((string)$episode, $epNumbers);
        if ($currentIndex !== false) {
            $values = array_values($epNumbers);
            $key = array_search((string)$episode, $values);
            if ($key > 0) {
                $prevEpisode = $values[$key - 1];
            }
            if ($key < count($values) - 1) {
                $nextEpisode = $values[$key + 1];
            }
        }

        return view('anikoto.watch', [
            'anime' => $anime,
            'episodes' => $episodes,
            'currentEpisode' => $currentEpisodeObj,
            'episodeNum' => $episode,
            'embedSub' => $embedSub,
            'embedDub' => $embedDub,
            'embedAnivexaSub' => $embedAnivexaSub,
            'embedAnivexaDub' => $embedAnivexaDub,
            'embedAnivexa' => $embedAnivexaSub ?? $embedAnivexaDub,
            'embedVidcloudSub'   => $embedVidcloudSub,
            'embedVidcloudDub'   => $embedVidcloudDub,
            'embedUpcloudSub'    => $embedUpcloudSub,
            'embedUpcloudDub'    => $embedUpcloudDub,
            'embedMegacloudSub'  => $embedMegacloudSub,
            'embedMegacloudDub'  => $embedMegacloudDub,
            'embedGogoSub'       => $embedGogoSub,
            'embedGogoDub'       => $embedGogoDub,
            'embedFilemoonSub'   => $embedFilemoonSub,
            'embedFilemoonDub'   => $embedFilemoonDub,
            'embedStreamtapeSub' => $embedStreamtapeSub,
            'embedStreamtapeDub' => $embedStreamtapeDub,
            'embedDoodSub'       => $embedDoodSub,
            'embedDoodDub'       => $embedDoodDub,
            'embedStreamSbSub'   => $embedStreamSbSub,
            'embedStreamSbDub'   => $embedStreamSbDub,
            'embedMp4uploadSub'  => $embedMp4uploadSub,
            'embedMp4uploadDub'  => $embedMp4uploadDub,
            'embedMiruroSub'     => $embedMiruroSub,
            'embedMiruroDub'     => $embedMiruroDub,
            'embedUrl' => $embedUrl,
            'prevEpisode' => $prevEpisode,
            'nextEpisode' => $nextEpisode,
            'id' => $id,
            'metaTitle' => ($anime['title'] ?? 'Watch') . " Episode {$episode} — Anitep",
            'metaDescription' => "Watch " . ($anime['title'] ?? 'Anime') . " Episode {$episode} on Anitep. English subbed and dubbed options.",
            'metaImage' => $anime['poster'] ?? '',
        ]);
    }
}
