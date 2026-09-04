@props(['paginator', 'onEachSide' => 2])

@php
    $box = 'inline-flex items-center justify-center min-w-9 h-9 px-3 text-sm rounded-lg transition-colors';
    $link = $box . ' text-foreground bg-card border border-border hover:border-primary/35 hover:bg-primary-soft';
    $disabled = $box . ' text-muted-foreground/45 bg-muted border border-transparent cursor-not-allowed';
    $current = $box . ' font-bold text-white bg-primary border border-primary tabular';
@endphp

@if ($paginator->hasPages())
    <nav class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mt-8"
        aria-label="{{ __('common.pagination.showing') }}">
        <p class="text-sm text-muted-foreground text-center md:text-left">
            {{ __('common.pagination.showing') }}
            <span class="font-semibold text-foreground tabular">{{ $paginator->firstItem() }}</span>
            &ndash;
            <span class="font-semibold text-foreground tabular">{{ $paginator->lastItem() }}</span>
            /
            <span class="font-semibold text-foreground tabular">{{ $paginator->total() }}</span>
            {{ __('common.pagination.results') }}
        </p>

        <div class="flex md:hidden items-center justify-center gap-2">
            @if ($paginator->onFirstPage())
                <span class="{{ $disabled }}" aria-hidden="true"><i class="fa-solid fa-angle-left"></i></span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $link }}">
                    <i class="fa-solid fa-angle-left"></i>
                </a>
            @endif

            <span class="text-sm text-muted-foreground tabular px-2">
                {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $link }}">
                    <i class="fa-solid fa-angle-right"></i>
                </a>
            @else
                <span class="{{ $disabled }}" aria-hidden="true"><i class="fa-solid fa-angle-right"></i></span>
            @endif
        </div>

        <div class="hidden md:flex items-center justify-center gap-1.5">
            @if ($paginator->onFirstPage())
                <span class="{{ $disabled }}" aria-hidden="true"><i class="fa-solid fa-angles-left"></i></span>
                <span class="{{ $disabled }}" aria-hidden="true"><i class="fa-solid fa-angle-left"></i></span>
            @else
                <a href="{{ $paginator->url(1) }}" class="{{ $link }}"><i class="fa-solid fa-angles-left"></i></a>
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $link }}">
                    <i class="fa-solid fa-angle-left"></i>
                </a>
            @endif

            @php
                $currentPage = $paginator->currentPage();
                $lastPage = $paginator->lastPage();
                $start = max($currentPage - $onEachSide, 1);
                $end = min($currentPage + $onEachSide, $lastPage);
            @endphp

            @if ($start > 1)
                <a href="{{ $paginator->url(1) }}" class="{{ $link }} tabular">1</a>
                @if ($start > 2)
                    <span class="px-1 text-muted-foreground">&hellip;</span>
                @endif
            @endif

            @for ($i = $start; $i <= $end; $i++)
                @if ($i == $currentPage)
                    <span class="{{ $current }}" aria-current="page">{{ $i }}</span>
                @else
                    <a href="{{ $paginator->url($i) }}" class="{{ $link }} tabular">{{ $i }}</a>
                @endif
            @endfor

            @if ($end < $lastPage)
                @if ($end < $lastPage - 1)
                    <span class="px-1 text-muted-foreground">&hellip;</span>
                @endif
                <a href="{{ $paginator->url($lastPage) }}" class="{{ $link }} tabular">{{ $lastPage }}</a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $link }}">
                    <i class="fa-solid fa-angle-right"></i>
                </a>
                <a href="{{ $paginator->url($paginator->lastPage()) }}" class="{{ $link }}">
                    <i class="fa-solid fa-angles-right"></i>
                </a>
            @else
                <span class="{{ $disabled }}" aria-hidden="true"><i class="fa-solid fa-angle-right"></i></span>
                <span class="{{ $disabled }}" aria-hidden="true"><i class="fa-solid fa-angles-right"></i></span>
            @endif
        </div>
    </nav>
@endif
