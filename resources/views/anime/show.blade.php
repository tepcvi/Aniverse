@extends('layouts.app')

@section('content')
@php
    $title = $anime['title']['english'] ?? $anime['title']['romaji'] ?? 'Unknown';
    $romaji = $anime['title']['romaji'] ?? '';
    $native = $anime['title']['native'] ?? '';
    $banner = $anime['bannerImage'] ?? '';
    $cover = $anime['coverImage']['extraLarge'] ?? $anime['coverImage']['large'] ?? '';
    $color = $anime['coverImage']['color'] ?? '#6366f1';
    $description = $anime['description'] ?? '';
    $score = $anime['averageScore'] ?? null;
    $popularity = $anime['popularity'] ?? null;
    $favourites = $anime['favourites'] ?? null;
    $episodes = $anime['episodes'] ?? '?';
    $duration = $anime['duration'] ?? null;
    $status = $anime['status'] ?? '';
    $format = $anime['format'] ?? '';
    $source = $anime['source'] ?? '';
    $season = $anime['season'] ?? '';
    $seasonYear = $anime['seasonYear'] ?? '';
    $genres = $anime['genres'] ?? [];
    $tags = array_filter($anime['tags'] ?? [], fn($t) => !($t['isMediaSpoiler'] ?? false));
    $studios = $anime['studios']['nodes'] ?? [];
    $trailer = $anime['trailer'] ?? null;
    $characters = $anime['characters']['edges'] ?? [];
    $relations = $anime['relations']['edges'] ?? [];
    $recommendations = $anime['recommendations']['nodes'] ?? [];
    $nextAiring = $anime['nextAiringEpisode'] ?? null;
@endphp

{{-- Banner --}}
<div class="relative h-72 md:h-96 overflow-hidden">
    @if($banner)
        <img src="{{ $banner }}" alt="{{ $title }}" class="w-full h-full object-cover">
    @endif
    <div class="absolute inset-0 bg-gradient-to-t from-dark-950 via-dark-950/60 to-transparent"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-dark-950/80 to-transparent"></div>
</div>

