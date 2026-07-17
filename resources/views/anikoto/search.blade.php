@extends('layouts.app')

@php $metaTitle = 'Stream Search — AniVerse'; @endphp

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="text-center mb-12">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-white light:text-dark-900 mb-4">
            🎥 Anikoto Streaming Search
        </h1>
        <p class="text-dark-400 light:text-dark-500">Look up any anime slug to access direct streaming episodes</p>
    </div>

    {{-- Search Bar --}}
    <form method="GET" action="{{ route('anikoto.search') }}" class="mb-10">
        <div class="relative flex items-center">
            <input type="text" name="q" value="{{ $query }}" placeholder="Enter anime slug (e.g. 'one-piece-odmau', 'solo-leveling-j4o8p')..."
                class="w-full pl-6 pr-24 py-4 rounded-2xl bg-dark-800/80 light:bg-white border border-dark-700 light:border-dark-200 text-white light:text-dark-900 placeholder-dark-500 focus:outline-none focus:ring-2 focus:ring-primary-500/50 transition-all text-base sm:text-lg">
            <button type="submit" class="absolute right-3 px-6 py-2 rounded-xl bg-primary-600 hover:bg-primary-500 text-white text-sm font-semibold transition-all">
                Search
            </button>
        </div>
    </form>

    {{-- Error state --}}
    @if($error)
        <div class="p-6 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm mb-6 flex items-start gap-3">
            <span class="text-xl">⚠️</span>
            <div>
                <h4 class="font-bold text-white mb-1">Search Error</h4>
                <p>{{ $error }}</p>
            </div>
        </div>
    @endif

    {{-- Results --}}
    @if($result)
        <div class="p-6 rounded-3xl bg-dark-800/40 light:bg-white border border-dark-700/50 light:border-dark-200 shadow-2xl flex flex-col sm:flex-row gap-6 animate-fade-in">
            @if(!empty($result['poster']))
                <div class="w-full sm:w-48 shrink-0 rounded-2xl overflow-hidden shadow-lg border border-dark-700/30">
                    <img src="{{ $result['poster'] }}" alt="{{ $result['title'] ?? 'Poster' }}" class="w-full h-64 sm:h-full object-cover">
                </div>
            @endif

            <div class="flex-grow">
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    @if(!empty($result['type']))
                        <span class="px-2.5 py-0.5 rounded-full bg-primary-500/10 text-primary-400 text-xs font-semibold">{{ $result['type'] }}</span>
                    @endif
                    @if(!empty($result['status']))
                        <span class="px-2.5 py-0.5 rounded-full bg-accent-500/10 text-accent-400 text-xs font-semibold">{{ $result['status'] }}</span>
                    @endif
                </div>

                <h2 class="text-2xl font-extrabold text-white light:text-dark-900 mb-2">
                    {{ $result['title'] ?? 'Unknown' }}
                </h2>
                
                @if(!empty($result['titleJapanese']))
                    <p class="text-sm text-dark-400 mb-4">{{ $result['titleJapanese'] }}</p>
                @endif

                <p class="text-sm text-dark-300 light:text-dark-600 mb-6 line-clamp-3 leading-relaxed">
                    {{ $result['synopsis'] ?? 'No synopsis available.' }}
                </p>

                <a href="{{ route('anikoto.show', $query) }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-semibold hover:bg-primary-500 transition-colors shadow-lg shadow-primary-600/20">
                    📂 Browse Episodes
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>
    @elseif(!$error && !empty($query))
        <div class="text-center py-16 bg-dark-900/30 rounded-3xl border border-dark-800/50">
            <span class="text-5xl mb-4 block">🔍</span>
            <h3 class="text-xl font-bold text-white mb-2">No Match Found</h3>
            <p class="text-dark-400 max-w-md mx-auto">Double check the slug format. It should exactly match the title slug on anikototv.to watch page.</p>
        </div>
    @endif
</div>
@endsection
