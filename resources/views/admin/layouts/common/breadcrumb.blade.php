@props(['breadcrumbs' => []])

@php
    $breadcrumbs = collect($breadcrumbs);
    if ($breadcrumbs->isEmpty() || $breadcrumbs->first()['label'] !== 'Dashboard') {
        $breadcrumbs->prepend(['url' => route('admin.dashboard'), 'label' => 'Dashboard']);
    }
@endphp

<div class="pagetitle">
    @foreach ($breadcrumbs as $breadcrumb)
        @if ($loop->last)
            <h1>{{ $breadcrumb['label'] }}</h1>
        @endif
    @endforeach

    <nav>
        <ol class="breadcrumb">
            @foreach ($breadcrumbs as $breadcrumb)
                @if ($loop->last)
                    <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb['label'] }}</li>
                @else
                    <li class="breadcrumb-item">
                        <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['label'] }}</a>
                    </li>
                @endif
            @endforeach
        </ol>
    </nav>
</div>
