@props(['title', 'link' => null, 'linkText' => 'View All'])

<div class="flex items-center justify-between mb-6" data-animate>
    <h2 class="text-2xl sm:text-3xl font-bold text-white light:text-dark-900">
        {{ $title }}
    </h2>
    @if($link)
        <a href="{{ $link }}" class="flex items-center gap-1 text-sm font-medium text-primary-400 hover:text-primary-300 transition-colors group">
            {{ $linkText }}
            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    @endif
</div>
