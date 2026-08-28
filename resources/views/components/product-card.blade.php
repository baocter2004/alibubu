@php
    $price = $product->effective_price;
    $base = $product->base_price;
    $discount = $product->discount_percent;
    $url = route('shop.show', $product->slug);
    $sellableVariants = $product->hasVariants()
        ? $product->variants->where('is_active', true)->count()
        : 1;
    $outOfStock = ! $product->inStock() || $sellableVariants === 0;
@endphp

<article
    class="group relative flex flex-col bg-card border border-border rounded-2xl overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:border-primary/40">
    <a href="{{ $url }}" class="relative block aspect-square bg-white overflow-hidden">
        @if ($product->thumbnail)
            <img src="{{ Storage::disk('public')->url($product->thumbnail) }}" alt="{{ $product->name }}"
                loading="lazy"
                class="w-full h-full object-contain p-4 transition-transform duration-500 group-hover:scale-105">
        @else
            <span class="w-full h-full flex items-center justify-center bg-muted">
                <i class="fa-solid fa-box-open text-5xl text-muted-foreground/25"></i>
            </span>
        @endif

        <span class="absolute top-2.5 left-2.5 flex flex-col gap-1.5 items-start">
            @if ($discount > 0)
                <span class="px-2 py-0.5 text-[11px] font-bold bg-red-500 text-white rounded-full shadow-sm">
                    -{{ $discount }}%
                </span>
            @endif
            @if ($product->is_trending)
                <span class="px-2 py-0.5 text-[11px] font-semibold bg-amber-400 text-amber-950 rounded-full shadow-sm">
                    <i class="fa-solid fa-fire"></i>
                </span>
            @endif
        </span>

        @if ($outOfStock)
            <span class="absolute inset-0 bg-white/70 flex items-center justify-center">
                <span class="px-3 py-1.5 text-xs font-bold text-foreground bg-white rounded-full shadow">
                    {{ __('client.product.out_of_stock') }}
                </span>
            </span>
        @endif
    </a>

    <div class="flex flex-col flex-1 p-4">
        @if ($product->branch)
            <span class="text-[11px] font-semibold uppercase tracking-wider text-primary/70 mb-1">
                {{ $product->branch->name }}
            </span>
        @endif

        <a href="{{ $url }}"
            class="text-sm font-medium text-foreground line-clamp-2 mb-2 leading-snug hover:text-primary transition-colors">
            {{ $product->name }}
        </a>

        <div class="flex items-center gap-2 mb-2.5">
            @include('components.rating', ['rating' => $product->rating, 'showValue' => true])
            <span class="text-xs text-muted-foreground">·</span>
            <span class="text-xs text-muted-foreground">
                {{ __('client.product.sold', ['count' => number_format($product->sold)]) }}
            </span>
        </div>

        <div class="flex flex-wrap items-baseline gap-2 mb-3 mt-auto">
            <span class="text-lg font-bold text-primary">{{ format_price($price) }}</span>
            @if ($discount > 0)
                <span class="text-xs text-muted-foreground line-through">{{ format_price($base) }}</span>
            @endif
        </div>

        @if ($product->hasVariants())
            <a href="{{ $url }}"
                class="w-full inline-flex items-center justify-center gap-2 px-3 py-2.5 text-sm font-semibold text-primary border border-primary/40 rounded-xl hover:bg-primary hover:text-white transition-colors">
                <i class="fa-solid fa-sliders"></i>
                {{ __('client.product.choose') }}
            </a>
        @elseif ($outOfStock)
            <span
                class="w-full inline-flex items-center justify-center gap-2 px-3 py-2.5 text-sm font-semibold text-muted-foreground bg-muted rounded-xl cursor-not-allowed">
                {{ __('client.product.out_of_stock') }}
            </span>
        @else
            <form action="{{ route('cart.store') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 px-3 py-2.5 text-sm font-semibold text-white bg-primary rounded-xl hover:bg-primary/90 transition-colors">
                    <i class="fa-solid fa-cart-plus"></i>
                    {{ __('client.product.add_to_cart') }}
                </button>
            </form>
        @endif
    </div>
</article>
