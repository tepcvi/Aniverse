@extends('layouts.app')

@php $metaTitle = "{$genre} Anime — AniVerse"; @endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('genres.index') }}" class="text-dark-500 hover:text-primary-400 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white light:text-dark-900">{{ $genre }}</h1>
        </div>
        <p class="text-dark-400 light:text-dark-500">Discover the best {{ strtolower($genre) }} anime</p>
    </div>

    <x-anime-grid :anime="$anime" />
    <x-pagination :pageInfo="$pageInfo" :currentPage="$currentPage" />
</div>
@endsection
