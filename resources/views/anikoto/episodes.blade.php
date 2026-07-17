@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 animate-fade-in">
    {{-- Breadcrumb --}}
    <div class="mb-6">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm text-dark-400 hover:text-primary-400 font-semibold transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Home
        </a>
    </div>

    {{-- Detail Card --}}
    <div class="p-6 sm:p-8 rounded-3xl bg-dark-900/50 light:bg-white border border-dark-800 light:border-dark-200 shadow-2xl mb-8 flex flex-col md:flex-row gap-8">
        @if(!empty($info['poster']))
            <div class="w-full md:w-64 shrink-0 rounded-2xl overflow-hidden shadow-xl border border-dark-800 light:border-dark-100">
                <img src="{{ $info['poster'] }}" alt="{{ $info['title'] ?? 'Poster' }}" class="w-full object-cover">
            </div>
        @endif

        <div class="flex-grow">
            <div class="flex flex-wrap items-center gap-2 mb-4">
                @if(!empty($info['type']))
                    <span class="px-3 py-1 rounded-lg bg-primary-500/10 text-primary-400 text-xs font-semibold uppercase">{{ $info['type'] }}</span>
                @endif
                @if(!empty($info['status']))
                    <span class="px-3 py-1 rounded-lg bg-emerald-500/10 text-emerald-400 text-xs font-semibold uppercase">{{ $info['status'] }}</span>
                @endif
                @if(!empty($info['rating']))
                    <span class="px-3 py-1 rounded-lg bg-yellow-500/10 text-yellow-400 text-xs font-semibold">⭐ {{ $info['rating'] }}</span>
                @endif
            </div>

            <h1 class="text-3xl sm:text-4xl font-extrabold text-white light:text-dark-900 mb-2">
                {{ $info['title'] ?? 'Unknown' }}
            </h1>
            
            @if(!empty($info['titleJapanese']))
                <p class="text-sm text-dark-400 mb-4 italic">{{ $info['titleJapanese'] }}</p>
            @endif

            <h3 class="text-lg font-bold text-white light:text-dark-900 mb-2">Synopsis</h3>
            <p class="text-dark-300 light:text-dark-600 text-sm sm:text-base leading-relaxed">
                {{ $info['synopsis'] ?? 'No synopsis available.' }}
            </p>
        </div>
    </div>

    {{-- Episodes list --}}
    <div>
        <h2 class="text-2xl font-bold text-white light:text-dark-900 mb-6">📺 Available Episodes</h2>

        @if(empty($episodes))
            <div class="p-8 rounded-3xl bg-dark-900/30 border border-dark-800/50 text-center text-dark-400">
                No episodes found for this series.
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($episodes as $index => $ep)
                    <a href="{{ route('watch', ['slug' => $slug, 'episode' => $ep['num']]) }}"
                       class="p-4 rounded-2xl bg-dark-800/30 hover:bg-primary-600/10 border border-dark-800/60 hover:border-primary-500/30 transition-all duration-200 group flex flex-col justify-between h-32 animate-fade-in"
                       style="animation-delay: {{ $index * 0.02 }}s; opacity: 0">
                        <div>
                            <span class="text-primary-400 font-extrabold text-xs block mb-1">EPISODE {{ $ep['num'] }}</span>
                            <h4 class="text-white light:text-dark-900 font-bold text-sm line-clamp-2 group-hover:text-primary-400 transition-colors">
                                {{ $ep['title'] }}
                            </h4>
                        </div>
                        <div class="text-xs text-primary-400 font-semibold text-right">
                            Watch →
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
