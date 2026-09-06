@extends('client.layouts.app')

@section('title', __('common.app_name') . ' - ' . __('client.cart.breadcrumb'))

@section('content')
    <nav class="flex items-center gap-2 text-sm text-muted-foreground mb-6">
        <a href="{{ route('index') }}" class="hover:text-primary transition-colors">{{ __('client.nav.home') }}</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <span class="text-foreground font-medium">{{ __('client.cart.breadcrumb') }}</span>
    </nav>

    <div class="flex items-center justify-between gap-3 mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-foreground">{{ __('client.cart.title') }}</h1>
        @if ($items->isNotEmpty())
            <span class="px-3 py-1.5 text-sm font-medium bg-primary/10 text-primary rounded-full">
                {{ $items->sum('quantity') }}
            </span>
        @endif
    </div>

    @if ($items->isEmpty())
        <div class="bg-card border border-dashed border-border rounded-3xl py-20 text-center">
            <span class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-muted mb-5">
                <i class="fa-solid fa-cart-shopping text-3xl text-muted-foreground/40"></i>
            </span>
            <p class="text-lg font-semibold text-foreground mb-1">{{ __('client.cart.empty_title') }}</p>
            <p class="text-sm text-muted-foreground mb-6">{{ __('client.cart.empty_description') }}</p>
            <a href="{{ route('shop.index') }}"
                class="inline-flex items-center gap-2 px-6 py-3 text-sm font-bold btn-primary rounded-xl">
                <i class="fa-solid fa-bag-shopping"></i>
                {{ __('client.cart.empty_cta') }}
            </a>
        </div>
    @else
        <div class="grid lg:grid-cols-3 gap-6 items-start">
            <div class="lg:col-span-2 space-y-3">
                @foreach ($items as $item)
                    @php
                        $product = $item['product'];
                        $variant = $item['variant'];
                    @endphp

                    <div class="flex flex-wrap sm:flex-nowrap gap-4 bg-card border border-border rounded-2xl p-4 hover:border-primary/30 transition-colors"
                        data-cart-line data-cart-update="{{ route('cart.update', $item['key']) }}">
                        <a href="{{ route('shop.show', $product->slug) }}"
                            class="w-20 h-20 sm:w-24 sm:h-24 shrink-0 bg-white border border-border rounded-xl overflow-hidden flex items-center justify-center">
                            @if ($product->thumbnail)
                                <img src="{{ Storage::disk('public')->url($product->thumbnail) }}"
                                    alt="{{ $product->name }}" class="w-full h-full object-contain p-1.5">
                            @else
                                <i class="fa-solid fa-box-open text-2xl text-muted-foreground/25"></i>
                            @endif
                        </a>

                        <div class="flex-1 min-w-0">
                            <a href="{{ route('shop.show', $product->slug) }}"
                                class="font-medium text-foreground hover:text-primary transition-colors line-clamp-2">
                                {{ $product->name }}
                            </a>

                            @if ($variant)
                                <p class="inline-block mt-1 px-2 py-0.5 text-xs bg-muted text-muted-foreground rounded-md">
                                    {{ $variant->attributeValues->pluck('value')->implode(' / ') ?: $variant->sku }}
                                </p>
                            @endif

                            <p class="text-sm price-main mt-1.5">{{ format_price($item['price']) }}</p>

                            <div class="flex flex-wrap items-center gap-3 mt-3">
                                <form action="{{ route('cart.update', $item['key']) }}" method="POST"
                                    class="flex items-center border border-border rounded-lg overflow-hidden" data-cart-qty>
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" name="quantity" value="{{ $item['quantity'] - 1 }}"
                                        data-qty-step="-1"
                                        class="w-9 h-9 flex items-center justify-center text-muted-foreground hover:bg-muted transition-colors"
                                        aria-label="{{ __('client.product.decrease') }}">
                                        <i class="fa-solid fa-minus text-[10px]"></i>
                                    </button>
                                    <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1"
                                        max="{{ \App\Services\Client\CartService::MAX_QUANTITY }}" inputmode="numeric"
                                        aria-label="{{ __('client.product.quantity') }}"
                                        data-qty-input data-line-quantity="{{ $item['key'] }}"
                                        class="w-12 h-9 text-center text-sm font-medium tabular border-x border-border focus:outline-none">
                                    <button type="submit" name="quantity" value="{{ $item['quantity'] + 1 }}"
                                        data-qty-step="1"
                                        class="w-9 h-9 flex items-center justify-center text-muted-foreground hover:bg-muted transition-colors"
                                        aria-label="{{ __('client.product.increase') }}">
                                        <i class="fa-solid fa-plus text-[10px]"></i>
                                    </button>
                                </form>

                                <form action="{{ route('cart.destroy', $item['key']) }}" method="POST" data-cart-remove>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-xs text-muted-foreground hover:text-red-500 transition-colors">
                                        <i class="fa-regular fa-trash-can mr-1"></i>{{ __('client.cart.remove') }}
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="text-right shrink-0 ml-auto">
                            <p class="text-xs text-muted-foreground mb-1">{{ __('client.cart.line_total') }}</p>
                            <p class="font-bold text-foreground whitespace-nowrap"
                                data-line-subtotal="{{ $item['key'] }}">{{ format_price($item['subtotal']) }}</p>
                        </div>
                    </div>
                @endforeach

                <div class="flex flex-wrap justify-between items-center gap-3 pt-2">
                    <a href="{{ route('shop.index') }}"
                        class="inline-flex items-center gap-2 text-sm font-medium text-primary hover:underline">
                        <i class="fa-solid fa-arrow-left"></i> {{ __('client.cart.continue') }}
                    </a>

                    <form action="{{ route('cart.clear') }}" method="POST" data-submit-once>
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="text-sm text-muted-foreground hover:text-red-500 transition-colors">
                            <i class="fa-regular fa-trash-can mr-1"></i>{{ __('client.cart.clear') }}
                        </button>
                    </form>
                </div>
            </div>

            <aside class="bg-card border border-border rounded-2xl p-5 lg:sticky lg:top-24">
                <h2 class="font-bold text-foreground mb-4">{{ __('client.cart.summary') }}</h2>

                <dl class="space-y-3 text-sm mb-4">
                    <div class="flex justify-between">
                        <dt class="text-muted-foreground">{{ __('client.cart.subtotal') }}</dt>
                        <dd class="font-medium text-foreground" data-cart-subtotal>{{ format_price($subtotal) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-muted-foreground">{{ __('client.cart.shipping') }}</dt>
                        <dd class="font-medium text-success">{{ __('client.cart.free') }}</dd>
                    </div>
                </dl>

                @include('components.coupon-box', ['coupon' => $coupon, 'discount' => $discount])

                <div class="flex justify-between text-sm mb-4 {{ $discount > 0 ? '' : 'hidden' }}" data-cart-discount-row>
                    <span class="text-muted-foreground">{{ __('client.coupon.discount') }}</span>
                    <span class="font-medium text-success" data-cart-discount>-{{ format_price($discount) }}</span>
                </div>

                <div class="border-t border-border my-4"></div>

                <div class="flex justify-between items-baseline mb-5">
                    <span class="font-semibold text-foreground">{{ __('client.cart.total') }}</span>
                    <span class="text-2xl price-main" data-cart-total>{{ format_price($total) }}</span>
                </div>

                <a href="{{ route('checkout.index') }}"
                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 text-sm font-bold btn-accent rounded-xl">
                    {{ __('client.cart.checkout') }}
                    <i class="fa-solid fa-arrow-right"></i>
                </a>

                <div class="flex flex-col gap-2 mt-5 pt-5 border-t border-border">
                    @foreach ([['fa-truck-fast', __('client.product.benefits.shipping')], ['fa-rotate-left', __('client.product.benefits.returns')], ['fa-shield-halved', __('client.product.benefits.warranty')]] as [$icon, $label])
                        <span class="inline-flex items-center gap-2 text-xs text-muted-foreground">
                            <i class="fa-solid {{ $icon }} text-primary/70 w-4 text-center"></i>
                            {{ $label }}
                        </span>
                    @endforeach
                </div>
            </aside>
        </div>

        <div class="buy-bar lg:hidden">
            <div class="flex items-center gap-3 px-4 py-3">
                <div class="min-w-0">
                    <p class="text-[11px] text-muted-foreground">{{ __('client.cart.total') }}</p>
                    <p class="text-lg price-main truncate" data-cart-total>{{ format_price($total) }}</p>
                </div>

                <a href="{{ route('checkout.index') }}"
                    class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 text-sm font-bold btn-accent rounded-xl">
                    {{ __('client.cart.checkout') }}
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>
    @endif
@endsection
