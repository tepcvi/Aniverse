@props(['anime'])

@php
    $title = $anime['title']['english'] ?? $anime['title']['romaji'] ?? 'Unknown';
    $romaji = $anime['title']['romaji'] ?? '';
    $score = $anime['averageScore'] ?? null;
    $episodes = $anime['episodes'] ?? '?';
    $status = $anime['status'] ?? '';
    $format = $anime['format'] ?? '';
    $year = $anime['seasonYear'] ?? $anime['startDate']['year'] ?? '';
    $genres = array_slice($anime['genres'] ?? [], 0, 3);
    $cover = $anime['coverImage']['large'] ?? $anime['coverImage']['medium'] ?? '';
    $color = $anime['coverImage']['color'] ?? '#6366f1';
@endphp

<a href="{{ route('anime.show', $anime['id']) }}" class="group block card-hover">
    <div class="relative rounded-xl overflow-hidden bg-dark-800 light:bg-white shadow-lg">
        {{-- Cover Image --}}
        <div class="aspect-[3/4] overflow-hidden">
            <img
                src="{{ $cover }}"
                alt="{{ $title }}"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                loading="lazy"
            >
            {{-- Gradient Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-t from-dark-900 via-dark-900/20 to-transparent opacity-60 group-hover:opacity-80 transition-opacity"></div>

            {{-- Score Badge --}}
            @if($score)
                <x-score-badge :score="$score" class="absolute top-3 left-3" />
            @endif

            {{-- Format Badge --}}
            @if($format)
                <span class="absolute top-3 right-3 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-md bg-dark-900/70 text-dark-200 backdrop-blur-sm">
                    {{ \App\Services\AniListService::formatType($format) }}
                </span>
            @endif

            {{-- Hover Info --}}
            <div class="absolute bottom-0 left-0 right-0 p-4 translate-y-2 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300">
                {{-- Genres --}}
                @if(count($genres))
                    <div class="flex flex-wrap gap-1 mb-2">
                        @foreach($genres as $genre)
                            <span class="px-2 py-0.5 text-[10px] font-medium rounded-full bg-primary-500/20 text-primary-300 border border-primary-500/20">{{ $genre }}</span>
                        @endforeach
                    </div>
                @endif

                {{-- Details Row --}}
                <div class="flex items-center gap-2 text-[11px] text-dark-300">
                    @if($episodes && $episodes !== '?')
                        <span>{{ $episodes }} eps</span>
                        <span class="text-dark-600">•</span>
                    @endif
                    @if($year)
                        <span>{{ $year }}</span>
                        <span class="text-dark-600">•</span>
                    @endif
                    @if($status)
                        <span class="@if($status === 'RELEASING') text-emerald-400 @elseif($status === 'FINISHED') text-sky-400 @else text-dark-400 @endif">
                            {{ \App\Services\AniListService::formatStatus($status) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Title Area --}}
        <div class="p-3">
            <h3 class="text-sm font-semibold text-white light:text-dark-900 line-clamp-2 leading-tight group-hover:text-primary-400 transition-colors">
                {{ $title }}
            </h3>
            @if($romaji && $romaji !== $title)
                <p class="text-xs text-dark-500 light:text-dark-400 mt-1 line-clamp-1">{{ $romaji }}</p>
            @endif
        </div>
    </div>
</a>
