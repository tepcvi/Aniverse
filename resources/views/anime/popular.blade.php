@extends('layouts.app')

@php $metaTitle = 'Popular Anime — AniVerse'; @endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-white light:text-dark-900">
            ⭐ Most Popular Anime
        </h1>
        <p class="text-dark-400 light:text-dark-500 mt-2">All-time most popular anime based on user engagement</p>
    </div>

    <x-anime-grid :anime="$anime" />
    <x-pagination :pageInfo="$pageInfo" :currentPage="$currentPage" />
</div>
@endsection
