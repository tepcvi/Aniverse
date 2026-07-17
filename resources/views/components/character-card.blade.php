@props(['character'])

@php
    $charName = $character['node']['name']['full'] ?? 'Unknown';
    $charImage = $character['node']['image']['large'] ?? $character['node']['image']['medium'] ?? '';
    $role = $character['role'] ?? '';
    $vaRole = $character['voiceActorRoles'][0] ?? null;
    $vaName = $vaRole['voiceActor']['name']['full'] ?? '';
    $vaImage = $vaRole['voiceActor']['image']['large'] ?? $vaRole['voiceActor']['image']['medium'] ?? '';
@endphp

<div class="flex bg-dark-800/50 light:bg-white rounded-xl overflow-hidden border border-dark-700/50 light:border-dark-200 hover:border-primary-500/30 transition-colors group">
    {{-- Character --}}
    <div class="flex items-center gap-3 flex-1 p-3">
        <img src="{{ $charImage }}" alt="{{ $charName }}" class="w-14 h-14 rounded-lg object-cover shrink-0" loading="lazy">
        <div class="min-w-0">
            <p class="text-sm font-semibold text-white light:text-dark-900 truncate">{{ $charName }}</p>
            <p class="text-xs text-dark-400 light:text-dark-500 capitalize">{{ strtolower($role) }}</p>
        </div>
    </div>

    {{-- Voice Actor --}}
    @if($vaName)
    <div class="flex items-center gap-3 flex-1 p-3 text-right border-l border-dark-700/30 light:border-dark-200">
        <div class="min-w-0 flex-1">
            <p class="text-sm font-medium text-white light:text-dark-900 truncate">{{ $vaName }}</p>
            <p class="text-xs text-dark-400 light:text-dark-500">Japanese</p>
        </div>
        <img src="{{ $vaImage }}" alt="{{ $vaName }}" class="w-14 h-14 rounded-lg object-cover shrink-0" loading="lazy">
    </div>
    @endif
</div>
