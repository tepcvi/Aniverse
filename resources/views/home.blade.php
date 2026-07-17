@extends('layouts.app')

@section('content')
    {{-- Hero Banner --}}
    <x-hero-banner :featured="$featured" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-16">

        {{-- Trending Anime --}}
        <section data-animate>
            <x-section-header title="🔥 Trending Now" :link="route('anime.trending')" />
            <x-anime-grid :anime="$trending" />
        </section>

        {{-- Popular Anime --}}
        <section data-animate>
            <x-section-header title="⭐ Most Popular" :link="route('anime.popular')" />
            <x-anime-grid :anime="$popular" />
        </section>

        {{-- Top Rated --}}
        <section data-animate>
            <x-section-header title="🏆 Top Rated" :link="route('anime.top-rated')" />
            <x-anime-grid :anime="$topRated" />
        </section>

        {{-- Recently Released --}}
        <section data-animate>
            <x-section-header title="📺 Recently Released" :link="route('anime.seasonal')" linkText="View Seasonal" />
            <x-anime-grid :anime="$recent" />
        </section>

    </div>
@endsection
