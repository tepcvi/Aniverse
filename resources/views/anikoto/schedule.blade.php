@extends('layouts.app')

@php $metaTitle = 'Anikoto Schedule — AniVerse'; @endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    {{-- Header --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white light:text-dark-900">
                📅 Anikoto Release Schedule
            </h1>
            <p class="text-dark-400 light:text-dark-500 mt-2">See what episodes are releasing today and upcoming</p>
        </div>
        
        {{-- Date Picker Form --}}
        <form method="GET" action="{{ route('anikoto.schedule') }}" class="flex items-center gap-2">
            <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()"
                class="px-4 py-2 rounded-xl bg-dark-800/60 light:bg-white border border-dark-700 light:border-dark-200 text-white light:text-dark-900 focus:outline-none focus:ring-2 focus:ring-primary-500/50 transition-all">
        </form>
    </div>

    @if($error)
        <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
            {{ $error }}
        </div>
    @elseif(empty($schedule))
        <div class="text-center py-20 bg-dark-900/30 rounded-3xl border border-dark-800/50 light:bg-white light:border-dark-100">
            <span class="text-5xl mb-4 block">📭</span>
            <h3 class="text-xl font-bold text-white light:text-dark-900 mb-2">No Releases Scheduled</h3>
            <p class="text-dark-400 light:text-dark-500">There are no anime releases found for this date.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($schedule as $index => $item)
                @php
                    // Try to extract the slug from the url: e.g. "https://anikototv.to/watch/slug"
                    $slug = null;
                    if (!empty($item['url'])) {
                        $parts = explode('/watch/', $item['url']);
                        if (isset($parts[1])) {
                            $slug = $parts[1];
                        }
                    }
                @endphp
                <div class="p-5 rounded-2xl bg-dark-800/50 light:bg-white border border-dark-700/50 light:border-dark-200 hover:border-primary-500/30 hover:shadow-xl transition-all duration-300 animate-fade-in" style="animation-delay: {{ $index * 0.05 }}s; opacity: 0">
                    <div class="flex items-center justify-between gap-4 mb-3">
                        <span class="px-3 py-1 rounded-lg bg-primary-500/10 text-primary-400 text-xs font-semibold">
                            🕒 {{ $item['time'] ?? 'N/A' }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full bg-accent-500/10 text-accent-400 text-xs font-semibold">
                            {{ $item['episode'] ?? 'N/A' }}
                        </span>
                    </div>

                    <h3 class="text-lg font-bold text-white light:text-dark-900 mb-1 line-clamp-1">
                        {{ $item['title'] ?? 'Unknown Title' }}
                    </h3>
                    
                    @if(!empty($item['titleJapanese']))
                        <p class="text-xs text-dark-500 mb-4 line-clamp-1 italic">{{ $item['titleJapanese'] }}</p>
                    @else
                        <div class="h-4 mb-4"></div>
                    @endif

                    @if($slug)
                        <a href="{{ route('anikoto.show', $slug) }}"
                           class="inline-flex items-center gap-2 text-sm text-primary-400 hover:text-primary-300 font-semibold transition-colors">
                            View Details & Episodes
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @else
                        <span class="text-xs text-dark-500">Details unavailable</span>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
