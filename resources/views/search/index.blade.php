@extends('layouts.app')

@php
    $metaTitle = ($filters['query'] ?? false) ? "Search: {$filters['query']} — AniVerse" : 'Search Anime — AniVerse';
@endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8 text-left">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-white">
            🔍 Search Anime
        </h1>
        <p class="text-dark-400 mt-2">Find anime by title, genre, season, format, and more</p>
    </div>

    {{-- Filter Bar --}}
    <div class="mb-10 text-left">
        <x-filter-bar
            :genres="$genres"
            :seasons="$seasons"
            :formats="$formats"
            :statuses="$statuses"
            :filters="$filters"
        />
    </div>

    {{-- Results --}}
    @if($hasFilters)
        @if(count($anime))
            <div class="mb-4 text-left">
                <p class="text-sm text-dark-400">
                    Found <span class="font-semibold text-white">{{ $pageInfo['total'] ?? count($anime) }}</span> results
                    @if($filters['query'] ?? false)
                        for "<span class="text-primary-400 font-bold">{{ $filters['query'] }}</span>"
                    @endif
                </p>
            </div>
        @endif

        <div class="text-left">
            <x-anime-grid :anime="$anime" />
        </div>
        
        <div class="mt-8">
            <x-pagination :pageInfo="$pageInfo" :currentPage="$filters['page'] ?? 1" />
        </div>
    @else
        {{-- Empty State / Recommendations --}}
        <div class="text-center py-20 bg-dark-900/10 border border-dashed border-dark-800 rounded-3xl">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-dark-900 border border-dark-850 mb-6">
                <svg class="w-10 h-10 text-dark-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">Start Searching</h3>
            <p class="text-dark-400 max-w-md mx-auto mb-6">Enter a title or select filters above to search the global AniList database.</p>
        </div>
    @endif
</div>
@endsection
