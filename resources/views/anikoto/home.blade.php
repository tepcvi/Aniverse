@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Hero Section (Featured Anime) --}}
    @if(!empty($anime))
        @php
            $featured = $anime[0];
            $featuredTitle = $featured['title'] ?? $featured['alternative'] ?? 'Unknown';
            $featuredGenres = $featured['terms_by_type']['genre'] ?? [];
            $featuredStudios = $featured['terms_by_type']['studios'] ?? [];
            $featuredType = $featured['terms_by_type']['type'][0] ?? 'TV';
        @endphp
        <div class="relative rounded-3xl overflow-hidden mb-12 bg-dark-900 border border-dark-800 shadow-2xl min-h-[380px] flex items-center">
            {{-- Background banner fallback --}}
            <div class="absolute inset-0 bg-cover bg-center opacity-10" style="background-image: url('{{ $featured['background_image'] ?? $featured['poster'] }}')"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-dark-950 via-dark-950/90 to-transparent"></div>
            
            <div class="relative p-8 md:p-12 max-w-3xl flex flex-col md:flex-row gap-8 items-center z-10">
                <div class="w-40 shrink-0 rounded-2xl overflow-hidden shadow-2xl border border-dark-800 self-start md:self-center">
                    <img src="{{ $featured['poster'] }}" alt="{{ $featuredTitle }}" class="w-full h-auto object-cover">
                </div>
                <div class="flex-1 text-left">
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <span class="px-2.5 py-0.5 rounded bg-primary-600 text-white text-[10px] font-bold uppercase tracking-wider">{{ $featuredType }}</span>
                        @if(!empty($featured['year']))
                            <span class="text-xs text-dark-400 font-medium">{{ $featured['year'] }}</span>
                        @endif
                        @if(!empty($featured['score']))
                            <span class="text-xs text-yellow-500 font-bold">★ {{ $featured['score'] }}</span>
                        @endif
                    </div>
                    <h1 class="text-3xl md:text-4xl font-extrabold text-white leading-tight mb-3">
                        {{ $featuredTitle }}
                    </h1>
                    <p class="text-dark-400 text-sm line-clamp-3 mb-6 leading-relaxed">
                        {{ $featured['description'] ?? 'No description available.' }}
                    </p>
                    <div class="flex flex-wrap gap-4 items-center">
                        <a href="{{ route('anime.show', ['id' => $featured['id']]) }}" class="px-6 py-3 rounded-2xl bg-primary-600 hover:bg-primary-500 text-white text-sm font-bold shadow-lg shadow-primary-600/25 transition-all flex items-center gap-2">
                            <span>View Details</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                        @if(!empty($featured['episodes']))
                            <span class="text-xs text-dark-500 font-semibold">{{ $featured['episodes'] }} Episodes</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Main Grid Section --}}
    <div class="mb-10 text-left">
        <h2 class="text-2xl font-black text-white mb-6 flex items-center gap-3">
            <span class="w-2.5 h-6 rounded-full bg-primary-500"></span>
            <span>Recent Anime Releases</span>
        </h2>

        @if(!empty($anime))
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
                @foreach($anime as $item)
                    @php
                        $title = $item['title'] ?? $item['alternative'] ?? 'Unknown';
                        $type = $item['terms_by_type']['type'][0] ?? 'TV';
                    @endphp
                    <div class="group relative flex flex-col bg-dark-900/40 rounded-2xl border border-dark-800/80 overflow-hidden hover:border-primary-500/30 transition-all duration-300">
                        <a href="{{ route('anime.show', ['id' => $item['id']]) }}" class="block aspect-[3/4] overflow-hidden relative bg-dark-950">
                            <img src="{{ $item['poster'] }}" alt="{{ $title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                            
                            {{-- Tags Overlay --}}
                            <div class="absolute top-3 left-3 flex flex-col gap-1">
                                <span class="px-2 py-0.5 rounded bg-dark-950/80 backdrop-filter backdrop-blur text-[10px] font-bold text-primary-400 border border-dark-800">{{ $type }}</span>
                            </div>
                            
                            {{-- Score Badge --}}
                            @if(!empty($item['score']))
                                <div class="absolute bottom-3 right-3 px-2 py-0.5 rounded bg-yellow-500 text-dark-950 text-[10px] font-bold">
                                    ★ {{ $item['score'] }}
                                </div>
                            @endif
                        </a>
                        <div class="p-4 flex-1 flex flex-col justify-between text-left">
                            <div>
                                <h3 class="font-bold text-sm text-white line-clamp-2 hover:text-primary-400 transition-colors mb-1">
                                    <a href="{{ route('anime.show', ['id' => $item['id']]) }}">{{ $title }}</a>
                                </h3>
                                @if(!empty($item['alternative']))
                                    <p class="text-[11px] text-dark-500 line-clamp-1 mb-2">{{ $item['alternative'] }}</p>
                                @endif
                            </div>
                            <div class="flex items-center justify-between text-[11px] text-dark-400 mt-2 pt-2 border-t border-dark-800/50">
                                <span>{{ $item['year'] ?? '' }} {{ $item['season'] ?? '' }}</span>
                                @if(!empty($item['episodes']))
                                    <span class="font-bold text-primary-400">{{ $item['episodes'] }} Eps</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Custom Pagination --}}
            @if(isset($pagination) && $pagination['total_pages'] > 1)
                <div class="mt-12 flex items-center justify-center gap-4">
                    @if($pagination['page'] > 1)
                        <a href="{{ route('home', ['page' => $pagination['page'] - 1]) }}" class="px-5 py-2.5 rounded-xl bg-dark-900 hover:bg-dark-800 text-white font-semibold text-xs border border-dark-800 transition-colors">
                            ◀ Previous Page
                        </a>
                    @endif
                    
                    <span class="text-xs text-dark-400 font-bold uppercase tracking-wider">
                        Page {{ $pagination['page'] }} of {{ $pagination['total_pages'] }}
                    </span>

                    @if($pagination['page'] < $pagination['total_pages'])
                        <a href="{{ route('home', ['page' => $pagination['page'] + 1]) }}" class="px-5 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-500 text-white font-semibold text-xs transition-colors shadow-lg shadow-primary-600/15">
                            Next Page ▶
                        </a>
                    @endif
                </div>
            @endif

        @else
            <div class="p-12 text-center rounded-3xl bg-dark-900/30 border border-dark-800/80">
                <p class="text-sm text-dark-400">No recent anime found or the streaming service is offline.</p>
            </div>
        @endif
    </div>
</div>
@endsection
