@props(['pageInfo' => [], 'currentPage' => 1, 'baseUrl' => ''])

@php
    $lastPage = $pageInfo['lastPage'] ?? 1;
    $hasNext = $pageInfo['hasNextPage'] ?? false;
    $currentPage = (int) $currentPage;
    
    // Build page range
    $start = max(1, $currentPage - 2);
    $end = min($lastPage, $currentPage + 2);
    
    // Get current query parameters
    $queryParams = request()->except('page');
@endphp

@if($lastPage > 1)
<nav class="flex items-center justify-center gap-2 mt-10" aria-label="Pagination">
    {{-- Previous --}}
    @if($currentPage > 1)
        <a href="{{ url()->current() . '?' . http_build_query(array_merge($queryParams, ['page' => $currentPage - 1])) }}"
           class="flex items-center gap-1 px-4 py-2 text-sm font-medium rounded-xl bg-dark-800 light:bg-white text-dark-300 light:text-dark-600 border border-dark-700 light:border-dark-200 hover:bg-dark-700 light:hover:bg-dark-50 hover:text-white light:hover:text-dark-900 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Prev
        </a>
    @endif

    {{-- Page Numbers --}}
    @if($start > 1)
        <a href="{{ url()->current() . '?' . http_build_query(array_merge($queryParams, ['page' => 1])) }}"
           class="w-10 h-10 flex items-center justify-center text-sm font-medium rounded-xl bg-dark-800 light:bg-white text-dark-300 light:text-dark-600 border border-dark-700 light:border-dark-200 hover:bg-dark-700 light:hover:bg-dark-50 transition-all">1</a>
        @if($start > 2)
            <span class="text-dark-600 px-1">…</span>
        @endif
    @endif

    @for($i = $start; $i <= $end; $i++)
        @if($i === $currentPage)
            <span class="w-10 h-10 flex items-center justify-center text-sm font-bold rounded-xl bg-gradient-to-r from-primary-600 to-primary-500 text-white shadow-lg shadow-primary-600/25">{{ $i }}</span>
        @else
            <a href="{{ url()->current() . '?' . http_build_query(array_merge($queryParams, ['page' => $i])) }}"
               class="w-10 h-10 flex items-center justify-center text-sm font-medium rounded-xl bg-dark-800 light:bg-white text-dark-300 light:text-dark-600 border border-dark-700 light:border-dark-200 hover:bg-dark-700 light:hover:bg-dark-50 transition-all">{{ $i }}</a>
        @endif
    @endfor

    @if($end < $lastPage)
        @if($end < $lastPage - 1)
            <span class="text-dark-600 px-1">…</span>
        @endif
        <a href="{{ url()->current() . '?' . http_build_query(array_merge($queryParams, ['page' => $lastPage])) }}"
           class="w-10 h-10 flex items-center justify-center text-sm font-medium rounded-xl bg-dark-800 light:bg-white text-dark-300 light:text-dark-600 border border-dark-700 light:border-dark-200 hover:bg-dark-700 light:hover:bg-dark-50 transition-all">{{ $lastPage }}</a>
    @endif

    {{-- Next --}}
    @if($hasNext)
        <a href="{{ url()->current() . '?' . http_build_query(array_merge($queryParams, ['page' => $currentPage + 1])) }}"
           class="flex items-center gap-1 px-4 py-2 text-sm font-medium rounded-xl bg-dark-800 light:bg-white text-dark-300 light:text-dark-600 border border-dark-700 light:border-dark-200 hover:bg-dark-700 light:hover:bg-dark-50 hover:text-white light:hover:text-dark-900 transition-all">
            Next
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    @endif
</nav>
@endif
