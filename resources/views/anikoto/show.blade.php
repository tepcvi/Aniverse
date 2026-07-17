@extends('layouts.app')

@section('content')
<div class="relative min-h-[260px] md:min-h-[380px] bg-dark-950 flex items-end">
    {{-- Banner image blur background --}}
    <div class="absolute inset-0 bg-cover bg-center filter blur-md scale-105 opacity-15" style="background-image: url('{{ $anime['background_image'] ?? $anime['poster'] }}')"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-dark-950 via-dark-950/80 to-transparent"></div>
    
    {{-- Details Header content --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full relative z-10">
        <div class="flex flex-col md:flex-row gap-8 items-start">
            {{-- Poster Cover --}}
            <div class="w-44 shrink-0 rounded-2xl overflow-hidden shadow-2xl border border-dark-800/80 self-start">
                <img src="{{ $anime['poster'] }}" alt="{{ $anime['title'] ?? 'Poster' }}" class="w-full h-auto">
            </div>
            
            {{-- Title info --}}
            <div class="flex-1 text-left">
                <h1 class="text-3xl md:text-4xl font-extrabold text-white leading-tight mb-2">
                    {{ $anime['title'] ?? 'Unknown' }}
                </h1>
                @if(!empty($anime['native']))
                    <p class="text-sm text-primary-400 font-semibold mb-4">{{ $anime['native'] }}</p>
                @endif
                @if(!empty($anime['alternative']))
                    <p class="text-xs text-dark-400 mb-6 italic">{{ $anime['alternative'] }}</p>
                @endif

                {{-- Horizontal metadata stats --}}
                <div class="flex flex-wrap items-center gap-3 text-xs mb-6 font-semibold">
                    @if(!empty($anime['score']))
                        <span class="px-3 py-1 rounded-xl bg-yellow-500/10 text-yellow-400 border border-yellow-500/20">★ {{ $anime['score'] }} Rating</span>
                    @endif
                    @if(!empty($anime['status']))
                        <span class="px-3 py-1 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">{{ $anime['status'] }}</span>
                    @endif
                    @if(!empty($anime['terms_by_type']['type'][0]))
                        <span class="px-3 py-1 rounded-xl bg-primary-500/10 text-primary-400 border border-primary-500/20 uppercase">{{ $anime['terms_by_type']['type'][0] }}</span>
                    @endif
                    @if(!empty($anime['episodes']))
                        <span class="px-3 py-1 rounded-xl bg-dark-800 text-dark-300">{{ $anime['episodes'] }} Episodes</span>
                    @endif
                </div>

                {{-- Genres --}}
                <div class="flex flex-wrap gap-2">
                    @foreach((array)($anime['terms_by_type']['genre'] ?? []) as $genre)
                        <span class="px-3 py-1 rounded-xl bg-dark-800/60 text-dark-300 text-xs border border-dark-800">{{ $genre }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Left: Details and Synopsis --}}
        <div class="lg:col-span-2 text-left">
            <section class="mb-10">
                <h2 class="text-xl font-bold text-white mb-4">Synopsis</h2>
                <div class="p-6 rounded-3xl bg-dark-900/40 border border-dark-800/80 leading-relaxed text-sm text-dark-300">
                    {!! nl2br(e($anime['description'] ?? 'No description available.')) !!}
                </div>
            </section>

            {{-- Dynamic Episode Grid --}}
            <section class="mb-10">
                <h2 class="text-xl font-bold text-white mb-4">📺 Episode List</h2>
                
                @if(!empty($episodes))
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                        @foreach($episodes as $ep)
                            <a href="{{ route('watch', ['id' => $id, 'episode' => $ep['number'] ?? $loop->iteration]) }}"
                               class="group p-4 rounded-2xl bg-dark-900/40 hover:bg-primary-600/10 border border-dark-800 hover:border-primary-500/30 text-center font-bold text-sm text-white transition-all duration-200 flex flex-col justify-center items-center">
                                <span class="text-primary-400 group-hover:text-primary-300">Episode {{ $ep['number'] ?? $loop->iteration }}</span>
                                @if(!empty($ep['title']))
                                    <span class="text-[10px] text-dark-500 font-normal truncate max-w-full mt-1">{{ $ep['title'] }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center rounded-2xl bg-dark-900/30 border border-dark-800/80">
                        <p class="text-sm text-dark-400">No episodes are currently indexed for this series.</p>
                    </div>
                @endif
            </section>
        </div>

        {{-- Right: Side Details --}}
        <div class="text-left">
            <div class="p-6 rounded-3xl bg-dark-900/40 border border-dark-800/80">
                <h3 class="text-sm font-bold uppercase tracking-wider text-dark-400 mb-4">Information</h3>
                <dl class="space-y-4 text-xs">
                    @if(!empty($anime['aired']))
                        <div>
                            <dt class="text-dark-500 mb-1">Aired</dt>
                            <dd class="text-white font-medium">{{ $anime['aired'] }}</dd>
                        </div>
                    @endif
                    @if(!empty($anime['season']))
                        <div>
                            <dt class="text-dark-500 mb-1">Season</dt>
                            <dd class="text-white font-medium capitalize">{{ $anime['season'] }} {{ $anime['year'] ?? '' }}</dd>
                        </div>
                    @endif
                    @if(!empty($anime['duration']))
                        <div>
                            <dt class="text-dark-500 mb-1">Duration</dt>
                            <dd class="text-white font-medium">{{ $anime['duration'] }}</dd>
                        </div>
                    @endif
                    @if(!empty($anime['rating']))
                        <div>
                            <dt class="text-dark-500 mb-1">Rating</dt>
                            <dd class="text-white font-medium">{{ $anime['rating'] }}</dd>
                        </div>
                    @endif
                    @if(!empty($anime['terms_by_type']['studios']))
                        <div>
                            <dt class="text-dark-500 mb-1">Studios</dt>
                            <dd class="text-white font-medium">{{ implode(', ', (array)$anime['terms_by_type']['studios']) }}</dd>
                        </div>
                    @endif
                    @if(!empty($anime['terms_by_type']['producers']))
                        <div>
                            <dt class="text-dark-500 mb-1">Producers</dt>
                            <dd class="text-white font-medium text-dark-400">{{ implode(', ', (array)$anime['terms_by_type']['producers']) }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
