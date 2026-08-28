@extends('client.layouts.app')

@section('title', __('common.app_name') . ' - ' . __('client.cart.breadcrumb'))

@section('content')
    <nav class="flex items-center gap-2 text-sm text-muted-foreground mb-6">
        <a href="{{ route('index') }}" class="hover:text-primary transition-colors">{{ __('client.nav.home') }}</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <span class="text-foreground font-medium">{{ __('client.cart.breadcrumb') }}</span>
    </nav>

    <h1 class="text-2xl md:text-3xl font-bold text-foreground mb-6">{{ __('client.cart.title') }}</h1>

    @if ($items->isEmpty())
        <div class="bg-card border border-dashed border-border rounded-2xl py-20 text-center">
            <i class="fa-solid fa-cart-shopping text-5xl text-muted-foreground/25 mb-4"></i>
            <p class="text-lg font-semibold text-foreground mb-1">{{ __('client.cart.empty_title') }}</p>
            <p class="text-sm text-muted-foreground mb-6">{{ __('client.cart.empty_description') }}</p>
            <a href="{{ route('shop.index') }}"
                class="inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold text-white bg-primary rounded-xl hover:bg-primary/90 transition-colors">
                <i class="fa-solid fa-bag-shopping"></i> {{ __('client.cart.empty_cta') }}
            </a>
        </div>
    @else
        <div class="grid lg:grid-cols-3 gap-6 items-start">
            <div class="lg:col-span-2 space-y-4">
                @foreach ($items as $item)
                    @php
                        $product = $item['product'];
                        $variant = $item['variant'];
                    @endphp

                    <div class="flex gap-4 bg-card border border-border rounded-xl p-4">
                        <a href="{{ route('shop.show', $product->slug) }}"
                            class="w-20 h-20 sm:w-24 sm:h-24 shrink-0 bg-muted rounded-lg overflow-hidden flex items-center justify-center">
                            @if ($product->thumbnail)
                                <img src="{{ Storage::disk('public')->url($product->thumbnail) }}"
                                    alt="{{ $product->name }}" class="w-full h-full object-cover">
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
                                <p class="text-xs text-muted-foreground mt-1">
                                    {{ $variant->attributeValues->pluck('value')->implode(' / ') ?: $variant->sku }}
                                </p>
                            @endif

                            <p class="text-sm font-semibold text-primary mt-1">{{ format_price($item['price']) }}</p>

                            <div class="flex flex-wrap items-center gap-3 mt-3">
                                <form action="{{ route('cart.update', $item['key']) }}" method="POST"
                                    class="flex items-center border border-border rounded-lg overflow-hidden">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" name="quantity" value="{{ $item['quantity'] - 1 }}"
                                        class="w-8 h-8 flex items-center justify-center text-muted-foreground hover:bg-muted transition-colors"
                                        aria-label="{{ __('client.product.decrease') }}">
                                        <i class="fa-solid fa-minus text-[10px]"></i>
                                    </button>
                                    <span class="w-10 h-8 flex items-center justify-center text-sm border-x border-border">
                                        {{ $item['quantity'] }}
                                    </span>
                                    <button type="submit" name="quantity" value="{{ $item['quantity'] + 1 }}"
                                        class="w-8 h-8 flex items-center justify-center text-muted-foreground hover:bg-muted transition-colors"
                                        aria-label="{{ __('client.product.increase') }}"
                                        @disabled($item['quantity'] >= \App\Services\Client\CartService::MAX_QUANTITY)>
                                        <i class="fa-solid fa-plus text-[10px]"></i>
                                    </button>
                                </form>

                                <form action="{{ route('cart.destroy', $item['key']) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-xs text-muted-foreground hover:text-red-500 transition-colors">
                                        <i class="fa-regular fa-trash-can mr-1"></i>{{ __('client.cart.remove') }}
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <p class="text-xs text-muted-foreground mb-1">{{ __('client.cart.line_total') }}</p>
                            <p class="font-bold text-foreground">{{ format_price($item['subtotal']) }}</p>
                        </div>
                    </div>
                @endforeach

                <div class="flex justify-between items-center pt-1">
                    <a href="{{ route('shop.index') }}"
                        class="inline-flex items-center gap-2 text-sm font-medium text-primary hover:underline">
                        <i class="fa-solid fa-arrow-left"></i> {{ __('client.cart.continue') }}
                    </a>

                    <form action="{{ route('cart.clear') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="text-sm text-muted-foreground hover:text-red-500 transition-colors">
                            <i class="fa-regular fa-trash-can mr-1"></i>{{ __('client.cart.clear') }}
                        </button>
                    </form>
                </div>
            </div>

            <aside class="bg-card border border-border rounded-xl p-5 lg:sticky lg:top-24">
                <h2 class="font-bold text-foreground mb-4">{{ __('client.cart.summary') }}</h2>

                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-muted-foreground">{{ __('client.cart.subtotal') }}</dt>
                        <dd class="font-medium text-foreground">{{ format_price($subtotal) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-muted-foreground">{{ __('client.cart.shipping') }}</dt>
                        <dd class="font-medium text-green-600">{{ __('client.cart.free') }}</dd>
                    </div>
                </dl>

                <div class="border-t border-border my-4"></div>

                <div class="flex justify-between items-baseline mb-5">
                    <span class="font-semibold text-foreground">{{ __('client.cart.total') }}</span>
                    <span class="text-xl font-bold text-primary">{{ format_price($subtotal) }}</span>
                </div>

                <a href="{{ route('checkout.index') }}"
                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold text-white bg-primary rounded-xl hover:bg-primary/90 transition-colors">
                    {{ __('client.cart.checkout') }}
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </aside>
        </div>
    @endif
@endsection
