@props(['paginator', 'onEachSide' => 2])

@if ($paginator->hasPages())
    <nav class="flex items-center justify-center space-x-2 mt-10">
        {{-- First Page Link a --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-1 border border-gray-300 bg-gray-200 rounded-md text-gray-500">
                <i class="fas fa-angle-double-left"></i>
            </span>
        @else
            <a href="{{ $paginator->url(1) }}"
                class="px-3 py-1 border border-gray-300 rounded-md text-gray-500 hover:text-gray-700">
                <i class="fas fa-angle-double-left"></i>
            </a>
        @endif

        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-1 border border-gray-300 bg-gray-200 rounded-md text-gray-500">
                <i class="fas fa-angle-left"></i>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
                class="px-3 py-1 border border-gray-300 rounded-md text-gray-500 hover:text-gray-700">
                <i class="fas fa-angle-left"></i>
            </a>
        @endif

        {{-- Pagination Elements --}}
        @php
            $currentPage = $paginator->currentPage();
            $lastPage = $paginator->lastPage();

            $start = max($currentPage - $onEachSide, 1);
            $end = min($currentPage + $onEachSide, $lastPage);
        @endphp

        @if ($start > 1)
            <a href="{{ $paginator->url(1) }}"
                class="px-3 py-1 border border-gray-300 rounded-md text-gray-500 hover:text-gray-700">
                1
            </a>
            @if ($start > 2)
                <span class="px-3 py-1 border border-gray-300 rounded-md text-gray-500">
                    ...
                </span>
            @endif
        @endif

        @for ($i = $start; $i <= $end; $i++)
            @if ($i == $currentPage)
                <span class="px-3 py-1 border border-gray-300 rounded-md bg-blue-500 text-white">
                    {{ $i }}
                </span>
            @else
                <a href="{{ $paginator->url($i) }}"
                    class="px-3 py-1 border border-gray-300 rounded-md text-gray-500 hover:text-gray-700">
                    {{ $i }}
                </a>
            @endif
        @endfor

        @if ($end < $lastPage)
            @if ($end < $lastPage - 1)
                <span class="px-3 py-1 border border-gray-300 rounded-md text-gray-500">
                    ...
                </span>
            @endif
            <a href="{{ $paginator->url($lastPage) }}"
                class="px-3 py-1 border border-gray-300 rounded-md text-gray-500 hover:text-gray-700">
                {{ $lastPage }}
            </a>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
                class="px-3 py-1 border border-gray-300 rounded-md text-gray-500 hover:text-gray-700">
                <i class="fas fa-angle-right"></i>
            </a>
        @else
            <span class="px-3 py-1 border border-gray-300 bg-gray-200 rounded-md text-gray-500">
                <i class="fas fa-angle-right"></i>
            </span>
        @endif

        {{-- Last Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->url($paginator->lastPage()) }}"
                class="px-3 py-1 border border-gray-300 rounded-md text-gray-500 hover:text-gray-700">
                <i class="fas fa-angle-double-right"></i>
            </a>
        @else
            <span class="px-3 py-1 border border-gray-300 bg-gray-200 rounded-md text-gray-500">
                <i class="fas fa-angle-double-right"></i>
            </span>
        @endif
    </nav>
@endif
