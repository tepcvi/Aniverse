<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnikotoService
{
    private string $baseUrl;

    // Cache TTLs in seconds
    private const CACHE_SHORT = 300;     // 5 minutes (for feeds / page lists)
    private const CACHE_LONG = 3600;     // 1 hour (for series / static data)

    public function __construct()
    {
        $this->baseUrl = config('services.anikoto.base_url', 'https://anikotoapi.site');
    }

    /**
     * Make a GET request with retry, timeout, validation, caching and logging.
     */
    private function request(string $endpoint, array $query = [], int $ttl = self::CACHE_SHORT): ?array
    {
        $cacheKey = 'anikoto_api_' . md5($endpoint . '?' . http_build_query($query));

        return Cache::remember($cacheKey, $ttl, function () use ($endpoint, $query) {
            try {
                // Timeout after 10 seconds, retry 3 times with 100ms delay
                $response = Http::retry(3, 100)
                    ->timeout(10)
                    ->baseUrl($this->baseUrl)
                    ->get($endpoint, $query);

                // Log every API response
                Log::info('Anikoto API Response', [
                    'url' => $this->baseUrl . $endpoint,
                    'query' => $query,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                if ($response->successful()) {
                    $json = $response->json();
                    
                    // Validate every response contains 'ok' and 'data' before rendering
                    if (is_array($json) && isset($json['ok']) && $json['ok'] === true) {
                        return $json;
                    }
                    
                    Log::warning('Anikoto API returned invalid schema', [
                        'response' => $json
                    ]);
                }

                return null;
            } catch (\Exception $e) {
                Log::error('Anikoto API request exception', [
                    'endpoint' => $endpoint,
                    'query' => $query,
                    'error' => $e->getMessage()
                ]);
                return null;
            }
        });
    }

    /**
     * Fetch recent anime catalog page.
     * GET /recent-anime?page=1&per_page=20
     */
    public function recentAnime(int $page = 1, int $perPage = 20): ?array
    {
        return $this->request('/recent-anime', [
            'page' => $page,
            'per_page' => $perPage
        ], self::CACHE_SHORT);
    }

    /**
     * Fetch series details and episode list.
     * GET /series/{id}
     */
    public function series(string|int $id): ?array
    {
        return $this->request("/series/{$id}", [], self::CACHE_LONG);
    }

    /**
     * Map AniList ID or MAL ID to Anikoto numeric series ID.
     * Caches the entire catalog mapping for 7 days.
     */
    public function resolveAnikotoId(string|int $aniListId, string|int $malId = null): ?int
    {
        $mappings = Cache::remember('anikoto_id_mappings_v2', 86400 * 7, function () {
            $totalPages = 89;
            $responses = Http::pool(fn ($pool) => array_map(
                fn ($page) => $pool->baseUrl($this->baseUrl)->get('/recent-anime', ['page' => $page, 'per_page' => 100]),
                range(1, $totalPages)
            ));

            $maps = ['ani_id' => [], 'mal_id' => []];
            foreach ($responses as $res) {
                if ($res instanceof \Illuminate\Http\Client\Response && $res->successful()) {
                    $data = $res->json();
                    if (isset($data['data']) && is_array($data['data'])) {
                        foreach ($data['data'] as $anime) {
                            if (!empty($anime['id'])) {
                                if (!empty($anime['ani_id'])) {
                                    $maps['ani_id'][(string)$anime['ani_id']] = (int)$anime['id'];
                                }
                                if (!empty($anime['mal_id'])) {
                                    $maps['mal_id'][(string)$anime['mal_id']] = (int)$anime['id'];
                                }
                            }
                        }
                    }
                }
            }
            return $maps;
        });

        // 1. Check cached AniList mapping
        if ($aniListId && isset($mappings['ani_id'][(string)$aniListId])) {
            return $mappings['ani_id'][(string)$aniListId];
        }

        // 2. Check cached MAL mapping
        if ($malId && isset($mappings['mal_id'][(string)$malId])) {
            return $mappings['mal_id'][(string)$malId];
        }

        // 3. Fallback scan of page 1 and 2 for newly added anime (freshness guard)
        try {
            $newFeed = Http::pool(fn ($pool) => [
                $pool->baseUrl($this->baseUrl)->get('/recent-anime', ['page' => 1, 'per_page' => 100]),
                $pool->baseUrl($this->baseUrl)->get('/recent-anime', ['page' => 2, 'per_page' => 100]),
            ]);

            foreach ($newFeed as $res) {
                if ($res instanceof \Illuminate\Http\Client\Response && $res->successful()) {
                    $data = $res->json();
                    if (isset($data['data']) && is_array($data['data'])) {
                        foreach ($data['data'] as $anime) {
                            if (!empty($anime['id'])) {
                                if (!empty($anime['ani_id']) && (string)$anime['ani_id'] === (string)$aniListId) {
                                    return (int)$anime['id'];
                                }
                                if (!empty($anime['mal_id']) && $malId && (string)$anime['mal_id'] === (string)$malId) {
                                    return (int)$anime['id'];
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Freshness scan fallback failed in resolveAnikotoId: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Search the anime catalog.
     * Searches pages of recent anime in-memory and returns matches.
     */
    public function search(string $query): array
    {
        $query = strtolower(trim($query));
        if (empty($query)) {
            return [];
        }

        $results = [];
        // Scan first 5 pages to find matches
        for ($page = 1; $page <= 5; $page++) {
            $feed = $this->recentAnime($page, 20);
            if (!empty($feed['data'])) {
                foreach ($feed['data'] as $item) {
                    $title = strtolower($item['title'] ?? '');
                    $alternative = strtolower($item['alternative'] ?? '');
                    $native = strtolower($item['native'] ?? '');
                    $titles = strtolower($item['titles'] ?? '');

                    if (str_contains($title, $query) || 
                        str_contains($alternative, $query) || 
                        str_contains($native, $query) || 
                        str_contains($titles, $query)) {
                        $results[$item['id']] = $item;
                    }
                }
            }
        }

        return array_values($results);
    }

    /**
     * Get details of a single episode.
     */
    public function getEpisode(string|int $seriesId, string|int $episodeNum): ?array
    {
        $seriesData = $this->series($seriesId);
        $episodes = $seriesData['data']['episodes'] ?? [];

        foreach ($episodes as $ep) {
            if ((string)($ep['number'] ?? '') === (string)$episodeNum) {
                return $ep;
            }
        }

        return null;
    }

    /**
     * Generate the documented MegaPlay embed URL formats.
     */
    public function getEmbedUrl(array $anime, array $episode, string $language = 'sub'): ?string
    {
        $language = strtolower($language) === 'dub' ? 'dub' : 'sub';

        // 1. Try episode_embed_id (aniwatch-ep-id style)
        if (!empty($episode['episode_embed_id'])) {
            return "https://animeplay.cfd/stream/s-2/{$episode['episode_embed_id']}/{$language}";
        }

        // 2. Try parsing megaplay.buzz domain fallback from API
        if (!empty($episode['embed_url'][$language])) {
            return str_replace('megaplay.buzz', 'animeplay.cfd', $episode['embed_url'][$language]);
        }

        // 3. Fallback to AniList ID (ani_id) or MAL ID (mal_id) if embed ID is missing
        $epNum = $episode['number'] ?? 1;
        if (!empty($anime['ani_id'])) {
            return "https://animeplay.cfd/stream/ani/{$anime['ani_id']}/{$epNum}/{$language}";
        }
        if (!empty($anime['mal_id'])) {
            return "https://animeplay.cfd/stream/mal/{$anime['mal_id']}/{$epNum}/{$language}";
        }

        return null;
    }

    /**
     * Legacy compatibility alias: search anime.
     */
    public function searchAnime(string $query): array
    {
        return $this->search($query);
    }

    /**
     * Legacy compatibility alias: get episodes list by ID.
     */
    public function getEpisodes(string|int $id): array
    {
        $res = $this->series($id);
        return $res['data']['episodes'] ?? [];
    }

    /**
     * Legacy compatibility alias: get full anime details including info and episodes.
     */
    public function getFullAnimeDetails(string $id): array
    {
        $res = $this->series($id);
        return [
            'info' => $res['data']['anime'] ?? null,
            'episodes' => $res['data']['episodes'] ?? [],
            'dataId' => $res['data']['anime']['id'] ?? $id,
        ];
    }

    /**
     * Legacy compatibility alias: get schedule.
     */
    public function getSchedule(string $date): array
    {
        return [];
    }

    /**
     * Legacy compatibility alias: check health of the API endpoint.
     */
    public function isHealthy(): bool
    {
        try {
            $response = Http::timeout(4)->get($this->baseUrl . '/recent-anime', ['per_page' => 1]);
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}
