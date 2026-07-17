@extends('layouts.app')

@php $metaTitle = 'Anime Genres — AniVerse'; @endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-white light:text-dark-900">
            🎭 Genres
        </h1>
        <p class="text-dark-400 light:text-dark-500 mt-2">Browse anime by genre</p>
    </div>

    @php
        $genreColors = [
            'Action' => 'from-red-500 to-orange-500',
            'Adventure' => 'from-emerald-500 to-teal-500',
            'Comedy' => 'from-amber-400 to-yellow-500',
            'Drama' => 'from-purple-500 to-violet-500',
            'Ecchi' => 'from-pink-400 to-rose-500',
            'Fantasy' => 'from-indigo-500 to-purple-500',
            'Horror' => 'from-gray-700 to-red-900',
            'Mahou Shoujo' => 'from-pink-400 to-purple-400',
            'Mecha' => 'from-slate-500 to-zinc-600',
            'Music' => 'from-cyan-400 to-blue-500',
            'Mystery' => 'from-violet-600 to-indigo-700',
            'Psychological' => 'from-fuchsia-600 to-purple-700',
            'Romance' => 'from-rose-400 to-pink-500',
            'Sci-Fi' => 'from-cyan-500 to-blue-600',
            'Slice of Life' => 'from-green-400 to-emerald-500',
            'Sports' => 'from-orange-500 to-red-500',
            'Supernatural' => 'from-purple-500 to-indigo-600',
            'Thriller' => 'from-gray-600 to-red-800',
        ];
    @endphp

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
        @foreach($genres as $index => $genre)
            @php
                $gradient = $genreColors[$genre] ?? 'from-primary-500 to-primary-700';
            @endphp
            <a href="{{ route('genres.show', $genre) }}"
               class="group relative overflow-hidden rounded-2xl p-6 bg-gradient-to-br {{ $gradient }} shadow-lg hover:shadow-2xl transition-all duration-300 hover:scale-105 animate-fade-in"
               style="animation-delay: {{ $index * 0.03 }}s; opacity: 0">
                <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition-colors"></div>
                <div class="absolute -bottom-4 -right-4 w-20 h-20 rounded-full bg-white/10 group-hover:scale-150 transition-transform duration-500"></div>
                <span class="relative text-sm sm:text-base font-bold text-white drop-shadow-sm">{{ $genre }}</span>
            </a>
        @endforeach
    </div>
</div>
@endsection
