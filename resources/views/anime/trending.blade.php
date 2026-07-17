@extends('layouts.app')

@php $metaTitle = 'Trending Anime — AniVerse'; @endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-white light:text-dark-900">
            🔥 Trending Anime
        </h1>
        <p class="text-dark-400 light:text-dark-500 mt-2">The hottest anime right now based on user activity</p>
    </div>

    {{-- Grid --}}
    <x-anime-grid :anime="$anime" />

    {{-- Pagination --}}
    <x-pagination :pageInfo="$pageInfo" :currentPage="$currentPage" />
</div>
@endsection
