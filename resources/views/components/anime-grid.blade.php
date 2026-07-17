@props(['anime' => [], 'cols' => 6])

@php
    $gridCols = match((int)$cols) {
        4 => 'grid-cols-2 sm:grid-cols-3 md:grid-cols-4',
        5 => 'grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5',
        6 => 'grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6',
        default => 'grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6',
    };
@endphp

<div class="grid {{ $gridCols }} gap-4 md:gap-6">
    @forelse($anime as $index => $item)
        <div class="animate-fade-in" style="animation-delay: {{ $index * 0.05 }}s; opacity: 0">
            <x-anime-card :anime="$item" />
        </div>
    @empty
        <div class="col-span-full text-center py-16">
            <svg class="w-16 h-16 mx-auto text-dark-700 light:text-dark-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.182 16.318A4.486 4.486 0 0012.016 15a4.486 4.486 0 00-3.198 1.318M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z"/>
            </svg>
            <p class="text-dark-500 light:text-dark-400 text-lg font-medium">No anime found</p>
            <p class="text-dark-600 light:text-dark-300 text-sm mt-1">Try adjusting your search or filters</p>
        </div>
    @endforelse
</div>
