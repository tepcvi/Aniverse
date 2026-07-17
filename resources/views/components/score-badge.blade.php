@props(['score'])

@php
    $score = (int) $score;
    $colorClass = match(true) {
        $score >= 75 => 'from-emerald-500 to-emerald-600 shadow-emerald-500/30',
        $score >= 50 => 'from-amber-500 to-amber-600 shadow-amber-500/30',
        default => 'from-red-500 to-red-600 shadow-red-500/30',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 px-2 py-1 text-xs font-bold rounded-lg bg-gradient-to-r {$colorClass} text-white shadow-lg"]) }}>
    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
    {{ $score }}%
</span>
