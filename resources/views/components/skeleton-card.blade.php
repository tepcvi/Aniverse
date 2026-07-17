@props(['count' => 6])

@for($i = 0; $i < $count; $i++)
<div class="animate-pulse">
    <div class="rounded-xl overflow-hidden bg-dark-800 light:bg-dark-200">
        <div class="aspect-[3/4] animate-shimmer"></div>
        <div class="p-3 space-y-2">
            <div class="h-4 bg-dark-700 light:bg-dark-300 rounded w-3/4"></div>
            <div class="h-3 bg-dark-700 light:bg-dark-300 rounded w-1/2"></div>
        </div>
    </div>
</div>
@endfor