{{-- Main Content --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-32 relative z-10">
    <div class="flex flex-col md:flex-row gap-8">
        {{-- Cover Image --}}
        <div class="shrink-0">
            <div class="w-48 md:w-56 rounded-2xl overflow-hidden shadow-2xl ring-4 ring-dark-800/50 light:ring-white/50">
                <img src="{{ $cover }}" alt="{{ $title }}" class="w-full aspect-[3/4] object-cover">
            </div>
        </div>

        {{-- Info Area --}}
        <div class="flex-1 pt-4 md:pt-16">
            {{-- Title --}}
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-white light:text-dark-900 leading-tight mb-2">
                {{ $title }}
            </h1>
            @if($romaji && $romaji !== $title)
                <p class="text-lg text-dark-400 light:text-dark-500 mb-1">{{ $romaji }}</p>
            @endif
            @if($native)
                <p class="text-sm text-dark-500 light:text-dark-400 mb-4">{{ $native }}</p>
            @endif

            {{-- Quick Stats --}}
            <div class="flex flex-wrap items-center gap-4 mb-6">
                @if($score)
                    <x-score-badge :score="$score" class="text-base px-3 py-1.5" />
                @endif
                @if($popularity)
                    <div class="flex items-center gap-1.5 text-dark-400 light:text-dark-500">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                        <span class="text-sm">{{ number_format($popularity) }} users</span>
                    </div>
                @endif
                @if($favourites)
                    <div class="flex items-center gap-1.5 text-dark-400 light:text-dark-500">
                        <svg class="w-4 h-4 text-rose-400" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                        <span class="text-sm">{{ number_format($favourites) }} favourites</span>
                    </div>
                @endif
            </div>

            {{-- Next Airing --}}
            @if($nextAiring)
                <div class="mb-6 p-4 rounded-xl bg-primary-500/10 border border-primary-500/20">
                    <div class="flex items-center gap-2 text-primary-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="font-semibold">Episode {{ $nextAiring['episode'] }}</span>
                        <span class="text-sm text-dark-400">airs in</span>
                        <span class="font-semibold">
                            @php
                                $seconds = $nextAiring['timeUntilAiring'];
                                $days = floor($seconds / 86400);
                                $hours = floor(($seconds % 86400) / 3600);
                            @endphp
                            {{ $days > 0 ? $days . 'd ' : '' }}{{ $hours }}h
                        </span>
                    </div>
                </div>
            @endif

            {{-- Genres --}}
            @if(count($genres))
                <div class="flex flex-wrap gap-2 mb-6">
                    @foreach($genres as $genre)
                        <a href="{{ route('genres.show', $genre) }}" class="px-3 py-1.5 text-xs font-semibold rounded-full bg-dark-800/60 light:bg-dark-100 text-dark-200 light:text-dark-600 border border-dark-700/50 light:border-dark-200 hover:border-primary-500/50 hover:text-primary-400 transition-all">
                            {{ $genre }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Details Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-10">
        {{-- Left Column: Synopsis + Content --}}
        <div class="lg:col-span-2 space-y-10">

            {{-- Synopsis --}}
            @if($description)
                <section>
                    <h2 class="text-xl font-bold text-white light:text-dark-900 mb-4">Synopsis</h2>
                    <div class="prose prose-sm prose-invert light:prose max-w-none text-dark-300 light:text-dark-600 leading-relaxed">
                        {!! nl2br(e(strip_tags($description))) !!}
                    </div>
                </section>
            @endif

            {{-- Streaming Episodes --}}
            <section class="border-t border-dark-800/80 pt-8 mt-6">
                <h2 class="text-xl font-bold text-white light:text-dark-900 mb-4 flex items-center justify-between">
                    <span>📺 Watch Episodes</span>
                    @if(!empty($streamingEpisodes))
                        <span class="text-xs text-dark-400 font-normal">{{ count($streamingEpisodes) }} episodes found</span>
                    @endif
                </h2>
                
                @if(!empty($streamingEpisodes))
                    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3">
                        @foreach(array_reverse($streamingEpisodes) as $ep)
                            <a href="{{ route('watch', ['slug' => $streamingSlug, 'episode' => $ep['num']]) }}"
                               class="py-3 px-4 rounded-xl bg-dark-800/40 hover:bg-primary-600/10 border border-dark-800 hover:border-primary-500/30 text-center font-bold text-sm text-white light:text-dark-900 transition-all duration-200">
                                Ep {{ $ep['num'] }}
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="p-6 rounded-2xl bg-dark-900/30 border border-dark-800/60 text-center">
                        <p class="text-sm text-dark-400 mb-4">No direct streaming episodes found under this title.</p>
                        <a href="{{ route('anikoto.search') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-600 text-white text-xs font-semibold hover:bg-primary-500 transition-colors">
                            Search Streaming Database
                        </a>
                    </div>
                @endif
            </section>

            {{-- Trailer --}}
            @if($trailer && $trailer['site'] === 'youtube')
                <section>
                    <h2 class="text-xl font-bold text-white light:text-dark-900 mb-4">Trailer</h2>
                    <div class="aspect-video rounded-2xl overflow-hidden bg-dark-800">
                        <iframe
                            src="https://www.youtube.com/embed/{{ $trailer['id'] }}"
                            title="{{ $title }} Trailer"
                            class="w-full h-full"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                            loading="lazy"
                        ></iframe>
                    </div>
                </section>
            @endif

            {{-- Characters --}}
            @if(count($characters))
                <section>
                    <h2 class="text-xl font-bold text-white light:text-dark-900 mb-4">Characters & Voice Actors</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($characters as $char)
                            <x-character-card :character="$char" />
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- Relations --}}
            @if(count($relations))
                <section>
                    <h2 class="text-xl font-bold text-white light:text-dark-900 mb-4">Relations</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($relations as $rel)
                            @php
                                $relNode = $rel['node'];
                                $relType = $rel['relationType'] ?? '';
                                $relTitle = $relNode['title']['english'] ?? $relNode['title']['romaji'] ?? 'Unknown';
                                $relCover = $relNode['coverImage']['large'] ?? $relNode['coverImage']['medium'] ?? '';
                                $isAnime = ($relNode['type'] ?? '') === 'ANIME';
                            @endphp
                            <a href="{{ $isAnime ? route('anime.show', $relNode['id']) : '#' }}" class="flex gap-3 p-3 rounded-xl bg-dark-800/50 light:bg-white border border-dark-700/50 light:border-dark-200 hover:border-primary-500/30 transition-all group {{ !$isAnime ? 'opacity-60' : '' }}">
                                <img src="{{ $relCover }}" alt="{{ $relTitle }}" class="w-16 h-22 rounded-lg object-cover shrink-0" loading="lazy">
                                <div class="min-w-0">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-primary-400">{{ str_replace('_', ' ', $relType) }}</span>
                                    <p class="text-sm font-semibold text-white light:text-dark-900 line-clamp-2 mt-1 group-hover:text-primary-400 transition-colors">{{ $relTitle }}</p>
                                    <p class="text-xs text-dark-500 mt-1">{{ \App\Services\AniListService::formatType($relNode['format'] ?? '') }} • {{ \App\Services\AniListService::formatStatus($relNode['status'] ?? '') }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- Recommendations --}}
            @if(count($recommendations))
                <section>
                    <h2 class="text-xl font-bold text-white light:text-dark-900 mb-4">Recommendations</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @foreach($recommendations as $rec)
                            @if($rec['mediaRecommendation'] ?? null)
                                <x-anime-card :anime="$rec['mediaRecommendation']" />
                            @endif
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        {{-- Right Sidebar: Info Panel --}}
        <div class="space-y-6">
            <div class="rounded-2xl bg-dark-800/50 light:bg-white border border-dark-700/50 light:border-dark-200 overflow-hidden">
                <div class="p-6 space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-dark-400 light:text-dark-500">Information</h3>

                    @if($format)
                    <div>
                        <span class="text-xs text-dark-500 light:text-dark-400">Format</span>
                        <p class="text-sm font-medium text-white light:text-dark-900">{{ \App\Services\AniListService::formatType($format) }}</p>
                    </div>
                    @endif

                    @if($episodes && $episodes !== '?')
                    <div>
                        <span class="text-xs text-dark-500 light:text-dark-400">Episodes</span>
                        <p class="text-sm font-medium text-white light:text-dark-900">{{ $episodes }}</p>
                    </div>
                    @endif

                    @if($duration)
                    <div>
                        <span class="text-xs text-dark-500 light:text-dark-400">Duration</span>
                        <p class="text-sm font-medium text-white light:text-dark-900">{{ $duration }} mins</p>
                    </div>
                    @endif

                    @if($status)
                    <div>
                        <span class="text-xs text-dark-500 light:text-dark-400">Status</span>
                        <p class="text-sm font-medium @if($status === 'RELEASING') text-emerald-400 @elseif($status === 'FINISHED') text-sky-400 @else text-white light:text-dark-900 @endif">
                            {{ \App\Services\AniListService::formatStatus($status) }}
                        </p>
                    </div>
                    @endif

                    @if($season && $seasonYear)
                    <div>
                        <span class="text-xs text-dark-500 light:text-dark-400">Season</span>
                        <p class="text-sm font-medium text-white light:text-dark-900">{{ ucfirst(strtolower($season)) }} {{ $seasonYear }}</p>
                    </div>
                    @endif

                    @if($source)
                    <div>
                        <span class="text-xs text-dark-500 light:text-dark-400">Source</span>
                        <p class="text-sm font-medium text-white light:text-dark-900">{{ ucfirst(strtolower(str_replace('_', ' ', $source))) }}</p>
                    </div>
                    @endif

                    @if(count($studios))
                    <div>
                        <span class="text-xs text-dark-500 light:text-dark-400">Studios</span>
                        <p class="text-sm font-medium text-white light:text-dark-900">{{ implode(', ', array_column($studios, 'name')) }}</p>
                    </div>
                    @endif

                    @if($anime['meanScore'] ?? null)
                    <div>
                        <span class="text-xs text-dark-500 light:text-dark-400">Mean Score</span>
                        <p class="text-sm font-medium text-white light:text-dark-900">{{ $anime['meanScore'] }}%</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Tags --}}
            @if(count($tags))
                <div class="rounded-2xl bg-dark-800/50 light:bg-white border border-dark-700/50 light:border-dark-200 overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-dark-400 light:text-dark-500 mb-4">Tags</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach(array_slice($tags, 0, 15) as $tag)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs rounded-lg bg-dark-700/50 light:bg-dark-100 text-dark-300 light:text-dark-600">
                                    {{ $tag['name'] }}
                                    <span class="text-dark-500 text-[10px]">{{ $tag['rank'] }}%</span>
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- External Link --}}
            @if($anime['siteUrl'] ?? null)
                <a href="{{ $anime['siteUrl'] }}" target="_blank" rel="noopener" class="flex items-center justify-center gap-2 w-full px-4 py-3 rounded-xl bg-dark-800/50 light:bg-white border border-dark-700/50 light:border-dark-200 text-dark-300 light:text-dark-600 hover:text-primary-400 hover:border-primary-500/30 transition-all text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    View on AniList
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
