@php
    $price = $product->effective_price;
    $base = $product->base_price;
    $discount = $product->discount_percent;
    $url = route('shop.show', $product->slug);
    $sellableVariants = $product->hasVariants()
        ? $product->variants->where('is_active', true)->count()
        : 1;
    $outOfStock = ! $product->inStock() || $sellableVariants === 0;
    $reveal = $reveal ?? false;
    $compared = app(\App\Services\Client\CompareService::class)->has($product->id);
    $wishlisted = Auth::check() && Auth::user()->hasWishlisted($product->id);
@endphp

<article class="group relative flex flex-col min-w-0 card-surface card-interactive overflow-hidden {{ $reveal ? 'reveal' : '' }}">
    <a href="{{ $url }}" class="relative block aspect-square bg-white overflow-hidden">
        @if ($product->thumbnail)
            <img src="{{ Storage::disk('public')->url($product->thumbnail) }}" alt="{{ $product->name }}"
                loading="lazy" width="400" height="400"
                class="w-full h-full object-contain p-5 transition-transform duration-500 ease-out group-hover:scale-105">
        @else
            <span class="w-full h-full flex items-center justify-center bg-muted/50">
                <i class="fa-solid fa-box-open text-5xl text-muted-foreground/20"></i>
            </span>
        @endif

        <span class="absolute top-3 left-3 flex flex-col gap-1.5 items-start z-10">
            @if ($discount > 0)
                <span class="px-2 py-1 text-[11px] font-bold badge-sale tabular">-{{ $discount }}%</span>
            @endif
            @if ($product->is_trending)
                <span class="px-2 py-1 text-[10px] font-bold badge-flag bg-accent text-accent-foreground">
                    <i class="fa-solid fa-fire"></i>
                </span>
            @endif
        </span>

        @if ($outOfStock)
            <span class="absolute inset-0 bg-white/75 backdrop-blur-[1px] flex items-center justify-center">
                <span class="px-3.5 py-1.5 text-xs font-bold text-white bg-foreground/85 rounded-lg">
                    {{ __('client.product.out_of_stock') }}
                </span>
            </span>
        @endif
    </a>

    <div class="absolute top-3 right-3 z-20 flex flex-col gap-1.5">
        @auth
            <form action="{{ route('shop.wishlist.toggle', $product->slug) }}" method="POST" data-wishlist-toggle>
                @csrf
                <button type="submit" aria-pressed="{{ $wishlisted ? 'true' : 'false' }}"
                    title="{{ $wishlisted ? __('client.wishlist.remove') : __('client.wishlist.add') }}"
                    data-wishlist-on="bg-red-50 text-red-600 border-red-200"
                    data-wishlist-off="bg-white/95 text-muted-foreground border-border"
                    class="w-9 h-9 flex items-center justify-center rounded-full border shadow-sm hover:text-red-600 hover:border-red-200 transition-colors {{ $wishlisted ? 'bg-red-50 text-red-600 border-red-200' : 'bg-white/95 text-muted-foreground border-border' }}">
                    <i class="fa-{{ $wishlisted ? 'solid' : 'regular' }} fa-heart text-sm"></i>
                </button>
            </form>
        @else
            <a href="{{ route('auth.client.showFormLogin') }}" title="{{ __('client.wishlist.add') }}"
                class="w-9 h-9 flex items-center justify-center rounded-full border border-border bg-white/95 text-muted-foreground shadow-sm hover:text-red-600 hover:border-red-200 transition-colors">
                <i class="fa-regular fa-heart text-sm"></i>
            </a>
        @endauth

        <form action="{{ route('shop.compare.toggle', $product->slug) }}" method="POST" data-compare-toggle>
            @csrf
            <button type="submit" aria-pressed="{{ $compared ? 'true' : 'false' }}"
                title="{{ $compared ? __('client.compare.remove') : __('client.compare.add') }}"
                class="w-9 h-9 flex items-center justify-center rounded-full border shadow-sm hover:text-primary hover:border-primary/40 transition-colors {{ $compared ? 'bg-primary text-white border-primary' : 'bg-white/95 text-muted-foreground border-border' }}">
                <i class="fa-solid fa-code-compare text-sm"></i>
            </button>
        </form>
    </div>

    <div class="flex flex-col flex-1 p-4 pt-3.5">
        @if ($product->branch)
            <span class="text-[11px] font-bold uppercase tracking-[0.1em] text-muted-foreground mb-1.5">
                {{ $product->branch->name }}
            </span>
        @endif

        <a href="{{ $url }}"
            class="text-sm font-semibold text-foreground line-clamp-2 mb-2.5 leading-snug hover:text-primary transition-colors">
            {{ $product->name }}
        </a>

        <div class="flex flex-wrap items-center gap-x-2 gap-y-1 mb-3">
            @include('components.rating', ['rating' => $product->rating, 'showValue' => true])
            <span class="text-xs text-muted-foreground truncate">
                {{ __('client.product.sold', ['count' => number_format($product->sold)]) }}
            </span>
        </div>

        <div class="flex flex-wrap items-baseline gap-2 mb-3.5 mt-auto">
            <span class="text-lg price-main">{{ format_price($price) }}</span>
            @if ($discount > 0)
                <span class="text-xs text-muted-foreground line-through tabular">{{ format_price($base) }}</span>
            @endif
        </div>

        @if ($product->hasVariants())
            <a href="{{ $url }}"
                class="w-full inline-flex items-center justify-center gap-2 px-3 py-2.5 text-sm font-bold btn-outline rounded-xl">
                <i class="fa-solid fa-sliders text-xs"></i>
                {{ __('client.product.choose') }}
            </a>
        @elseif ($outOfStock)
            <span
                class="w-full inline-flex items-center justify-center gap-2 px-3 py-2.5 text-sm font-semibold text-muted-foreground bg-muted rounded-xl cursor-not-allowed">
                {{ __('client.product.out_of_stock') }}
            </span>
        @else
            <form action="{{ route('cart.store') }}" method="POST" data-cart-add>
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 px-3 py-2.5 text-sm font-bold btn-accent rounded-xl">
                    <i class="fa-solid fa-cart-plus text-xs"></i>
                    {{ __('client.product.add_to_cart') }}
                </button>
            </form>
        @endif
    </div>
</article>
