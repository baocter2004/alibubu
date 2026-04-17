@props(['paginator', 'onEachSide' => 2])

@if ($paginator->hasPages())
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mt-6">
        <div class="text-sm text-gray-600 text-center md:text-left">
            Hiển thị
            <span class="font-medium">
                {{ $paginator->firstItem() }}
            </span>
            -
            <span class="font-medium">
                {{ $paginator->lastItem() }}
            </span>
            /
            <span class="font-semibold">
                {{ $paginator->total() }}
            </span>
            kết quả
        </div>

        {{-- Pagination --}}
        <nav class="flex items-center justify-center space-x-1">

            {{-- Mobile --}}
            <div class="flex md:hidden items-center space-x-2">
                {{-- Prev --}}
                @if ($paginator->onFirstPage())
                    <span class="px-3 py-2 bg-gray-200 text-gray-400 rounded-lg">
                        <i class="fas fa-angle-left"></i>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}"
                        class="px-3 py-2 bg-white border border-gray-300 hover:border-blue-300 rounded-lg hover:bg-blue-200 hover:text-white">
                        <i class="fas fa-angle-left"></i>
                    </a>
                @endif

                {{-- Page --}}
                <span class="px-3 py-2 text-sm text-gray-700">
                    {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
                </span>

                {{-- Next --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}"
                        class="px-3 py-2 bg-white border border-gray-300 hover:border-blue-300 rounded-lg hover:bg-blue-200 hover:text-white">
                        <i class="fas fa-angle-right"></i>
                    </a>
                @else
                    <span class="px-3 py-2 bg-gray-200 text-gray-400 rounded-lg">
                        <i class="fas fa-angle-right"></i>
                    </span>
                @endif
            </div>

            {{-- Desktop --}}
            <div class="hidden md:flex items-center space-x-1">

                {{-- First --}}
                @if ($paginator->onFirstPage())
                    <span class="px-3 py-1 bg-gray-200 text-gray-400 rounded-md">
                        <i class="fas fa-angle-double-left"></i>
                    </span>
                @else
                    <a href="{{ $paginator->url(1) }}"
                        class="px-3 py-1 border border-gray-300 hover:border-blue-300 rounded-md hover:bg-blue-200 hover:text-white">
                        <i class="fas fa-angle-double-left"></i>
                    </a>
                @endif

                {{-- Prev --}}
                @if ($paginator->onFirstPage())
                    <span class="px-3 py-1 bg-gray-200 text-gray-400 rounded-md">
                        <i class="fas fa-angle-left"></i>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}"
                        class="px-3 py-1 border border-gray-300 hover:border-blue-300 rounded-md hover:bg-blue-200 hover:text-white">
                        <i class="fas fa-angle-left"></i>
                    </a>
                @endif

                @php
                    $currentPage = $paginator->currentPage();
                    $lastPage = $paginator->lastPage();

                    $start = max($currentPage - $onEachSide, 1);
                    $end = min($currentPage + $onEachSide, $lastPage);
                @endphp

                {{-- ... first --}}
                @if ($start > 1)
                    <a href="{{ $paginator->url(1) }}"
                        class="px-3 py-1 border border-gray-300 hover:border-blue-300 rounded-md hover:bg-blue-200 hover:text-white">1</a>

                    @if ($start > 2)
                        <span class="px-2">...</span>
                    @endif
                @endif

                {{-- Pages --}}
                @for ($i = $start; $i <= $end; $i++)
                    @if ($i == $currentPage)
                        <span class="px-3 py-1 bg-blue-500 text-white rounded-md">
                            {{ $i }}
                        </span>
                    @else
                        <a href="{{ $paginator->url($i) }}"
                            class="px-3 py-1 border border-gray-300 hover:border-blue-300 rounded-md hover:bg-blue-200 hover:text-white">
                            {{ $i }}
                        </a>
                    @endif
                @endfor

                {{-- ... last --}}
                @if ($end < $lastPage)
                    @if ($end < $lastPage - 1)
                        <span class="px-2">...</span>
                    @endif

                    <a href="{{ $paginator->url($lastPage) }}"
                        class="px-3 py-1 border border-gray-300 hover:border-blue-300 rounded-md hover:bg-blue-200 hover:text-white">
                        {{ $lastPage }}
                    </a>
                @endif

                {{-- Next --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}"
                        class="px-3 py-1 border border-gray-300 hover:border-blue-300 rounded-md hover:bg-blue-200 hover:text-white">
                        <i class="fas fa-angle-right"></i>
                    </a>
                @else
                    <span class="px-3 py-1 bg-gray-200 text-gray-400 rounded-md">
                        <i class="fas fa-angle-right"></i>
                    </span>
                @endif

                {{-- Last --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->url($paginator->lastPage()) }}"
                        class="px-3 py-1 border border-gray-300 hover:border-blue-300 rounded-md hover:bg-blue-200 hover:text-white">
                        <i class="fas fa-angle-double-right"></i>
                    </a>
                @else
                    <span class="px-3 py-1 bg-gray-200 text-gray-400 rounded-md">
                        <i class="fas fa-angle-double-right"></i>
                    </span>
                @endif
            </div>
        </nav>
    </div>
@endif
