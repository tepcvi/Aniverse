<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnivexaService
{
    private string $baseUrl;

    // Cache TTLs in seconds
    private const CACHE_SHORT = 300;     // 5 minutes
    private const CACHE_LONG  = 3600;    // 1 hour

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.anivexa.base_url', 'http://localhost:8001'), '/');
    }

    /**
     * Make a cached GET request to the local/Railway Anivexa API aggregator server.
     */
    private function request(string $endpoint, array $query = [], int $ttl = self::CACHE_SHORT): ?array
    {
        $cacheKey = 'anivexa_api_' . md5($endpoint . '?' . http_build_query($query));

        return Cache::remember($cacheKey, $ttl, function () use ($endpoint, $query) {
            try {
                $response = Http::retry(2, 200)
                    ->timeout(8)
                    ->baseUrl($this->baseUrl)
                    ->get($endpoint, $query);

                Log::info('Anivexa API Response', [
                    'url'    => $this->baseUrl . $endpoint,
                    'query'  => $query,
                    'status' => $response->status(),
                ]);

                if ($response->successful()) {
                    return $response->json();
                }

                return null;
            } catch (\Exception $e) {
                Log::warning('Anivexa API request failed (Server might be offline): ' . $e->getMessage(), [
                    'endpoint' => $endpoint,
                ]);
                return null;
            }
        });
    }

    // ─── Public API Methods ─────────────────────────────────────

    /**
     * Get episode list or provider info for an AniList ID.
     */
    public function getEpisodes(string|int $anilistId): ?array
    {
        if (empty($anilistId)) {
            return null;
        }

        $res = $this->request("/api/episodes/{$anilistId}", [], self::CACHE_SHORT);
        if (!$res) {
            $res = $this->request('/api/episodes', ['id' => $anilistId], self::CACHE_SHORT);
        }
        if (!$res) {
            $res = $this->request("/episodes/{$anilistId}", [], self::CACHE_SHORT);
        }

        if (isset($res['data'])) {
            return $res['data'];
        }
        return is_array($res) ? $res : null;
    }

    /**
     * Resolve Sub and Dub streaming URLs for a specific episode using AniList ID.
     *
     * @return array  ['sub' => ?string, 'dub' => ?string]
     */
    public function getStreamUrls(string|int $anilistId, string|int $episodeNum): array
    {
        $multi = $this->getMultiServers($anilistId, $episodeNum);
        return [
            'sub' => $multi['sub']['vidcloud'] ?? $multi['sub']['upcloud'] ?? $multi['sub']['gogoanime'] ?? $multi['sub']['streamsb'] ?? $multi['sub']['miruro'] ?? null,
            'dub' => $multi['dub']['vidcloud'] ?? $multi['dub']['upcloud'] ?? $multi['dub']['gogoanime'] ?? $multi['dub']['streamsb'] ?? $multi['dub']['miruro'] ?? null,
        ];
    }

    /**
     * Unpack all multi-source streaming providers from Anivexa API into individual server slots.
     * Returns a dictionary of providers for both Sub and Dub tracks.
     *
     * @return array
     */
    public function getMultiServers(string|int $anilistId, string|int $episodeNum): array
    {
        $servers = [
            'sub' => [
                'vidcloud'   => null,
                'upcloud'    => null,
                'megacloud'  => null,
                'gogoanime'  => null,
                'filemoon'   => null,
                'streamtape' => null,
                'doodstream' => null,
                'streamsb'   => null,
                'mp4upload'  => null,
                'miruro'     => null,
            ],
            'dub' => [
                'vidcloud'   => null,
                'upcloud'    => null,
                'megacloud'  => null,
                'gogoanime'  => null,
                'filemoon'   => null,
                'streamtape' => null,
                'doodstream' => null,
                'streamsb'   => null,
                'mp4upload'  => null,
                'miruro'     => null,
            ],
        ];

        if (empty($anilistId)) {
            return $servers;
        }

        // Query various REST aggregator endpoints in Anivexa / Miruro forks
        $res = $this->request('/api/sources', ['id' => $anilistId, 'ep' => $episodeNum], self::CACHE_SHORT);
        if (!$res) {
            $res = $this->request('/api/watch', ['anilistId' => $anilistId, 'episode' => $episodeNum], self::CACHE_SHORT);
        }
        if (!$res) {
            $res = $this->request("/sources/{$anilistId}/{$episodeNum}", [], self::CACHE_SHORT);
        }
        if (!$res) {
            $res = $this->request("/watch/{$anilistId}/{$episodeNum}", [], self::CACHE_SHORT);
        }

        if (!empty($res)) {
            // Helper closure to map provider names to slot keys
            $assignProvider = function ($url, $name, $lang) use (&$servers) {
                if (empty($url)) return;
                $name = strtolower((string)$name);
                if (str_contains($name, 'mega') || str_contains($name, 'cloud-3')) {
                    $servers[$lang]['megacloud'] = $servers[$lang]['megacloud'] ?? $url;
                } elseif (str_contains($name, 'vid') || str_contains($name, 'hianime') || str_contains($name, 'cloud-1')) {
                    $servers[$lang]['vidcloud'] = $servers[$lang]['vidcloud'] ?? $url;
                } elseif (str_contains($name, 'up') || str_contains($name, 'moviebox') || str_contains($name, 'cloud-2')) {
                    $servers[$lang]['upcloud'] = $servers[$lang]['upcloud'] ?? $url;
                } elseif (str_contains($name, 'gogo') || str_contains($name, 'goload') || str_contains($name, 'cdn')) {
                    $servers[$lang]['gogoanime'] = $servers[$lang]['gogoanime'] ?? $url;
                } elseif (str_contains($name, 'moon') || str_contains($name, 'filemoon')) {
                    $servers[$lang]['filemoon'] = $servers[$lang]['filemoon'] ?? $url;
                } elseif (str_contains($name, 'tape') || str_contains($name, 'streamtape')) {
                    $servers[$lang]['streamtape'] = $servers[$lang]['streamtape'] ?? $url;
                } elseif (str_contains($name, 'dood') || str_contains($name, 'doodstream')) {
                    $servers[$lang]['doodstream'] = $servers[$lang]['doodstream'] ?? $url;
                } elseif (str_contains($name, 'sb') || str_contains($name, 'streamsb')) {
                    $servers[$lang]['streamsb'] = $servers[$lang]['streamsb'] ?? $url;
                } elseif (str_contains($name, 'mp4') || str_contains($name, 'upload')) {
                    $servers[$lang]['mp4upload'] = $servers[$lang]['mp4upload'] ?? $url;
                } else {
                    $servers[$lang]['miruro'] = $servers[$lang]['miruro'] ?? $url;
                }
            };

            // Parse separate sub/dub arrays if present
            foreach (['sub', 'dub'] as $lang) {
                if (isset($res[$lang]) && is_array($res[$lang])) {
                    foreach ($res[$lang] as $item) {
                        $url  = is_string($item) ? $item : ($item['url'] ?? ($item['link'] ?? null));
                        $name = is_string($item) ? 'vidcloud' : ($item['server'] ?? ($item['name'] ?? ($item['source'] ?? 'vidcloud')));
                        $assignProvider($url, $name, $lang);
                    }
                }
            }

            // Parse unified sources array
            $items = isset($res['sources']) ? $res['sources'] : (isset($res['servers']) ? $res['servers'] : (isset($res['data']) ? $res['data'] : (is_array($res) ? $res : [])));
            if (is_array($items)) {
                foreach ($items as $item) {
                    if (is_array($item) && !empty($item['url'])) {
                        $audio = strtolower($item['audio'] ?? ($item['type'] ?? 'sub'));
                        $lang  = (str_contains($audio, 'dub') || str_contains($audio, 'eng')) ? 'dub' : 'sub';
                        $name  = $item['server'] ?? ($item['name'] ?? ($item['source'] ?? ($item['provider'] ?? 'vidcloud')));
                        $assignProvider($item['url'], $name, $lang);
                    }
                }
            }
        }

        return $servers;
    }

    /**
     * Check whether the local/Railway Anivexa API server instance is reachable.
     */
    public function isHealthy(): bool
    {
        try {
            $response = Http::timeout(2)->get($this->baseUrl . '/api/health');
            if ($response->successful()) {
                return true;
            }
            $fallback = Http::timeout(2)->get($this->baseUrl);
            return $fallback->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}
