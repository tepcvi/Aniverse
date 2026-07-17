<!DOCTYPE html>
<html lang="en" class="h-full bg-black">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player Frame</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full m-0 overflow-hidden flex items-center justify-center bg-black">
    <div class="relative w-full h-full" x-data="{ loaded: false, error: false }">
        {{-- Loader Shimmer Skeleton --}}
        <div x-show="!loaded && !error" class="absolute inset-0 flex flex-col items-center justify-center bg-dark-950 text-white z-50">
            <div class="w-12 h-12 rounded-full border-4 border-primary-500/20 border-t-primary-500 animate-spin mb-4"></div>
            <p class="text-sm font-semibold tracking-wider text-dark-400">LOADING STREAM MEDIA...</p>
        </div>

        {{-- Error Container --}}
        <div x-show="error" class="absolute inset-0 flex flex-col items-center justify-center bg-dark-950 text-white z-50 p-6 text-center">
            <span class="text-4xl mb-4">⚠️</span>
            <h4 class="text-lg font-bold mb-2">Failed to Load Video Server</h4>
            <p class="text-sm text-dark-400 max-w-md mb-6">This streaming server returned an error or took too long to respond. Try switching servers in the selector bar.</p>
        </div>

        {{-- Active Player Iframe --}}
        @if(!empty($activeServer['url']))
            <iframe 
                src="{{ $activeServer['url'] }}"
                class="w-full h-full border-0 absolute inset-0"
                allowfullscreen
                scrolling="no"
                allow="autoplay; encrypted-media; picture-in-picture"
                x-on:load="loaded = true"
                x-on:error="error = true"
            ></iframe>
        @else
            <div class="absolute inset-0 flex flex-col items-center justify-center bg-dark-950 text-white z-50 text-center">
                <span class="text-4xl mb-4">🚫</span>
                <h4 class="text-lg font-bold mb-2">Server Offline</h4>
                <p class="text-sm text-dark-400">No source URLs are returned for this video provider.</p>
            </div>
        @endif
    </div>
</body>
</html>
