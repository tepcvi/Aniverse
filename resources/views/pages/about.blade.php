@extends('layouts.app')

@php
    $metaTitle = 'About — AniVerse';
    $metaDescription = 'AniVerse is a modern anime discovery platform powered by the AniList API. Built with Laravel and Tailwind CSS.';
@endphp

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    {{-- Hero --}}
    <div class="text-center mb-16 animate-fade-in">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-br from-primary-500 to-accent-500 mb-6 shadow-2xl shadow-primary-600/30">
            <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2L2 19h20L12 2zm0 4l7 13H5l7-13z"/>
            </svg>
        </div>
        <h1 class="text-4xl sm:text-5xl font-extrabold text-white light:text-dark-900 mb-4">
            About <span class="text-gradient">AniVerse</span>
        </h1>
        <p class="text-lg text-dark-400 light:text-dark-500 max-w-2xl mx-auto leading-relaxed">
            Your gateway to the world of anime. Discover trending shows, explore genres, and find your next favorite series.
        </p>
    </div>

    {{-- Features --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-16">
        @php
            $features = [
                ['icon' => '🔥', 'title' => 'Trending & Popular', 'desc' => 'Stay up to date with the hottest anime trending right now and the most popular series of all time.'],
                ['icon' => '🔍', 'title' => 'Advanced Search', 'desc' => 'Find anime by title, genre, season, year, format, and status with our powerful search filters.'],
                ['icon' => '🌸', 'title' => 'Seasonal Browse', 'desc' => 'Browse anime by season to discover what\'s airing each quarter of the year.'],
                ['icon' => '🎭', 'title' => 'Genre Discovery', 'desc' => 'Explore anime across 20+ genres from Action to Slice of Life and everything in between.'],
                ['icon' => '📊', 'title' => 'Detailed Info', 'desc' => 'Get comprehensive details including characters, voice actors, relations, trailers, and more.'],
                ['icon' => '🌙', 'title' => 'Dark & Light Modes', 'desc' => 'Switch between dark and light themes for comfortable browsing at any time of day.'],
            ];
        @endphp

        @foreach($features as $i => $f)
            <div class="p-6 rounded-2xl bg-dark-800/50 light:bg-white border border-dark-700/50 light:border-dark-200 hover:border-primary-500/30 transition-all animate-fade-in" style="animation-delay: {{ $i * 0.1 }}s; opacity: 0">
                <span class="text-3xl mb-4 block">{{ $f['icon'] }}</span>
                <h3 class="text-lg font-bold text-white light:text-dark-900 mb-2">{{ $f['title'] }}</h3>
                <p class="text-sm text-dark-400 light:text-dark-500 leading-relaxed">{{ $f['desc'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Tech Stack --}}
    <div class="text-center mb-16 animate-fade-in" style="animation-delay: 0.3s; opacity: 0">
        <h2 class="text-2xl font-bold text-white light:text-dark-900 mb-6">Built With</h2>
        <div class="flex flex-wrap items-center justify-center gap-4">
            @foreach(['Laravel', 'PHP 8.3+', 'Tailwind CSS', 'AniList API', 'GraphQL', 'Vite'] as $tech)
                <span class="px-4 py-2 rounded-xl bg-dark-800 light:bg-dark-100 text-dark-300 light:text-dark-600 text-sm font-medium border border-dark-700/50 light:border-dark-200">{{ $tech }}</span>
            @endforeach
        </div>
    </div>

    {{-- Data Source --}}
    <div class="text-center p-8 rounded-2xl bg-gradient-to-br from-primary-950 to-dark-900 light:from-primary-50 light:to-white border border-primary-800/30 light:border-primary-200 animate-fade-in" style="animation-delay: 0.4s; opacity: 0">
        <h2 class="text-xl font-bold text-white light:text-dark-900 mb-3">Data Source</h2>
        <p class="text-dark-400 light:text-dark-500 mb-4">All anime data is provided by the AniList API — a free, comprehensive anime and manga database.</p>
        <a href="https://anilist.co" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-semibold hover:bg-primary-500 transition-colors">
            Visit AniList
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        </a>
    </div>
</div>
@endsection
