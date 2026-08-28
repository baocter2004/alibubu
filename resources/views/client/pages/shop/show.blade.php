@extends('client.layouts.app')

@section('title', $product->name . ' - ' . __('common.app_name'))

@section('content')
    @php
        $variants = $product->variants->where('is_active', true)->values();
        $hasVariants = $product->hasVariants() && $variants->isNotEmpty();
        $defaultVariant = $variants->sortBy('effective_price')->first();
        $price = $hasVariants ? $defaultVariant->effective_price : $product->effective_price;
        $base = $hasVariants ? (float) $defaultVariant->price : $product->base_price;
    @endphp

    <nav class="flex flex-wrap items-center gap-2 text-sm text-muted-foreground mb-6">
        <a href="{{ route('index') }}" class="hover:text-primary transition-colors">{{ __('client.nav.home') }}</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <a href="{{ route('shop.index') }}" class="hover:text-primary transition-colors">{{ __('client.shop.breadcrumb') }}</a>
        @if ($category = $product->categories->first())
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <a href="{{ route('shop.index', ['category_id' => $category->id]) }}"
                class="hover:text-primary transition-colors">{{ $category->name }}</a>
        @endif
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <span class="text-foreground font-medium line-clamp-1">{{ $product->name }}</span>
    </nav>

    <div class="grid lg:grid-cols-2 gap-8 mb-12">
        <div>
            <div class="aspect-square bg-muted border border-border rounded-2xl overflow-hidden flex items-center justify-center">
                @if ($product->thumbnail)
                    <img id="gallery-main" src="{{ Storage::disk('public')->url($product->thumbnail) }}"
                        alt="{{ $product->name }}" class="w-full h-full object-cover">
                @else
                    <i class="fa-solid fa-box-open text-7xl text-muted-foreground/25"></i>
                @endif
            </div>

            @if ($product->galleries->isNotEmpty())
                <div class="grid grid-cols-4 gap-3 mt-3">
                    @foreach ($product->galleries as $gallery)
                        <div
                            class="aspect-square bg-muted border border-border rounded-xl overflow-hidden flex items-center justify-center hover:border-primary transition-colors">
                            @if ($gallery->image)
                                <img src="{{ Storage::disk('public')->url($gallery->image) }}"
                                    alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @else
                                <i class="fa-regular fa-image text-2xl text-muted-foreground/25"></i>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div>
            <div class="flex flex-wrap items-center gap-2 mb-3">
                @if ($product->branch)
                    <a href="{{ route('shop.index', ['branch_id' => $product->branch_id]) }}"
                        class="px-2.5 py-1 text-xs font-semibold rounded-full bg-primary/10 text-primary hover:bg-primary/20 transition-colors">
                        {{ $product->branch->name }}
                    </a>
                @endif
                @foreach ($product->tags as $tag)
                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-muted text-muted-foreground">
                        {{ $tag->name }}
                    </span>
                @endforeach
            </div>

            <h1 class="text-2xl md:text-3xl font-bold text-foreground leading-snug mb-3">{{ $product->name }}</h1>

            <div class="flex items-center gap-4 text-sm text-muted-foreground mb-5">
                <span><i class="fa-solid fa-eye mr-1"></i>{{ __('client.product.views', ['count' => number_format($product->views)]) }}</span>
                @if ($product->sku)
                    <span><i class="fa-solid fa-barcode mr-1"></i>{{ __('client.product.sku') }}: {{ $product->sku }}</span>
                @endif
            </div>

            <div class="bg-muted/60 border border-border rounded-xl p-5 mb-6">
                <div class="flex flex-wrap items-baseline gap-3">
                    <span id="price-display" class="text-3xl font-bold text-primary">{{ format_price($price) }}</span>
                    @if ($base > $price)
                        <span id="base-display"
                            class="text-base text-muted-foreground line-through">{{ format_price($base) }}</span>
                        <span class="px-2 py-0.5 text-xs font-bold bg-red-500 text-white rounded-full">
                            -{{ (int) round((($base - $price) / $base) * 100) }}%
                        </span>
                    @endif
                </div>
            </div>

            @if ($product->short_descriptions)
                <p class="text-muted-foreground leading-relaxed mb-6">{{ $product->short_descriptions }}</p>
            @endif

            <form action="{{ route('cart.store') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                @if ($hasVariants)
                    <div>
                        <p class="text-sm font-semibold text-foreground mb-2">{{ __('client.product.select_variant') }}</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($variants as $index => $variant)
                                @php
                                    $label = $variant->attributeValues->pluck('value')->implode(' / ') ?: $variant->sku;
                                @endphp
                                <label class="cursor-pointer">
                                    <input type="radio" name="product_variant_id" value="{{ $variant->id }}"
                                        data-price="{{ $variant->effective_price }}"
                                        data-base="{{ $variant->price }}" class="peer sr-only variant-option"
                                        @checked($variant->id === $defaultVariant->id)>
                                    <span
                                        class="inline-flex items-center px-4 py-2 text-sm font-medium border border-border rounded-lg text-muted-foreground transition-all peer-checked:border-primary peer-checked:bg-primary/5 peer-checked:text-primary hover:border-primary/50">
                                        {{ $label }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        @error('product_variant_id')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <div class="flex flex-wrap items-center gap-4">
                    <div class="flex items-center border border-border rounded-lg overflow-hidden">
                        <button type="button" id="qty-minus"
                            class="w-10 h-10 flex items-center justify-center text-muted-foreground hover:bg-muted transition-colors"
                            aria-label="{{ __('client.product.decrease') }}">
                            <i class="fa-solid fa-minus text-xs"></i>
                        </button>
                        <input type="number" id="quantity" name="quantity" value="1" min="1"
                            max="{{ \App\Services\Client\CartService::MAX_QUANTITY }}"
                            class="w-14 h-10 text-center border-x border-border focus:outline-none">
                        <button type="button" id="qty-plus"
                            class="w-10 h-10 flex items-center justify-center text-muted-foreground hover:bg-muted transition-colors"
                            aria-label="{{ __('client.product.increase') }}">
                            <i class="fa-solid fa-plus text-xs"></i>
                        </button>
                    </div>

                    <button type="submit"
                        class="flex-1 min-w-40 inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold text-white bg-primary rounded-xl hover:bg-primary/90 transition-colors">
                        <i class="fa-solid fa-cart-plus"></i>
                        {{ __('client.product.add_to_cart') }}
                    </button>
                </div>
            </form>

            <div class="grid grid-cols-3 gap-3 mt-7">
                @foreach ([['fa-truck-fast', __('client.product.benefits.shipping')], ['fa-rotate-left', __('client.product.benefits.returns')], ['fa-shield-halved', __('client.product.benefits.warranty')]] as [$icon, $label])
                    <div class="flex flex-col items-center gap-2 p-3 bg-card border border-border rounded-xl text-center">
                        <i class="fa-solid {{ $icon }} text-primary"></i>
                        <span class="text-xs text-muted-foreground">{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @if ($product->descriptions)
        <section class="bg-card border border-border rounded-2xl p-6 md:p-8 mb-12">
            <h2 class="text-lg font-bold text-foreground mb-4">{{ __('client.product.description') }}</h2>
            <p class="text-muted-foreground leading-relaxed whitespace-pre-line">{{ $product->descriptions }}</p>
        </section>
    @endif

    @if ($relatedProducts->isNotEmpty())
        <section class="mb-12">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-xl font-bold text-foreground">{{ __('client.product.related') }}</h2>
                <a href="{{ route('shop.index') }}" class="text-sm font-medium text-primary hover:underline">{{ __('common.actions.view_all') }}</a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($relatedProducts as $related)
                    <x-product-card :product="$related" />
                @endforeach
            </div>
        </section>
    @endif
@endsection

@push('scripts')
    <script>
        $(function() {
            const $qty = $('#quantity');
            const max = parseInt($qty.attr('max'), 10);

            $('#qty-minus').on('click', function() {
                $qty.val(Math.max(1, parseInt($qty.val(), 10) - 1));
            });

            $('#qty-plus').on('click', function() {
                $qty.val(Math.min(max, parseInt($qty.val(), 10) + 1));
            });

            $('.variant-option').on('change', function() {
                const price = parseFloat($(this).data('price'));
                const base = parseFloat($(this).data('base'));

                $('#price-display').text(formatPrice(price));

                if (base > price) {
                    $('#base-display').text(formatPrice(base)).show();
                } else {
                    $('#base-display').hide();
                }
            });

            function formatPrice(value) {
                return new Intl.NumberFormat('vi-VN').format(value) + 'đ';
            }
        });
    </script>
@endpush
