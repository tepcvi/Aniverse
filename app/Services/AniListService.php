<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AniListService
{
    private const API_URL = 'https://graphql.anilist.co';

    // Cache TTLs in seconds
    private const CACHE_SHORT = 600;      // 10 minutes (trending, recent)
    private const CACHE_MEDIUM = 1800;    // 30 minutes (details, search)
    private const CACHE_LONG = 86400;     // 24 hours (genres, static data)

    /**
     * Execute a GraphQL query against the AniList API.
     */
    private function query(string $query, array $variables = []): ?array
    {
        try {
            $response = Http::retry(3, 200, throw: false)
                ->timeout(15)
                ->post(self::API_URL, [
                    'query' => $query,
                    'variables' => $variables,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['errors'])) {
                    Log::warning('AniList API returned errors', ['errors' => $data['errors']]);
                }
                return $data['data'] ?? null;
            }

            Log::error('AniList API request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('AniList API exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Common media fields fragment for list queries.
     */
    private function mediaFields(): string
    {
        return '
            id
            title {
                romaji
                english
                native
            }
            coverImage {
                extraLarge
                large
                medium
                color
            }
            bannerImage
            description(asHtml: false)
            averageScore
            meanScore
            popularity
            favourites
            episodes
            duration
            status
            format
            season
            seasonYear
            startDate { year month day }
            endDate { year month day }
            genres
            tags { name rank isMediaSpoiler }
            studios(isMain: true) { nodes { name } }
            source
            isAdult
            siteUrl
            trailer { id site thumbnail }
            nextAiringEpisode { airingAt timeUntilAiring episode }
        ';
    }

    /**
     * Detailed media fields for the anime detail page.
     */
    private function detailFields(): string
    {
        return $this->mediaFields() . '
            relations {
                edges {
                    relationType(version: 2)
                    node {
                        id
                        title { romaji english }
                        coverImage { large medium }
                        format
                        status
                        type
                        averageScore
                    }
                }
            }
            characters(sort: [ROLE, FAVOURITES_DESC], perPage: 12) {
                edges {
                    role
                    voiceActorRoles(language: JAPANESE, sort: RELEVANCE) {
                        voiceActor {
                            id
                            name { full native }
                            image { large medium }
                            language
                        }
                    }
                    node {
                        id
                        name { full native }
                        image { large medium }
                    }
                }
            }
            recommendations(sort: RATING_DESC, perPage: 8) {
                nodes {
                    mediaRecommendation {
                        id
                        title { romaji english }
                        coverImage { large medium }
                        format
                        averageScore
                        episodes
                        status
                    }
                }
            }
            stats {
                scoreDistribution { score amount }
                statusDistribution { status amount }
            }
        ';
    }

    /**
     * Get trending anime.
     */
    public function getTrending(int $page = 1, int $perPage = 20): ?array
    {
        $cacheKey = "anilist.trending.{$page}.{$perPage}";

        return Cache::remember($cacheKey, self::CACHE_SHORT, function () use ($page, $perPage) {
            return $this->query('
                query ($page: Int, $perPage: Int) {
                    Page(page: $page, perPage: $perPage) {
                        pageInfo { total currentPage lastPage hasNextPage perPage }
                        media(type: ANIME, sort: TRENDING_DESC, isAdult: false) {
                            ' . $this->mediaFields() . '
                        }
                    }
                }
            ', compact('page', 'perPage'));
        });
    }

    /**
     * Get popular anime (all time).
     */
    public function getPopular(int $page = 1, int $perPage = 20): ?array
    {
        $cacheKey = "anilist.popular.{$page}.{$perPage}";

        return Cache::remember($cacheKey, self::CACHE_SHORT, function () use ($page, $perPage) {
            return $this->query('
                query ($page: Int, $perPage: Int) {
                    Page(page: $page, perPage: $perPage) {
                        pageInfo { total currentPage lastPage hasNextPage perPage }
                        media(type: ANIME, sort: POPULARITY_DESC, isAdult: false) {
                            ' . $this->mediaFields() . '
                        }
                    }
                }
            ', compact('page', 'perPage'));
        });
    }

    /**
     * Get top rated anime.
     */
    public function getTopRated(int $page = 1, int $perPage = 20): ?array
    {
        $cacheKey = "anilist.toprated.{$page}.{$perPage}";

        return Cache::remember($cacheKey, self::CACHE_SHORT, function () use ($page, $perPage) {
            return $this->query('
                query ($page: Int, $perPage: Int) {
                    Page(page: $page, perPage: $perPage) {
                        pageInfo { total currentPage lastPage hasNextPage perPage }
                        media(type: ANIME, sort: SCORE_DESC, isAdult: false) {
                            ' . $this->mediaFields() . '
                        }
                    }
                }
            ', compact('page', 'perPage'));
        });
    }

    /**
     * Get seasonal anime.
     */
    public function getSeasonal(?string $season = null, ?int $year = null, int $page = 1, int $perPage = 20): ?array
    {
        $season = $season ?? $this->getCurrentSeason();
        $year = $year ?? (int) date('Y');
        $cacheKey = "anilist.seasonal.{$season}.{$year}.{$page}.{$perPage}";

        return Cache::remember($cacheKey, self::CACHE_SHORT, function () use ($season, $year, $page, $perPage) {
            return $this->query('
                query ($page: Int, $perPage: Int, $season: MediaSeason, $seasonYear: Int) {
                    Page(page: $page, perPage: $perPage) {
                        pageInfo { total currentPage lastPage hasNextPage perPage }
                        media(type: ANIME, season: $season, seasonYear: $seasonYear, sort: POPULARITY_DESC, isAdult: false) {
                            ' . $this->mediaFields() . '
                        }
                    }
                }
            ', [
                'page' => $page,
                'perPage' => $perPage,
                'season' => strtoupper($season),
                'seasonYear' => $year,
            ]);
        });
    }

    /**
     * Get recently released / currently airing anime.
     */
    public function getRecentlyReleased(int $page = 1, int $perPage = 20): ?array
    {
        $cacheKey = "anilist.recent.{$page}.{$perPage}";

        return Cache::remember($cacheKey, self::CACHE_SHORT, function () use ($page, $perPage) {
            return $this->query('
                query ($page: Int, $perPage: Int) {
                    Page(page: $page, perPage: $perPage) {
                        pageInfo { total currentPage lastPage hasNextPage perPage }
                        media(type: ANIME, status: RELEASING, sort: UPDATED_AT_DESC, isAdult: false) {
                            ' . $this->mediaFields() . '
                        }
                    }
                }
            ', compact('page', 'perPage'));
        });
    }

    /**
     * Get full anime details by ID.
     */
    public function getAnimeDetails(int $id): ?array
    {
        $cacheKey = "anilist.anime.{$id}";

        return Cache::remember($cacheKey, self::CACHE_MEDIUM, function () use ($id) {
            return $this->query('
                query ($id: Int) {
                    Media(id: $id, type: ANIME) {
                        ' . $this->detailFields() . '
                    }
                }
            ', compact('id'));
        });
    }

    /**
     * Search anime with flexible filters.
     */
    public function search(array $params): ?array
    {
        $variables = [
            'page' => $params['page'] ?? 1,
            'perPage' => $params['perPage'] ?? 20,
        ];

        $args = ['$page: Int', '$perPage: Int'];
        $filters = ['type: ANIME', 'isAdult: false'];
        $sort = 'sort: [SEARCH_MATCH]';

        if (!empty($params['query'])) {
            $variables['search'] = $params['query'];
            $args[] = '$search: String';
            $filters[] = 'search: $search';
        } else {
            $sort = 'sort: [POPULARITY_DESC]';
        }

        if (!empty($params['genre'])) {
            $variables['genre'] = $params['genre'];
            $args[] = '$genre: String';
            $filters[] = 'genre: $genre';
        }

        if (!empty($params['season'])) {
            $variables['season'] = strtoupper($params['season']);
            $args[] = '$season: MediaSeason';
            $filters[] = 'season: $season';
        }

        if (!empty($params['year'])) {
            $variables['seasonYear'] = (int) $params['year'];
            $args[] = '$seasonYear: Int';
            $filters[] = 'seasonYear: $seasonYear';
        }

        if (!empty($params['format'])) {
            $variables['format'] = strtoupper($params['format']);
            $args[] = '$format: MediaFormat';
            $filters[] = 'format: $format';
        }

        if (!empty($params['status'])) {
            $variables['status'] = strtoupper($params['status']);
            $args[] = '$status: MediaStatus';
            $filters[] = 'status: $status';
        }

        if (!empty($params['sort'])) {
            $sortMap = [
                'popularity' => 'POPULARITY_DESC',
                'score' => 'SCORE_DESC',
                'trending' => 'TRENDING_DESC',
                'newest' => 'START_DATE_DESC',
                'title' => 'TITLE_ROMAJI',
            ];
            if (isset($sortMap[$params['sort']])) {
                $sort = 'sort: [' . $sortMap[$params['sort']] . ']';
            }
        }

        $argsStr = implode(', ', $args);
        $filtersStr = implode(', ', $filters) . ', ' . $sort;

        $cacheKey = 'anilist.search.' . md5(json_encode($variables));

        return Cache::remember($cacheKey, self::CACHE_MEDIUM, function () use ($argsStr, $filtersStr, $variables) {
            return $this->query("
                query ({$argsStr}) {
                    Page(page: \$page, perPage: \$perPage) {
                        pageInfo { total currentPage lastPage hasNextPage perPage }
                        media({$filtersStr}) {
                            {$this->mediaFields()}
                        }
                    }
                }
            ", $variables);
        });
    }

    /**
     * Get all available genres.
     */
    public function getGenres(): ?array
    {
        return Cache::remember('anilist.genres', self::CACHE_LONG, function () {
            return $this->query('query { GenreCollection }');
        });
    }

    /**
     * Lightweight search for autocomplete suggestions.
     */
    public function getAutocomplete(string $search): ?array
    {
        if (strlen($search) < 2) {
            return null;
        }

        $cacheKey = 'anilist.autocomplete.' . md5($search);

        return Cache::remember($cacheKey, self::CACHE_SHORT, function () use ($search) {
            return $this->query('
                query ($search: String) {
                    Page(page: 1, perPage: 6) {
                        media(type: ANIME, search: $search, sort: POPULARITY_DESC, isAdult: false) {
                            id
                            title { romaji english }
                            coverImage { medium }
                            format
                            seasonYear
                            averageScore
                        }
                    }
                }
            ', compact('search'));
        });
    }

    /**
     * Get featured anime for hero banner (top trending seasonal anime).
     */
    public function getFeatured(): ?array
    {
        $season = $this->getCurrentSeason();
        $year = (int) date('Y');
        $cacheKey = "anilist.featured.{$season}.{$year}";

        return Cache::remember($cacheKey, self::CACHE_SHORT, function () use ($season, $year) {
            return $this->query('
                query ($season: MediaSeason, $seasonYear: Int) {
                    Page(page: 1, perPage: 5) {
                        media(type: ANIME, season: $season, seasonYear: $seasonYear, sort: POPULARITY_DESC, isAdult: false) {
                            id
                            title { romaji english native }
                            bannerImage
                            coverImage { extraLarge large }
                            description(asHtml: false)
                            averageScore
                            episodes
                            genres
                            format
                            status
                            season
                            seasonYear
                            studios(isMain: true) { nodes { name } }
                        }
                    }
                }
            ', [
                'season' => strtoupper($season),
                'seasonYear' => $year,
            ]);
        });
    }

    /**
     * Determine the current anime season.
     */
    public function getCurrentSeason(): string
    {
        $month = (int) date('n');
        return match (true) {
            $month >= 1 && $month <= 3 => 'WINTER',
            $month >= 4 && $month <= 6 => 'SPRING',
            $month >= 7 && $month <= 9 => 'SUMMER',
            default => 'FALL',
        };
    }

    /**
     * Get all seasons list for filter dropdowns.
     */
    public function getSeasons(): array
    {
        return ['WINTER', 'SPRING', 'SUMMER', 'FALL'];
    }

    /**
     * Get available formats for filter dropdowns.
     */
    public function getFormats(): array
    {
        return ['TV', 'TV_SHORT', 'MOVIE', 'SPECIAL', 'OVA', 'ONA', 'MUSIC'];
    }

    /**
     * Get available statuses for filter dropdowns.
     */
    public function getStatuses(): array
    {
        return ['FINISHED', 'RELEASING', 'NOT_YET_RELEASED', 'CANCELLED', 'HIATUS'];
    }

    /**
     * Format a status string for display.
     */
    public static function formatStatus(string $status): string
    {
        return match ($status) {
            'FINISHED' => 'Finished',
            'RELEASING' => 'Airing',
            'NOT_YET_RELEASED' => 'Not Yet Aired',
            'CANCELLED' => 'Cancelled',
            'HIATUS' => 'Hiatus',
            default => ucfirst(strtolower(str_replace('_', ' ', $status))),
        };
    }

    /**
     * Format a media format string for display.
     */
    public static function formatType(string $format): string
    {
        return match ($format) {
            'TV' => 'TV',
            'TV_SHORT' => 'TV Short',
            'MOVIE' => 'Movie',
            'SPECIAL' => 'Special',
            'OVA' => 'OVA',
            'ONA' => 'ONA',
            'MUSIC' => 'Music',
            default => ucfirst(strtolower(str_replace('_', ' ', $format))),
        };
    }
}
