@props(['breadcrumbs' => []])

@php
    $breadcrumbs = collect($breadcrumbs);
    if ($breadcrumbs->first()['label'] !== 'Home') {
        $breadcrumbs->prepend(['url' => route('index'), 'label' => 'Home']);
    }
@endphp

<div class="bg-light py-3">
    <div class="container">
        <div class="row">
            <div class="col-md-12 mb-0">
                @foreach ($breadcrumbs as $breadcrumb)
                    @if (!$loop->last)
                        <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['label'] }}</a>
                        <span class="mx-2 mb-0">/</span>
                    @else
                        <strong class="text-black">{{ $breadcrumb['label'] }}</strong>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
