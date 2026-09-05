@php
    $value = (float) ($rating ?? 0);
    $size = $size ?? 'text-xs';
    $showValue = $showValue ?? true;
@endphp

<span class="inline-flex shrink-0 items-center gap-1 {{ $size }}">
    <span class="inline-flex items-center text-amber-400">
        @for ($i = 1; $i <= 5; $i++)
            @if ($value >= $i)
                <i class="fa-solid fa-star"></i>
            @elseif ($value >= $i - 0.5)
                <i class="fa-solid fa-star-half-stroke"></i>
            @else
                <i class="fa-regular fa-star text-muted-foreground/40"></i>
            @endif
        @endfor
    </span>

    @if ($showValue)
        <span class="text-muted-foreground">{{ number_format($value, 1) }}</span>
    @endif
</span>
