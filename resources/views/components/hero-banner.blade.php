@props(['featured' => []])

@if(count($featured))
<section class="relative h-[70vh] min-h-[500px] max-h-[700px] overflow-hidden">
    {{-- Slides --}}
    @foreach($featured as $index => $anime)
        @php
            $banner = $anime['bannerImage'] ?? $anime['coverImage']['extraLarge'] ?? '';
            $title = $anime['title']['english'] ?? $anime['title']['romaji'] ?? 'Unknown';
            $description = \Illuminate\Support\Str::limit(strip_tags($anime['description'] ?? ''), 200);
            $studio = $anime['studios']['nodes'][0]['name'] ?? '';
            $genres = array_slice($anime['genres'] ?? [], 0, 4);
        @endphp
        <div data-hero-slide class="absolute inset-0 transition-opacity duration-1000 {{ $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }}">
            {{-- Background Image --}}
            <div class="absolute inset-0">
                <img src="{{ $banner }}" alt="{{ $title }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-r from-dark-950 via-dark-950/80 to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-dark-950 via-transparent to-dark-950/30"></div>
            </div>

            {{-- Content --}}
            <div class="relative z-10 h-full flex items-center">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                    <div class="max-w-2xl">
                        {{-- Studio --}}
                        @if($studio)
                            <span class="inline-block px-3 py-1 text-xs font-semibold uppercase tracking-wider rounded-full bg-primary-500/20 text-primary-300 border border-primary-500/30 mb-4">{{ $studio }}</span>
                        @endif

                        {{-- Title --}}
                        <h2 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-4">
                            {{ $title }}
                        </h2>

                        {{-- Meta --}}
                        <div class="flex items-center gap-4 mb-4">
                            @if($anime['averageScore'] ?? null)
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                    <span class="text-lg font-bold text-white">{{ $anime['averageScore'] }}%</span>
                                </div>
                            @endif
                            @if($anime['episodes'] ?? null)
                                <span class="text-dark-400">{{ $anime['episodes'] }} Episodes</span>
                            @endif
                            @if($anime['format'] ?? null)
                                <span class="text-dark-400">{{ \App\Services\AniListService::formatType($anime['format']) }}</span>
                            @endif
                        </div>

                        {{-- Genres --}}
                        @if(count($genres))
                            <div class="flex flex-wrap gap-2 mb-5">
                                @foreach($genres as $genre)
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-dark-800/60 text-dark-200 border border-dark-700/50">{{ $genre }}</span>
                                @endforeach
                            </div>
                        @endif

                        {{-- Description --}}
                        @if($description)
                            <p class="text-dark-300 text-sm leading-relaxed mb-6 line-clamp-3">{{ $description }}</p>
                        @endif

                        {{-- CTA --}}
                        <a href="{{ route('anime.show', $anime['id']) }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-primary-600 to-primary-500 text-white font-semibold hover:from-primary-500 hover:to-primary-400 transition-all shadow-lg shadow-primary-600/25 hover:shadow-primary-500/40 hover:scale-105 active:scale-95">
                            <span>View Details</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Dots Navigation --}}
    @if(count($featured) > 1)
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-20 flex items-center gap-2">
            @foreach($featured as $index => $anime)
                <button data-hero-dot class="h-3 rounded-full transition-all duration-300 {{ $index === 0 ? 'bg-white w-8' : 'bg-white/40 w-3' }} hover:bg-white/70" aria-label="Go to slide {{ $index + 1 }}"></button>
            @endforeach
        </div>
    @endif
</section>
@endif
