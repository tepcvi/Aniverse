@extends('layouts.app')

@php $metaTitle = ucfirst(strtolower($currentSeason)) . " {$currentYear} Anime — AniVerse"; @endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-white light:text-dark-900">
            🌸 Seasonal Anime
        </h1>
        <p class="text-dark-400 light:text-dark-500 mt-2">Browse anime by season and year</p>
    </div>

    {{-- Season/Year Selector --}}
    <div class="flex flex-wrap items-center gap-3 mb-8">
        {{-- Season Buttons --}}
        <div class="flex rounded-xl overflow-hidden border border-dark-700 light:border-dark-200">
            @foreach($seasons as $s)
                <a href="{{ route('anime.seasonal', ['season' => $s, 'year' => $currentYear]) }}"
                    class="px-4 py-2 text-sm font-medium transition-colors {{ $currentSeason === $s ? 'bg-primary-600 text-white' : 'bg-dark-800/60 light:bg-white text-dark-400 light:text-dark-500 hover:text-white light:hover:text-dark-900 hover:bg-dark-700 light:hover:bg-dark-50' }}">
                    @php
                        $seasonIcons = ['WINTER' => '❄️', 'SPRING' => '🌸', 'SUMMER' => '☀️', 'FALL' => '🍂'];
                    @endphp
                    {{ $seasonIcons[$s] ?? '' }} {{ ucfirst(strtolower($s)) }}
                </a>
            @endforeach
        </div>

        {{-- Year Select --}}
        <select onchange="window.location.href='{{ route('anime.seasonal') }}?season={{ $currentSeason }}&year=' + this.value" class="px-4 py-2 text-sm rounded-xl bg-dark-800/60 light:bg-white border border-dark-700 light:border-dark-200 text-white light:text-dark-900 focus:outline-none focus:ring-2 focus:ring-primary-500/50 transition-all">
            @for($y = (int)date('Y') + 1; $y >= 2000; $y--)
                <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
    </div>

    {{-- Grid --}}
    <x-anime-grid :anime="$anime" />
    <x-pagination :pageInfo="$pageInfo" :currentPage="$currentPage" />
</div>
@endsection
