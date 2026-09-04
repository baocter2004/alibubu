@php
    $crumbs = $crumbs ?? [];
    $actions = $actions ?? [];
@endphp

<div class="mb-6">
    @if ($crumbs)
        <nav class="flex flex-wrap items-center gap-2 text-sm text-gray-500 mb-3">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-primary transition-colors">
                <i class="fa-solid fa-house"></i>
            </a>
            @foreach ($crumbs as $crumb)
                <i class="fa-solid fa-chevron-right text-[9px] text-gray-300"></i>
                @if (! empty($crumb['url']))
                    <a href="{{ $crumb['url'] }}" class="hover:text-primary transition-colors">{{ $crumb['label'] }}</a>
                @else
                    <span class="text-gray-700 font-medium">{{ $crumb['label'] }}</span>
                @endif
            @endforeach
        </nav>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="min-w-0">
            <h1 class="text-xl md:text-2xl font-semibold text-gray-900 truncate">{{ $title }}</h1>
            @if (! empty($subtitle))
                <p class="text-sm text-gray-500 mt-0.5">{{ $subtitle }}</p>
            @endif
        </div>

        @if ($actions)
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                @foreach ($actions as $action)
                    <a href="{{ $action['url'] }}" @if (! empty($action['blank'])) target="_blank" rel="noopener" @endif
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $action['class'] ?? 'text-gray-700 bg-gray-100 hover:bg-gray-200' }}">
                        @if (! empty($action['icon']))
                            <i class="fa-solid {{ $action['icon'] }}"></i>
                        @endif
                        <span class="{{ !empty($action['hideLabelOnMobile']) ? 'hidden sm:inline' : '' }}">{{ $action['label'] }}</span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
