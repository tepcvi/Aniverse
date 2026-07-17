<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $metaTitle ?? 'AniVerse — Discover Anime' }}</title>
    <meta name="description" content="{{ $metaDescription ?? 'AniVerse is a modern anime catalog. Browse trending, popular, and top-rated anime powered by AniList.' }}">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph --}}
    <meta property="og:title" content="{{ $metaTitle ?? 'AniVerse — Discover Anime' }}">
    <meta property="og:description" content="{{ $metaDescription ?? 'AniVerse is a modern anime catalog. Browse trending, popular, and top-rated anime powered by AniList.' }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @if(!empty($metaImage))
    <meta property="og:image" content="{{ $metaImage }}">
    @endif
    <meta property="og:site_name" content="AniVerse">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle ?? 'AniVerse — Discover Anime' }}">
    <meta name="twitter:description" content="{{ $metaDescription ?? 'Discover trending, popular, and top-rated anime on AniVerse.' }}">

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
    <footer class="border-t border-dark-800 light:border-dark-200 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                {{-- Brand --}}
                <div class="md:col-span-2">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-2xl font-bold">
                        <span class="text-gradient">Ani</span><span class="text-white light:text-dark-900">Verse</span>
                    </a>
                    <p class="mt-3 text-dark-400 light:text-dark-500 text-sm max-w-md">
                        Your gateway to the world of anime. Discover trending shows, explore genres, and find your next favorite series — all powered by AniList.
                    </p>
                </div>

                {{-- Browse --}}
                <div>
                    <h4 class="font-semibold text-sm uppercase tracking-wider text-dark-300 light:text-dark-600 mb-4">Browse</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('anime.trending') }}" class="text-dark-400 light:text-dark-500 hover:text-primary-400 transition-colors">Trending</a></li>
                        <li><a href="{{ route('anime.popular') }}" class="text-dark-400 light:text-dark-500 hover:text-primary-400 transition-colors">Popular</a></li>
                        <li><a href="{{ route('anime.top-rated') }}" class="text-dark-400 light:text-dark-500 hover:text-primary-400 transition-colors">Top Rated</a></li>
                        <li><a href="{{ route('anime.seasonal') }}" class="text-dark-400 light:text-dark-500 hover:text-primary-400 transition-colors">Seasonal</a></li>
                        <li><a href="{{ route('genres.index') }}" class="text-dark-400 light:text-dark-500 hover:text-primary-400 transition-colors">Genres</a></li>
                    </ul>
                </div>

                {{-- Info --}}
                <div>
                    <h4 class="font-semibold text-sm uppercase tracking-wider text-dark-300 light:text-dark-600 mb-4">Info</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('about') }}" class="text-dark-400 light:text-dark-500 hover:text-primary-400 transition-colors">About</a></li>
                        <li><a href="https://anilist.co" target="_blank" rel="noopener" class="text-dark-400 light:text-dark-500 hover:text-primary-400 transition-colors">AniList ↗</a></li>
                    </ul>
                </div>
            </div>

            <div class="mt-10 pt-8 border-t border-dark-800/50 light:border-dark-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-dark-500 light:text-dark-400 text-xs">
                    &copy; {{ date('Y') }} AniVerse. Data provided by <a href="https://anilist.co" target="_blank" rel="noopener" class="text-primary-400 hover:underline">AniList</a>.
                </p>
                <p class="text-dark-600 light:text-dark-300 text-xs">
                    Built with Laravel &amp; Tailwind CSS
                </p>
            </div>
        </div>
    </footer>

    {{-- Toast Container --}}
    <div id="toast-container" class="fixed bottom-6 right-6 z-50 flex flex-col gap-3 max-w-sm"></div>

</body>
</html>
