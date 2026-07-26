@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6"
     x-data="{
         currentLang: '{{ !empty($embedSub) || !empty($embedVidcloudSub) || !empty($embedUpcloudSub) || !empty($embedMegacloudSub) || !empty($embedGogoSub) || !empty($embedFilemoonSub) || !empty($embedStreamtapeSub) || !empty($embedDoodSub) || !empty($embedStreamSbSub) || !empty($embedMp4uploadSub) ? 'sub' : 'dub' }}',
         currentServer: '{{ !empty($embedSub) || !empty($embedDub) ? 'anikoto' : (!empty($embedVidcloudSub) || !empty($embedVidcloudDub) ? 'vidcloud' : (!empty($embedUpcloudSub) || !empty($embedUpcloudDub) ? 'upcloud' : (!empty($embedMegacloudSub) || !empty($embedMegacloudDub) ? 'megacloud' : (!empty($embedGogoSub) || !empty($embedGogoDub) ? 'gogoanime' : (!empty($embedFilemoonSub) || !empty($embedFilemoonDub) ? 'filemoon' : (!empty($embedStreamtapeSub) || !empty($embedStreamtapeDub) ? 'streamtape' : (!empty($embedDoodSub) || !empty($embedDoodDub) ? 'doodstream' : (!empty($embedStreamSbSub) || !empty($embedStreamSbDub) ? 'streamsb' : (!empty($embedMp4uploadSub) || !empty($embedMp4uploadDub) ? 'mp4upload' : 'vidcloud'))))))))) }}',
         embedSub: '{{ $embedSub }}',
         embedDub: '{{ $embedDub }}',
         embedVidcloudSub: '{{ $embedVidcloudSub ?? '' }}',
         embedVidcloudDub: '{{ $embedVidcloudDub ?? '' }}',
         embedUpcloudSub: '{{ $embedUpcloudSub ?? '' }}',
         embedUpcloudDub: '{{ $embedUpcloudDub ?? '' }}',
         embedMegacloudSub: '{{ $embedMegacloudSub ?? '' }}',
         embedMegacloudDub: '{{ $embedMegacloudDub ?? '' }}',
         embedGogoSub: '{{ $embedGogoSub ?? '' }}',
         embedGogoDub: '{{ $embedGogoDub ?? '' }}',
         embedFilemoonSub: '{{ $embedFilemoonSub ?? '' }}',
         embedFilemoonDub: '{{ $embedFilemoonDub ?? '' }}',
         embedStreamtapeSub: '{{ $embedStreamtapeSub ?? '' }}',
         embedStreamtapeDub: '{{ $embedStreamtapeDub ?? '' }}',
         embedDoodSub: '{{ $embedDoodSub ?? '' }}',
         embedDoodDub: '{{ $embedDoodDub ?? '' }}',
         embedStreamSbSub: '{{ $embedStreamSbSub ?? '' }}',
         embedStreamSbDub: '{{ $embedStreamSbDub ?? '' }}',
         embedMp4uploadSub: '{{ $embedMp4uploadSub ?? '' }}',
         embedMp4uploadDub: '{{ $embedMp4uploadDub ?? '' }}',
         embedMiruroSub: '{{ $embedMiruroSub ?? '' }}',
         embedMiruroDub: '{{ $embedMiruroDub ?? '' }}',
         autoplay: localStorage.getItem('anikoto_autoplay') === 'true',
         
         getEmbedUrl() {
             if (this.currentServer === 'vidcloud') return (this.currentLang === 'dub' ? this.embedVidcloudDub || this.embedVidcloudSub : this.embedVidcloudSub || this.embedVidcloudDub) || '';
             if (this.currentServer === 'upcloud') return (this.currentLang === 'dub' ? this.embedUpcloudDub || this.embedUpcloudSub : this.embedUpcloudSub || this.embedUpcloudDub) || '';
             if (this.currentServer === 'megacloud') return (this.currentLang === 'dub' ? this.embedMegacloudDub || this.embedMegacloudSub : this.embedMegacloudSub || this.embedMegacloudDub) || '';
             if (this.currentServer === 'gogoanime') return (this.currentLang === 'dub' ? this.embedGogoDub || this.embedGogoSub : this.embedGogoSub || this.embedGogoDub) || '';
             if (this.currentServer === 'filemoon') return (this.currentLang === 'dub' ? this.embedFilemoonDub || this.embedFilemoonSub : this.embedFilemoonSub || this.embedFilemoonDub) || '';
             if (this.currentServer === 'streamtape') return (this.currentLang === 'dub' ? this.embedStreamtapeDub || this.embedStreamtapeSub : this.embedStreamtapeSub || this.embedStreamtapeDub) || '';
             if (this.currentServer === 'doodstream') return (this.currentLang === 'dub' ? this.embedDoodDub || this.embedDoodSub : this.embedDoodSub || this.embedDoodDub) || '';
             if (this.currentServer === 'streamsb') return (this.currentLang === 'dub' ? this.embedStreamSbDub || this.embedStreamSbSub : this.embedStreamSbSub || this.embedStreamSbDub) || '';
             if (this.currentServer === 'mp4upload') return (this.currentLang === 'dub' ? this.embedMp4uploadDub || this.embedMp4uploadSub : this.embedMp4uploadSub || this.embedMp4uploadDub) || '';
             if (this.currentServer === 'miruro') return (this.currentLang === 'dub' ? this.embedMiruroDub || this.embedMiruroSub : this.embedMiruroSub || this.embedMiruroDub) || '';
             if (this.currentLang === 'dub') return this.embedDub || this.embedSub || this.embedVidcloudDub || this.embedUpcloudDub || '';
             return this.embedSub || this.embedDub || this.embedVidcloudSub || this.embedUpcloudSub || '';
         },
         
         hasEmbed() {
             const url = this.getEmbedUrl();
             return url && url.trim() !== '';
         },
         
         isDirectVideo() {
             const url = this.getEmbedUrl();
             return url && (url.endsWith('.mp4') || url.endsWith('.m3u8') || url.includes('/data') || url.includes('.mp4?'));
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
                
                <template x-if="hasEmbed() && isDirectVideo()">
                    <video
                        :src="getEmbedUrl()"
                        controls
                        autoplay
                        class="absolute inset-0 w-full h-full bg-black">
                        Your browser does not support HTML5 video.
                    </video>
                </template>

                <template x-if="hasEmbed() && !isDirectVideo()">
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

            {{-- Dedicated Streaming Server Selector Deck --}}
            <div class="p-5 rounded-3xl bg-dark-900/70 border border-dark-800/80 shadow-xl mb-6">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    
                    {{-- Server Selection List --}}
                    <div class="space-y-3 flex-1">
                        <div class="text-xs font-bold text-dark-400 uppercase tracking-wider flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
                            Select Streaming Server:
                        </div>
                        
                        {{-- SUB row --}}
                        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                            <span class="text-xs font-extrabold text-primary-400 bg-primary-500/10 px-2.5 py-1.5 rounded-lg border border-primary-500/20 shrink-0 w-14 text-center">
                                SUB
                            </span>
                            <div class="flex flex-wrap items-center gap-2">
                                @if(!empty($embedSub))
                                    <button 
                                        @click="currentLang = 'sub'; currentServer = 'anikoto'" 
                                        :class="currentLang === 'sub' && currentServer === 'anikoto' ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/30 font-bold border-primary-500' : 'bg-dark-800 text-dark-300 hover:bg-dark-700 hover:text-white border-dark-700'"
                                        class="px-3.5 py-1.5 rounded-xl text-xs font-semibold border transition-all flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full" :class="currentLang === 'sub' && currentServer === 'anikoto' ? 'bg-white animate-pulse' : 'bg-emerald-400'"></span>
                                        Server 1: Anikoto
                                    </button>
                                @endif
                                
                                <button 
                                    @click="currentLang = 'sub'; currentServer = 'vidcloud'" 
                                    :class="currentLang === 'sub' && currentServer === 'vidcloud' ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/30 font-bold border-primary-500' : 'bg-dark-800 text-dark-300 hover:bg-dark-700 hover:text-white border-dark-700'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold border transition-all flex items-center gap-2"
                                    title="{{ !empty($embedVidcloudSub) ? 'Play Vidcloud / HiAnime Stream' : 'Vidcloud source offline or standby' }}">
                                    <span class="w-2 h-2 rounded-full" :class="!empty($embedVidcloudSub) ? (currentLang === 'sub' && currentServer === 'vidcloud' ? 'bg-white animate-pulse' : 'bg-emerald-400') : 'bg-amber-400'"></span>
                                    Server 2: Vidcloud @if(empty($embedVidcloudSub)) <span class="text-[10px] opacity-75 font-normal">(Standby)</span> @endif
                                </button>

                                <button 
                                    @click="currentLang = 'sub'; currentServer = 'upcloud'" 
                                    :class="currentLang === 'sub' && currentServer === 'upcloud' ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/30 font-bold border-primary-500' : 'bg-dark-800 text-dark-300 hover:bg-dark-700 hover:text-white border-dark-700'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold border transition-all flex items-center gap-2"
                                    title="{{ !empty($embedUpcloudSub) ? 'Play UpCloud / Moviebox Stream' : 'UpCloud source offline or standby' }}">
                                    <span class="w-2 h-2 rounded-full" :class="!empty($embedUpcloudSub) ? (currentLang === 'sub' && currentServer === 'upcloud' ? 'bg-white animate-pulse' : 'bg-emerald-400') : 'bg-amber-400'"></span>
                                    Server 3: UpCloud @if(empty($embedUpcloudSub)) <span class="text-[10px] opacity-75 font-normal">(Standby)</span> @endif
                                </button>

                                <button 
                                    @click="currentLang = 'sub'; currentServer = 'megacloud'" 
                                    :class="currentLang === 'sub' && currentServer === 'megacloud' ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/30 font-bold border-primary-500' : 'bg-dark-800 text-dark-300 hover:bg-dark-700 hover:text-white border-dark-700'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold border transition-all flex items-center gap-2"
                                    title="{{ !empty($embedMegacloudSub) ? 'Play MegaCloud Stream' : 'MegaCloud source offline or standby' }}">
                                    <span class="w-2 h-2 rounded-full" :class="!empty($embedMegacloudSub) ? (currentLang === 'sub' && currentServer === 'megacloud' ? 'bg-white animate-pulse' : 'bg-emerald-400') : 'bg-amber-400'"></span>
                                    Server 4: MegaCloud @if(empty($embedMegacloudSub)) <span class="text-[10px] opacity-75 font-normal">(Standby)</span> @endif
                                </button>

                                <button 
                                    @click="currentLang = 'sub'; currentServer = 'gogoanime'" 
                                    :class="currentLang === 'sub' && currentServer === 'gogoanime' ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/30 font-bold border-primary-500' : 'bg-dark-800 text-dark-300 hover:bg-dark-700 hover:text-white border-dark-700'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold border transition-all flex items-center gap-2"
                                    title="{{ !empty($embedGogoSub) ? 'Play Gogoanime HD Stream' : 'Gogoanime source offline or standby' }}">
                                    <span class="w-2 h-2 rounded-full" :class="!empty($embedGogoSub) ? (currentLang === 'sub' && currentServer === 'gogoanime' ? 'bg-white animate-pulse' : 'bg-emerald-400') : 'bg-amber-400'"></span>
                                    Server 5: Gogoanime @if(empty($embedGogoSub)) <span class="text-[10px] opacity-75 font-normal">(Standby)</span> @endif
                                </button>

                                <button 
                                    @click="currentLang = 'sub'; currentServer = 'filemoon'" 
                                    :class="currentLang === 'sub' && currentServer === 'filemoon' ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/30 font-bold border-primary-500' : 'bg-dark-800 text-dark-300 hover:bg-dark-700 hover:text-white border-dark-700'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold border transition-all flex items-center gap-2"
                                    title="{{ !empty($embedFilemoonSub) ? 'Play Filemoon Stream' : 'Filemoon source offline or standby' }}">
                                    <span class="w-2 h-2 rounded-full" :class="!empty($embedFilemoonSub) ? (currentLang === 'sub' && currentServer === 'filemoon' ? 'bg-white animate-pulse' : 'bg-emerald-400') : 'bg-amber-400'"></span>
                                    Server 6: Filemoon @if(empty($embedFilemoonSub)) <span class="text-[10px] opacity-75 font-normal">(Standby)</span> @endif
                                </button>

                                <button 
                                    @click="currentLang = 'sub'; currentServer = 'streamtape'" 
                                    :class="currentLang === 'sub' && currentServer === 'streamtape' ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/30 font-bold border-primary-500' : 'bg-dark-800 text-dark-300 hover:bg-dark-700 hover:text-white border-dark-700'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold border transition-all flex items-center gap-2"
                                    title="{{ !empty($embedStreamtapeSub) ? 'Play StreamTape Stream' : 'StreamTape source offline or standby' }}">
                                    <span class="w-2 h-2 rounded-full" :class="!empty($embedStreamtapeSub) ? (currentLang === 'sub' && currentServer === 'streamtape' ? 'bg-white animate-pulse' : 'bg-emerald-400') : 'bg-amber-400'"></span>
                                    Server 7: StreamTape @if(empty($embedStreamtapeSub)) <span class="text-[10px] opacity-75 font-normal">(Standby)</span> @endif
                                </button>

                                <button 
                                    @click="currentLang = 'sub'; currentServer = 'doodstream'" 
                                    :class="currentLang === 'sub' && currentServer === 'doodstream' ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/30 font-bold border-primary-500' : 'bg-dark-800 text-dark-300 hover:bg-dark-700 hover:text-white border-dark-700'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold border transition-all flex items-center gap-2"
                                    title="{{ !empty($embedDoodSub) ? 'Play DoodStream Stream' : 'DoodStream source offline or standby' }}">
                                    <span class="w-2 h-2 rounded-full" :class="!empty($embedDoodSub) ? (currentLang === 'sub' && currentServer === 'doodstream' ? 'bg-white animate-pulse' : 'bg-emerald-400') : 'bg-amber-400'"></span>
                                    Server 8: DoodStream @if(empty($embedDoodSub)) <span class="text-[10px] opacity-75 font-normal">(Standby)</span> @endif
                                </button>

                                <button 
                                    @click="currentLang = 'sub'; currentServer = 'streamsb'" 
                                    :class="currentLang === 'sub' && currentServer === 'streamsb' ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/30 font-bold border-primary-500' : 'bg-dark-800 text-dark-300 hover:bg-dark-700 hover:text-white border-dark-700'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold border transition-all flex items-center gap-2"
                                    title="{{ !empty($embedStreamSbSub) ? 'Play StreamSB / Tape Stream' : 'StreamSB source offline or standby' }}">
                                    <span class="w-2 h-2 rounded-full" :class="!empty($embedStreamSbSub) ? (currentLang === 'sub' && currentServer === 'streamsb' ? 'bg-white animate-pulse' : 'bg-emerald-400') : 'bg-amber-400'"></span>
                                    Server 9: StreamSB @if(empty($embedStreamSbSub)) <span class="text-[10px] opacity-75 font-normal">(Standby)</span> @endif
                                </button>

                                <button 
                                    @click="currentLang = 'sub'; currentServer = 'mp4upload'" 
                                    :class="currentLang === 'sub' && currentServer === 'mp4upload' ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/30 font-bold border-primary-500' : 'bg-dark-800 text-dark-300 hover:bg-dark-700 hover:text-white border-dark-700'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold border transition-all flex items-center gap-2"
                                    title="{{ !empty($embedMp4uploadSub) ? 'Play Mp4Upload Stream' : 'Mp4Upload source offline or standby' }}">
                                    <span class="w-2 h-2 rounded-full" :class="!empty($embedMp4uploadSub) ? (currentLang === 'sub' && currentServer === 'mp4upload' ? 'bg-white animate-pulse' : 'bg-emerald-400') : 'bg-amber-400'"></span>
                                    Server 10: Mp4Upload @if(empty($embedMp4uploadSub)) <span class="text-[10px] opacity-75 font-normal">(Standby)</span> @endif
                                </button>

                                <button 
                                    @click="currentLang = 'sub'; currentServer = 'miruro'" 
                                    :class="currentLang === 'sub' && currentServer === 'miruro' ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/30 font-bold border-primary-500' : 'bg-dark-800 text-dark-300 hover:bg-dark-700 hover:text-white border-dark-700'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold border transition-all flex items-center gap-2"
                                    title="{{ !empty($embedMiruroSub) ? 'Play Miruro / Zoro Stream' : 'Miruro source offline or standby' }}">
                                    <span class="w-2 h-2 rounded-full" :class="!empty($embedMiruroSub) ? (currentLang === 'sub' && currentServer === 'miruro' ? 'bg-white animate-pulse' : 'bg-emerald-400') : 'bg-amber-400'"></span>
                                    Server 11: Zoro @if(empty($embedMiruroSub)) <span class="text-[10px] opacity-75 font-normal">(Standby)</span> @endif
                                </button>
                            </div>
                        </div>

                        {{-- DUB row --}}
                        @if(!empty($embedDub) || !empty($embedVidcloudDub) || !empty($embedUpcloudDub) || !empty($embedMegacloudDub) || !empty($embedGogoDub) || !empty($embedFilemoonDub) || !empty($embedStreamtapeDub) || !empty($embedDoodDub) || !empty($embedStreamSbDub) || !empty($embedMp4uploadDub) || !empty($embedMiruroDub))
                        <div class="flex flex-wrap items-center gap-2 sm:gap-3 pt-2 border-t border-dark-800/60">
                            <span class="text-xs font-extrabold text-indigo-400 bg-indigo-500/10 px-2.5 py-1.5 rounded-lg border border-indigo-500/20 shrink-0 w-14 text-center">
                                DUB
                            </span>
                            <div class="flex flex-wrap items-center gap-2">
                                @if(!empty($embedDub))
                                <button 
                                    @click="currentLang = 'dub'; currentServer = 'anikoto'" 
                                    :class="currentLang === 'dub' && currentServer === 'anikoto' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30 font-bold border-indigo-500' : 'bg-dark-800 text-dark-300 hover:bg-dark-700 hover:text-white border-dark-700'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold border transition-all flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full" :class="currentLang === 'dub' && currentServer === 'anikoto' ? 'bg-white animate-pulse' : 'bg-emerald-400'"></span>
                                    Server 1: Anikoto
                                </button>
                                @endif

                                @if(!empty($embedVidcloudDub))
                                <button 
                                    @click="currentLang = 'dub'; currentServer = 'vidcloud'" 
                                    :class="currentLang === 'dub' && currentServer === 'vidcloud' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30 font-bold border-indigo-500' : 'bg-dark-800 text-dark-300 hover:bg-dark-700 hover:text-white border-dark-700'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold border transition-all flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full" :class="currentLang === 'dub' && currentServer === 'vidcloud' ? 'bg-white animate-pulse' : 'bg-emerald-400'"></span>
                                    Server 2: Vidcloud
                                </button>
                                @endif

                                @if(!empty($embedUpcloudDub))
                                <button 
                                    @click="currentLang = 'dub'; currentServer = 'upcloud'" 
                                    :class="currentLang === 'dub' && currentServer === 'upcloud' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30 font-bold border-indigo-500' : 'bg-dark-800 text-dark-300 hover:bg-dark-700 hover:text-white border-dark-700'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold border transition-all flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full" :class="currentLang === 'dub' && currentServer === 'upcloud' ? 'bg-white animate-pulse' : 'bg-emerald-400'"></span>
                                    Server 3: UpCloud
                                </button>
                                @endif

                                @if(!empty($embedMegacloudDub))
                                <button 
                                    @click="currentLang = 'dub'; currentServer = 'megacloud'" 
                                    :class="currentLang === 'dub' && currentServer === 'megacloud' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30 font-bold border-indigo-500' : 'bg-dark-800 text-dark-300 hover:bg-dark-700 hover:text-white border-dark-700'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold border transition-all flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full" :class="currentLang === 'dub' && currentServer === 'megacloud' ? 'bg-white animate-pulse' : 'bg-emerald-400'"></span>
                                    Server 4: MegaCloud
                                </button>
                                @endif

                                @if(!empty($embedGogoDub))
                                <button 
                                    @click="currentLang = 'dub'; currentServer = 'gogoanime'" 
                                    :class="currentLang === 'dub' && currentServer === 'gogoanime' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30 font-bold border-indigo-500' : 'bg-dark-800 text-dark-300 hover:bg-dark-700 hover:text-white border-dark-700'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold border transition-all flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full" :class="currentLang === 'dub' && currentServer === 'gogoanime' ? 'bg-white animate-pulse' : 'bg-emerald-400'"></span>
                                    Server 5: Gogoanime
                                </button>
                                @endif

                                @if(!empty($embedFilemoonDub))
                                <button 
                                    @click="currentLang = 'dub'; currentServer = 'filemoon'" 
                                    :class="currentLang === 'dub' && currentServer === 'filemoon' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30 font-bold border-indigo-500' : 'bg-dark-800 text-dark-300 hover:bg-dark-700 hover:text-white border-dark-700'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold border transition-all flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full" :class="currentLang === 'dub' && currentServer === 'filemoon' ? 'bg-white animate-pulse' : 'bg-emerald-400'"></span>
                                    Server 6: Filemoon
                                </button>
                                @endif

                                @if(!empty($embedStreamtapeDub))
                                <button 
                                    @click="currentLang = 'dub'; currentServer = 'streamtape'" 
                                    :class="currentLang === 'dub' && currentServer === 'streamtape' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30 font-bold border-indigo-500' : 'bg-dark-800 text-dark-300 hover:bg-dark-700 hover:text-white border-dark-700'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold border transition-all flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full" :class="currentLang === 'dub' && currentServer === 'streamtape' ? 'bg-white animate-pulse' : 'bg-emerald-400'"></span>
                                    Server 7: StreamTape
                                </button>
                                @endif

                                @if(!empty($embedDoodDub))
                                <button 
                                    @click="currentLang = 'dub'; currentServer = 'doodstream'" 
                                    :class="currentLang === 'dub' && currentServer === 'doodstream' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30 font-bold border-indigo-500' : 'bg-dark-800 text-dark-300 hover:bg-dark-700 hover:text-white border-dark-700'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold border transition-all flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full" :class="currentLang === 'dub' && currentServer === 'doodstream' ? 'bg-white animate-pulse' : 'bg-emerald-400'"></span>
                                    Server 8: DoodStream
                                </button>
                                @endif

                                @if(!empty($embedStreamSbDub))
                                <button 
                                    @click="currentLang = 'dub'; currentServer = 'streamsb'" 
                                    :class="currentLang === 'dub' && currentServer === 'streamsb' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30 font-bold border-indigo-500' : 'bg-dark-800 text-dark-300 hover:bg-dark-700 hover:text-white border-dark-700'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold border transition-all flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full" :class="currentLang === 'dub' && currentServer === 'streamsb' ? 'bg-white animate-pulse' : 'bg-emerald-400'"></span>
                                    Server 9: StreamSB
                                </button>
                                @endif

                                @if(!empty($embedMp4uploadDub))
                                <button 
                                    @click="currentLang = 'dub'; currentServer = 'mp4upload'" 
                                    :class="currentLang === 'dub' && currentServer === 'mp4upload' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30 font-bold border-indigo-500' : 'bg-dark-800 text-dark-300 hover:bg-dark-700 hover:text-white border-dark-700'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold border transition-all flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full" :class="currentLang === 'dub' && currentServer === 'mp4upload' ? 'bg-white animate-pulse' : 'bg-emerald-400'"></span>
                                    Server 10: Mp4Upload
                                </button>
                                @endif

                                @if(!empty($embedMiruroDub))
                                <button 
                                    @click="currentLang = 'dub'; currentServer = 'miruro'" 
                                    :class="currentLang === 'dub' && currentServer === 'miruro' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30 font-bold border-indigo-500' : 'bg-dark-800 text-dark-300 hover:bg-dark-700 hover:text-white border-dark-700'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold border transition-all flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full" :class="currentLang === 'dub' && currentServer === 'miruro' ? 'bg-white animate-pulse' : 'bg-emerald-400'"></span>
                                    Server 11: Zoro
                                </button>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Right: Autoplay controls --}}
                    <div class="flex items-center gap-4 border-t lg:border-t-0 lg:border-l border-dark-800/80 pt-4 lg:pt-0 lg:pl-6 shrink-0">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="autoplay" class="sr-only peer">
                            <div class="w-9 h-5 bg-dark-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary-600"></div>
                            <span class="ml-2 text-xs font-semibold text-dark-300">Auto Next Episode</span>
                        </label>
                    </div>

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
