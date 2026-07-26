<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $metaTitle ?? 'Anitep — Discover Anime' }}</title>
    <meta name="description" content="{{ $metaDescription ?? 'Anitep is a modern anime catalog. Browse trending, popular, and top-rated anime powered by AniList.' }}">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}">

    {{-- Open Graph --}}
    <meta property="og:title" content="{{ $metaTitle ?? 'Anitep — Discover Anime' }}">
    <meta property="og:description" content="{{ $metaDescription ?? 'Anitep is a modern anime catalog. Browse trending, popular, and top-rated anime powered by AniList.' }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @if(!empty($metaImage))
    <meta property="og:image" content="{{ $metaImage }}">
    @endif
    <meta property="og:site_name" content="Anitep">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle ?? 'Anitep — Discover Anime' }}">
    <meta name="twitter:description" content="{{ $metaDescription ?? 'Discover trending, popular, and top-rated anime on Anitep.' }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-dark-950 text-white dark:bg-dark-950 dark:text-white light:bg-dark-50 light:text-dark-900 transition-colors duration-300">

    {{-- Navigation --}}
    @include('layouts.navigation')

    {{-- Main Content --}}
    <main class="min-h-screen">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="border-t border-dark-800 light:border-dark-200 mt-16 bg-dark-950/80 light:bg-dark-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-8">
                {{-- Brand --}}
                <div class="col-span-2 sm:col-span-3 md:col-span-1">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xl font-bold mb-3">
                        <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-primary-500 to-accent-500 flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L2 19h20L12 2zm0 4l7 13H5l7-13z"/>
                            </svg>
                        </div>
                        <span class="text-gradient">Ani</span><span class="text-white light:text-dark-900">tep</span>
                    </a>
                    <p class="text-dark-500 light:text-dark-400 text-xs leading-relaxed max-w-[200px]">
                        Your ultimate destination for anime streaming. Watch thousands of series and movies in HD, sub and dub.
                    </p>
                </div>

                {{-- Explore --}}
                <div>
                    <h4 class="font-bold text-xs uppercase tracking-wider text-dark-300 light:text-dark-600 mb-4">Explore</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('home') }}" class="text-dark-400 light:text-dark-500 hover:text-primary-400 transition-colors">Home</a></li>
                        <li><a href="{{ route('anime.trending') }}" class="text-dark-400 light:text-dark-500 hover:text-primary-400 transition-colors">Trending</a></li>
                        <li><a href="{{ route('anime.popular') }}" class="text-dark-400 light:text-dark-500 hover:text-primary-400 transition-colors">Most Popular</a></li>
                        <li><a href="{{ route('anime.latest') }}" class="text-dark-400 light:text-dark-500 hover:text-primary-400 transition-colors">Latest Updates</a></li>
                        <li><a href="{{ route('anikoto.schedule') }}" class="text-dark-400 light:text-dark-500 hover:text-primary-400 transition-colors">Airing Schedule</a></li>
                    </ul>
                </div>

                {{-- Browse --}}
                <div>
                    <h4 class="font-bold text-xs uppercase tracking-wider text-dark-300 light:text-dark-600 mb-4">Browse</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('anime.top-rated') }}" class="text-dark-400 light:text-dark-500 hover:text-primary-400 transition-colors">Top Rated</a></li>
                        <li><a href="{{ route('anime.seasonal') }}" class="text-dark-400 light:text-dark-500 hover:text-primary-400 transition-colors">Seasonal</a></li>
                        <li><a href="{{ route('search') }}" class="text-dark-400 light:text-dark-500 hover:text-primary-400 transition-colors">Search</a></li>
                    </ul>
                </div>

                {{-- Genres --}}
                <div>
                    <h4 class="font-bold text-xs uppercase tracking-wider text-dark-300 light:text-dark-600 mb-4">Genres</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('genres.show', 'Action') }}" class="text-dark-400 light:text-dark-500 hover:text-primary-400 transition-colors">Action</a></li>
                        <li><a href="{{ route('genres.show', 'Adventure') }}" class="text-dark-400 light:text-dark-500 hover:text-primary-400 transition-colors">Adventure</a></li>
                        <li><a href="{{ route('genres.show', 'Comedy') }}" class="text-dark-400 light:text-dark-500 hover:text-primary-400 transition-colors">Comedy</a></li>
                        <li><a href="{{ route('genres.show', 'Drama') }}" class="text-dark-400 light:text-dark-500 hover:text-primary-400 transition-colors">Drama</a></li>
                        <li><a href="{{ route('genres.index') }}" class="text-primary-400 hover:text-primary-300 transition-colors font-medium">All genres →</a></li>
                    </ul>
                </div>

                {{-- Info --}}
                <div>
                    <h4 class="font-bold text-xs uppercase tracking-wider text-dark-300 light:text-dark-600 mb-4">Info</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('about') }}" class="text-dark-400 light:text-dark-500 hover:text-primary-400 transition-colors">About</a></li>
                        <li><a href="https://anilist.co" target="_blank" rel="noopener" class="text-dark-400 light:text-dark-500 hover:text-primary-400 transition-colors">AniList ↗</a></li>
                    </ul>
                </div>
            </div>

            {{-- Bottom Bar --}}
            <div class="mt-10 pt-6 border-t border-dark-800/50 light:border-dark-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-dark-500 light:text-dark-400 text-xs">
                    &copy; {{ date('Y') }} Anitep. Watch anime online free in HD.
                </p>
                <p class="text-dark-600 light:text-dark-300 text-xs">
                    Created with <span class="text-red-500">&hearts;</span> by <span class="text-primary-400 font-medium">Tep</span>
                </p>
            </div>
        </div>
    </footer>

    {{-- Toast Container --}}
    <div id="toast-container" class="fixed bottom-6 right-6 z-50 flex flex-col gap-3 max-w-sm"></div>

</body>
</html>
