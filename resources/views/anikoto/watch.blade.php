@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6"
     x-data="{
         currentLanguage: '{{ !empty($embedSub) ? 'sub' : 'dub' }}',
         embedSub: '{{ $embedSub }}',
         embedDub: '{{ $embedDub }}',
         autoplay: localStorage.getItem('anikoto_autoplay') === 'true',
         
         getEmbedUrl() {
             return this.currentLanguage === 'dub' ? this.embedDub : this.embedSub;
         },
         
         hasEmbed() {
             const url = this.getEmbedUrl();
             return url && url.trim() !== '';
         },
         
         init() {
             this.$watch('autoplay', value => localStorage.setItem('anikoto_autoplay', value));
             
             // Listen for MegaPlay Embed events
             window.addEventListener('message', (event) => {
                 let data = event.data;
                 if (typeof data === 'string') {
                     try {
                         data = JSON.parse(data);
                     } catch(e) {
                         return;
                     }
                 }
                 
                 // MegaPlay complete trigger
                 if (data.event === 'complete' && this.autoplay) {
                     this.playNext();
                 }
             });
             
             // Remember last watched episode
             localStorage.setItem('anikoto_last_watched_{{ $id }}', '{{ $episodeNum }}');
         },
         
         playNext() {
             @if($nextEpisode)
                 window.location.href = '{{ route('watch', ['id' => $id, 'episode' => $nextEpisode]) }}';
             @else
                 window.showToast('You have reached the final episode!', 'success');
             @endif
         }
     }">

    {{-- Breadcrumb --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <a href="{{ route('anime.show', ['id' => $id]) }}" class="inline-flex items-center gap-2 text-sm text-dark-400 hover:text-primary-400 font-semibold transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Series
        </a>
        <div class="text-sm text-dark-400 font-medium">
            Streaming: <span class="text-primary-400 font-bold uppercase">Episode {{ $episodeNum }}</span>
        </div>
    </div>

    {{-- Video Deck --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
        {{-- Left: Player and Metadata --}}
        <div class="lg:col-span-2 text-left">
            
            {{-- Aspect Ratio 16:9 Frame --}}
            <div class="relative w-full aspect-video rounded-3xl overflow-hidden bg-black shadow-2xl border border-dark-800/80 mb-6">
                
                <template x-if="hasEmbed()">
                    <iframe
                        :src="getEmbedUrl()"
                        class="absolute inset-0 w-full h-full border-0"
                        allow="autoplay; fullscreen; picture-in-picture"
                        allowfullscreen
                        loading="lazy">
                    </iframe>
                </template>
                
                <template x-if="!hasEmbed()">
                    <div class="absolute inset-0 flex flex-col items-center justify-center bg-dark-950 text-white p-6 text-center">
                        <span class="text-4xl mb-4">🚫</span>
                        <h4 class="text-lg font-bold mb-2">Streaming Temporarily Unavailable</h4>
                        <p class="text-sm text-dark-400 max-w-md">
                            No valid video stream sources were returned by the API for the selected language. Try switching options below.
                        </p>
                    </div>
                </template>

            </div>

            {{-- Playback Mode & Language Toggles --}}
            <div class="p-5 rounded-2xl bg-dark-900/50 border border-dark-800 mb-6 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    @if(!empty($embedSub))
                        <button
                            @click="currentLanguage = 'sub'"
                            :class="currentLanguage === 'sub' ? 'bg-primary-600 text-white' : 'bg-dark-800 text-dark-300 hover:bg-dark-700'"
                            class="px-4 py-2 rounded-xl text-xs font-bold transition-all">
                            Subtitled (SUB)
                        </button>
                    @endif
                    @if(!empty($embedDub))
                        <button
                            @click="currentLanguage = 'dub'"
                            :class="currentLanguage === 'dub' ? 'bg-primary-600 text-white' : 'bg-dark-800 text-dark-300 hover:bg-dark-700'"
                            class="px-4 py-2 rounded-xl text-xs font-bold transition-all">
                            English Dubbed (DUB)
                        </button>
                    @endif
                </div>

                {{-- Autoplay controls --}}
                <div class="flex items-center gap-4">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" x-model="autoplay" class="sr-only peer">
                        <div class="w-9 h-5 bg-dark-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary-600"></div>
                        <span class="ml-2 text-xs font-semibold text-dark-300">Auto Next Episode</span>
                    </label>
                </div>
            </div>

            {{-- Navigation Buttons --}}
            <div class="flex items-center justify-between mb-8">
                @if($prevEpisode)
                    <a href="{{ route('watch', ['id' => $id, 'episode' => $prevEpisode]) }}"
                       class="px-6 py-3 rounded-2xl bg-dark-800 border border-dark-700 text-sm font-semibold text-white flex items-center gap-2 hover:bg-dark-700 transition-colors">
                        ◀ Previous Episode
                    </a>
                @else
                    <button disabled class="px-6 py-3 rounded-2xl bg-dark-800 border border-dark-700 text-sm font-semibold text-white opacity-40 cursor-not-allowed flex items-center gap-2">
                        ◀ Previous Episode
                    </button>
                @endif

                @if($nextEpisode)
                    <a href="{{ route('watch', ['id' => $id, 'episode' => $nextEpisode]) }}"
                       class="px-6 py-3 rounded-2xl bg-primary-600 text-white text-sm font-semibold flex items-center gap-2 hover:bg-primary-500 transition-colors">
                        Next Episode ▶
                    </a>
                @else
                    <button disabled class="px-6 py-3 rounded-2xl bg-primary-600 text-white text-sm font-semibold opacity-40 cursor-not-allowed flex items-center gap-2">
                        Next Episode ▶
                    </button>
                @endif
            </div>

            {{-- Anime Info Box --}}
            <div class="p-6 rounded-3xl bg-dark-900/40 border border-dark-800/80 shadow-xl mb-8 flex flex-col sm:flex-row gap-6">
                @if(!empty($anime['poster']))
                    <div class="w-32 shrink-0 rounded-2xl overflow-hidden shadow border border-dark-800/30 self-start">
                        <img src="{{ $anime['poster'] }}" alt="{{ $anime['title'] ?? 'Poster' }}" class="w-full object-cover">
                    </div>
                @endif
                <div>
                    <h2 class="text-2xl font-bold text-white mb-1">
                        {{ $anime['title'] ?? 'Unknown' }}
                    </h2>
                    @if(!empty($currentEpisode['title']))
                        <p class="text-sm text-primary-400 font-semibold mb-4">Episode {{ $episodeNum }}: {{ $currentEpisode['title'] }}</p>
                    @endif
                    <p class="text-dark-300 text-sm leading-relaxed mb-4">
                        {{ $anime['description'] ?? 'No description available.' }}
                    </p>
                    <div class="flex flex-wrap gap-1.5 pt-2">
                        @foreach((array)($anime['terms_by_type']['genre'] ?? []) as $genre)
                            <span class="px-2 py-0.5 rounded bg-dark-800 text-dark-300 text-xs">{{ $genre }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Episode Grid List Selector --}}
        <div>
            <div class="p-6 rounded-3xl bg-dark-900/40 border border-dark-800/80 shadow-xl max-h-[80vh] overflow-y-auto sticky top-24 text-left">
                <h3 class="text-lg font-bold text-white mb-4 flex items-center justify-between">
                    <span>Episode List</span>
                    <span class="text-xs text-dark-500 font-normal">{{ count($episodes) }} total</span>
                </h3>
                <div class="grid grid-cols-4 gap-2">
                    @foreach($episodes as $ep)
                        @php
                            $isCurrent = (string)($ep['number'] ?? '') === (string)$episodeNum;
                        @endphp
                        <a href="{{ route('watch', ['id' => $id, 'episode' => $ep['number'] ?? $loop->iteration]) }}"
                           class="py-2.5 rounded-xl text-xs transition-all font-semibold text-center {{ $isCurrent ? 'bg-primary-600 text-white font-bold' : 'bg-dark-800 text-dark-300 hover:bg-dark-700' }}">
                            {{ $ep['number'] ?? $loop->iteration }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
