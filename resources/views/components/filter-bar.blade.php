@props([
    'genres' => [],
    'seasons' => [],
    'formats' => [],
    'statuses' => [],
    'filters' => [],
])

<form method="GET" action="{{ route('search') }}" class="space-y-4">
    {{-- Search Input --}}
    <div class="relative">
        <input
            type="text"
            name="query"
            value="{{ $filters['query'] ?? '' }}"
            placeholder="Search anime by title..."
            class="w-full pl-12 pr-4 py-3.5 text-sm rounded-2xl bg-dark-800/60 light:bg-white border border-dark-700 light:border-dark-200 text-white light:text-dark-900 placeholder-dark-500 light:placeholder-dark-400 focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 transition-all"
        >
        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-dark-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
    </div>

    {{-- Filters Row --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
        {{-- Genre --}}
        <select name="genre" class="px-3 py-2.5 text-sm rounded-xl bg-dark-800/60 light:bg-white border border-dark-700 light:border-dark-200 text-white light:text-dark-900 focus:outline-none focus:ring-2 focus:ring-primary-500/50 transition-all">
            <option value="">All Genres</option>
            @foreach($genres as $genre)
                <option value="{{ $genre }}" {{ ($filters['genre'] ?? '') === $genre ? 'selected' : '' }}>{{ $genre }}</option>
            @endforeach
        </select>

        {{-- Season --}}
        <select name="season" class="px-3 py-2.5 text-sm rounded-xl bg-dark-800/60 light:bg-white border border-dark-700 light:border-dark-200 text-white light:text-dark-900 focus:outline-none focus:ring-2 focus:ring-primary-500/50 transition-all">
            <option value="">All Seasons</option>
            @foreach($seasons as $season)
                <option value="{{ $season }}" {{ strtoupper($filters['season'] ?? '') === $season ? 'selected' : '' }}>{{ ucfirst(strtolower($season)) }}</option>
            @endforeach
        </select>

        {{-- Year --}}
        <select name="year" class="px-3 py-2.5 text-sm rounded-xl bg-dark-800/60 light:bg-white border border-dark-700 light:border-dark-200 text-white light:text-dark-900 focus:outline-none focus:ring-2 focus:ring-primary-500/50 transition-all">
            <option value="">All Years</option>
            @for($y = (int)date('Y') + 1; $y >= 1940; $y--)
                <option value="{{ $y }}" {{ ($filters['year'] ?? '') == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>

        {{-- Format --}}
        <select name="format" class="px-3 py-2.5 text-sm rounded-xl bg-dark-800/60 light:bg-white border border-dark-700 light:border-dark-200 text-white light:text-dark-900 focus:outline-none focus:ring-2 focus:ring-primary-500/50 transition-all">
            <option value="">All Formats</option>
            @foreach($formats as $format)
                <option value="{{ $format }}" {{ strtoupper($filters['format'] ?? '') === $format ? 'selected' : '' }}>{{ \App\Services\AniListService::formatType($format) }}</option>
            @endforeach
        </select>

        {{-- Status --}}
        <select name="status" class="px-3 py-2.5 text-sm rounded-xl bg-dark-800/60 light:bg-white border border-dark-700 light:border-dark-200 text-white light:text-dark-900 focus:outline-none focus:ring-2 focus:ring-primary-500/50 transition-all">
            <option value="">All Statuses</option>
            @foreach($statuses as $status)
                <option value="{{ $status }}" {{ strtoupper($filters['status'] ?? '') === $status ? 'selected' : '' }}>{{ \App\Services\AniListService::formatStatus($status) }}</option>
            @endforeach
        </select>
    </div>

    {{-- Action Buttons --}}
    <div class="flex items-center gap-3">
        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-primary-600 to-primary-500 text-white text-sm font-semibold hover:from-primary-500 hover:to-primary-400 transition-all shadow-lg shadow-primary-600/20 hover:shadow-primary-500/30 active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            Search
        </button>
        <a href="{{ route('search') }}" class="px-4 py-2.5 rounded-xl text-sm font-medium text-dark-400 light:text-dark-500 hover:text-white light:hover:text-dark-900 hover:bg-dark-800 light:hover:bg-dark-100 transition-all">
            Clear
        </a>
    </div>
</form>
