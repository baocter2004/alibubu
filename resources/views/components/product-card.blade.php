@props(['product'])

@php
    $price = $product->effective_price;
    $base = $product->base_price;
    $discount = $product->discount_percent;
    $url = route('shop.show', $product->slug);
@endphp

<article
    class="group flex flex-col bg-card border border-border rounded-xl overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:border-primary/40">
    <a href="{{ $url }}" class="relative block aspect-square bg-muted overflow-hidden">
        @if ($product->thumbnail)
            <img src="{{ Storage::disk('public')->url($product->thumbnail) }}" alt="{{ $product->name }}"
                loading="lazy" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
        @else
            <span class="w-full h-full flex items-center justify-center">
                <i class="fa-solid fa-box-open text-5xl text-muted-foreground/25"></i>
            </span>
        @endif

        @if ($discount > 0)
            <span class="absolute top-2 left-2 px-2 py-0.5 text-[11px] font-bold bg-red-500 text-white rounded-full">
                -{{ $discount }}%
            </span>
        @endif

        @if ($product->is_trending)
            <span
                class="absolute top-2 right-2 px-2 py-0.5 text-[11px] font-semibold bg-white/90 text-primary rounded-full">
                <i class="fa-solid fa-fire"></i>
            </span>
        @endif
    </a>

    <div class="flex flex-col flex-1 p-3">
        @if ($product->branch)
            <span class="text-[11px] font-medium uppercase tracking-wide text-muted-foreground mb-1">
                {{ $product->branch->name }}
            </span>
        @endif

        <a href="{{ $url }}"
            class="text-sm font-medium text-foreground line-clamp-2 mb-2 hover:text-primary transition-colors">
            {{ $product->name }}
        </a>

        <div class="flex items-baseline gap-2 mb-3 mt-auto">
            <span class="font-bold text-primary">{{ format_price($price) }}</span>
            @if ($discount > 0)
                <span class="text-xs text-muted-foreground line-through">{{ format_price($base) }}</span>
            @endif
        </div>

        <div class="flex items-center justify-between gap-2">
            <span class="flex items-center gap-1 text-xs text-muted-foreground">
                <i class="fa-solid fa-eye"></i>{{ number_format($product->views) }}
            </span>

            @if ($product->hasVariants())
                <a href="{{ $url }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-primary border border-primary/30 rounded-lg hover:bg-primary hover:text-white transition-colors">
                    <i class="fa-solid fa-sliders"></i> {{ __('client.product.choose') }}
                </a>
            @else
                <form action="{{ route('cart.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors">
                        <i class="fa-solid fa-cart-plus"></i> {{ __('client.product.add') }}
                    </button>
                </form>
            @endif
        </div>
    </div>
</article>
