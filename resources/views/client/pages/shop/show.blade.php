@extends('client.layouts.app')

@section('title', $product->name . ' - ' . __('common.app_name'))

@section('content')
    @php
        $variants = $product->variants->where('is_active', true)->values();
        $hasVariants = $product->hasVariants() && $variants->isNotEmpty();
        $defaultVariant = $variants->sortBy('effective_price')->first();
        $price = $hasVariants ? $defaultVariant->effective_price : $product->effective_price;
        $base = $hasVariants ? (float) $defaultVariant->price : $product->base_price;
        $images = collect([$product->thumbnail])
            ->merge($product->galleries->pluck('image'))
            ->filter()
            ->unique()
            ->values();
        $noSellableVariant = $product->hasVariants() && $variants->isEmpty();
        $outOfStock = ! $product->inStock() || $noSellableVariant;
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
        <div class="lg:sticky lg:top-24 lg:self-start">
            <div
                class="aspect-square bg-white border border-border rounded-2xl overflow-hidden flex items-center justify-center">
                @if ($images->isNotEmpty())
                    <img id="gallery-main" src="{{ Storage::disk('public')->url($images->first()) }}"
                        alt="{{ $product->name }}" class="w-full h-full object-contain p-6 transition-opacity duration-200">
                @else
                    <i class="fa-solid fa-box-open text-7xl text-muted-foreground/25"></i>
                @endif
            </div>

            @if ($images->count() > 1)
                <div class="grid grid-cols-5 gap-3 mt-3">
                    @foreach ($images as $index => $image)
                        <button type="button"
                            class="gallery-thumb aspect-square bg-white border-2 rounded-xl overflow-hidden transition-all {{ $index === 0 ? 'border-primary' : 'border-border hover:border-primary/50' }}"
                            data-src="{{ Storage::disk('public')->url($image) }}">
                            <img src="{{ Storage::disk('public')->url($image) }}" alt="{{ $product->name }}"
                                loading="lazy" class="w-full h-full object-contain p-1.5">
                        </button>
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

            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-muted-foreground mb-5">
                @include('components.rating', ['rating' => $product->rating, 'size' => 'text-sm'])
                <span>·</span>
                <span>{{ __('client.product.sold', ['count' => number_format($product->sold)]) }}</span>
                <span>·</span>
                <span><i class="fa-solid fa-eye mr-1"></i>{{ number_format($product->views) }}</span>
                @if ($product->sku)
                    <span>·</span>
                    <span>{{ __('client.product.sku') }}: {{ $product->sku }}</span>
                @endif
            </div>

            <div class="bg-gradient-to-br from-primary/5 to-primary/10 border border-primary/20 rounded-2xl p-5 mb-6">
                <div class="flex flex-wrap items-baseline gap-3">
                    <span id="price-display" class="text-3xl font-bold text-primary">{{ format_price($price) }}</span>
                    <span id="base-display"
                        class="text-base text-muted-foreground line-through {{ $base > $price ? '' : 'hidden' }}">
                        {{ format_price($base) }}
                    </span>
                    @if ($base > $price)
                        <span id="discount-badge"
                            class="px-2 py-0.5 text-xs font-bold bg-red-500 text-white rounded-full">
                            -{{ (int) round((($base - $price) / $base) * 100) }}%
                        </span>
                    @endif
                </div>

                <p class="mt-2 text-sm {{ $outOfStock ? 'text-red-600' : 'text-green-600' }}">
                    <i class="fa-solid {{ $outOfStock ? 'fa-circle-xmark' : 'fa-circle-check' }} mr-1"></i>
                    {{ $outOfStock ? __('client.product.out_of_stock') : __('client.product.stock_left', ['count' => number_format($product->stock)]) }}
                </p>
            </div>

            @if ($product->short_descriptions)
                <p class="text-muted-foreground leading-relaxed mb-6">{{ $product->short_descriptions }}</p>
            @endif

            <form action="{{ route('cart.store') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                @if ($hasVariants)
                    <div>
                        <p class="text-sm font-semibold text-foreground mb-2.5">{{ __('client.product.select_variant') }}</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($variants as $variant)
                                @php
                                    $label = $variant->attributeValues->pluck('value')->implode(' / ') ?: $variant->sku;
                                @endphp
                                <label class="cursor-pointer">
                                    <input type="radio" name="product_variant_id" value="{{ $variant->id }}"
                                        data-price="{{ $variant->effective_price }}"
                                        data-base="{{ $variant->price }}" class="peer sr-only variant-option"
                                        @checked($variant->id === $defaultVariant->id)>
                                    <span
                                        class="inline-flex flex-col items-start px-4 py-2.5 text-sm border-2 border-border rounded-xl text-muted-foreground transition-all peer-checked:border-primary peer-checked:bg-primary/5 peer-checked:text-primary hover:border-primary/50">
                                        <span class="font-medium">{{ $label }}</span>
                                        <span class="text-xs opacity-80">{{ format_price($variant->effective_price) }}</span>
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
                    <div class="flex items-center border border-border rounded-xl overflow-hidden">
                        <button type="button" id="qty-minus"
                            class="w-11 h-11 flex items-center justify-center text-muted-foreground hover:bg-muted transition-colors"
                            aria-label="{{ __('client.product.decrease') }}">
                            <i class="fa-solid fa-minus text-xs"></i>
                        </button>
                        <input type="number" id="quantity" name="quantity" value="1" min="1"
                            max="{{ \App\Services\Client\CartService::MAX_QUANTITY }}"
                            class="w-14 h-11 text-center border-x border-border focus:outline-none">
                        <button type="button" id="qty-plus"
                            class="w-11 h-11 flex items-center justify-center text-muted-foreground hover:bg-muted transition-colors"
                            aria-label="{{ __('client.product.increase') }}">
                            <i class="fa-solid fa-plus text-xs"></i>
                        </button>
                    </div>

                    <button type="submit" @disabled($outOfStock)
                        class="flex-1 min-w-44 inline-flex items-center justify-center gap-2 px-6 py-3.5 text-sm font-semibold text-white rounded-xl transition-colors {{ $outOfStock ? 'bg-muted-foreground/40 cursor-not-allowed' : 'bg-primary hover:bg-primary/90' }}">
                        <i class="fa-solid fa-cart-plus"></i>
                        {{ $outOfStock ? __('client.product.out_of_stock') : __('client.product.add_to_cart') }}
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
                <a href="{{ route('shop.index') }}"
                    class="text-sm font-medium text-primary hover:underline">{{ __('common.actions.view_all') }}</a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($relatedProducts as $related)
                    @include('components.product-card', ['product' => $related])
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

            $('.gallery-thumb').on('click', function() {
                $('#gallery-main').css('opacity', 0.4).attr('src', $(this).data('src'));
                setTimeout(() => $('#gallery-main').css('opacity', 1), 120);

                $('.gallery-thumb').removeClass('border-primary').addClass('border-border');
                $(this).removeClass('border-border').addClass('border-primary');
            });

            $('.variant-option').on('change', function() {
                const price = parseFloat($(this).data('price'));
                const base = parseFloat($(this).data('base'));

                $('#price-display').text(formatPrice(price));

                if (base > price) {
                    const percent = Math.round(((base - price) / base) * 100);
                    $('#base-display').text(formatPrice(base)).removeClass('hidden');
                    $('#discount-badge').text('-' + percent + '%').removeClass('hidden');
                } else {
                    $('#base-display').addClass('hidden');
                    $('#discount-badge').addClass('hidden');
                }
            });

            function formatPrice(value) {
                return new Intl.NumberFormat('vi-VN').format(value) + 'đ';
            }
        });
    </script>
@endpush
