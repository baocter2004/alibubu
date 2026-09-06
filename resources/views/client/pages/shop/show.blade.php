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
                        <button type="button" aria-label="{{ $product->name }} {{ $index + 1 }}"
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
                    <span id="price-display" class="text-3xl md:text-4xl price-main tracking-tight">{{ format_price($price) }}</span>
                    <span id="base-display"
                        class="text-base text-muted-foreground line-through tabular {{ $base > $price ? '' : 'hidden' }}">
                        {{ format_price($base) }}
                    </span>
                    <span id="discount-badge"
                        class="px-2 py-0.5 text-xs font-bold bg-price text-white rounded-full {{ $base > $price ? '' : 'hidden' }}">
                        {{ $base > $price ? '-' . (int) round((($base - $price) / $base) * 100) . '%' : '' }}
                    </span>
                </div>

                <p class="mt-2 text-sm {{ $outOfStock ? 'text-red-600' : 'text-success' }}">
                    <i class="fa-solid {{ $outOfStock ? 'fa-circle-xmark' : 'fa-circle-check' }} mr-1"></i>
                    {{ $outOfStock ? __('client.product.out_of_stock') : __('client.product.stock_left', ['count' => number_format($product->stock)]) }}
                </p>
            </div>

            @if ($product->short_descriptions)
                <p class="text-muted-foreground leading-relaxed mb-6">{{ $product->short_descriptions }}</p>
            @endif

            <form action="{{ route('cart.store') }}" method="POST" id="add-to-cart-form" class="space-y-5" data-cart-add>
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

                <div class="flex flex-wrap items-center gap-3">
                    <span class="text-sm font-semibold text-foreground w-full sm:w-auto sm:sr-only">
                        {{ __('client.product.quantity') }}
                    </span>

                    <div class="flex items-center border border-border rounded-xl overflow-hidden">
                        <button type="button" id="qty-minus"
                            class="w-11 h-11 flex items-center justify-center text-muted-foreground hover:bg-muted transition-colors"
                            aria-label="{{ __('client.product.decrease') }}">
                            <i class="fa-solid fa-minus text-xs"></i>
                        </button>
                        <input type="number" id="quantity" name="quantity" value="1" min="1"
                            max="{{ \App\Services\Client\CartService::MAX_QUANTITY }}" inputmode="numeric"
                            class="w-14 h-11 text-center border-x border-border focus:outline-none">
                        <button type="button" id="qty-plus"
                            class="w-11 h-11 flex items-center justify-center text-muted-foreground hover:bg-muted transition-colors"
                            aria-label="{{ __('client.product.increase') }}">
                            <i class="fa-solid fa-plus text-xs"></i>
                        </button>
                    </div>

                    <button type="submit" @disabled($outOfStock)
                        class="flex-1 basis-full sm:basis-0 sm:min-w-40 inline-flex items-center justify-center gap-2 px-5 py-3.5 text-sm font-bold rounded-xl {{ $outOfStock ? 'text-muted-foreground bg-muted cursor-not-allowed' : 'btn-outline' }}">
                        <i class="fa-solid fa-cart-plus"></i>
                        {{ $outOfStock ? __('client.product.out_of_stock') : __('client.product.add_to_cart') }}
                    </button>

                    @unless ($outOfStock)
                        <button type="submit" name="buy_now" value="1"
                            class="flex-1 basis-full sm:basis-0 sm:min-w-40 inline-flex items-center justify-center gap-2 px-5 py-3.5 text-sm font-bold btn-accent rounded-xl">
                            <i class="fa-solid fa-bolt"></i>
                            {{ __('client.product.buy_now') }}
                        </button>
                    @endunless
                </div>
            </form>

            @php $compared = app(\App\Services\Client\CompareService::class)->has($product->id); @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-3">
                @auth
                    @php $wishlisted = Auth::user()->hasWishlisted($product->id); @endphp
                    <form action="{{ route('shop.wishlist.toggle', $product->slug) }}" method="POST" data-wishlist-toggle>
                        @csrf
                        <button type="submit" aria-pressed="{{ $wishlisted ? 'true' : 'false' }}"
                            data-wishlist-on="border-red-200 bg-red-50 text-red-600"
                            data-wishlist-off="border-border text-muted-foreground"
                            class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold rounded-xl border transition-colors hover:border-red-200 hover:text-red-600 {{ $wishlisted ? 'border-red-200 bg-red-50 text-red-600' : 'border-border text-muted-foreground' }}">
                            <i class="fa-{{ $wishlisted ? 'solid' : 'regular' }} fa-heart"></i>
                            <span data-wishlist-label>
                                {{ $wishlisted ? __('client.wishlist.remove') : __('client.wishlist.add') }}
                            </span>
                        </button>
                    </form>
                @endauth

                <form action="{{ route('shop.compare.toggle', $product->slug) }}" method="POST"
                    data-compare-toggle data-product="{{ $product->id }}">
                    @csrf
                    <button type="submit" aria-pressed="{{ $compared ? 'true' : 'false' }}"
                        class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold rounded-xl border transition-colors hover:border-primary hover:text-primary {{ $compared ? 'border-primary bg-primary/5 text-primary' : 'border-border text-muted-foreground' }}">
                        <i class="fa-solid fa-code-compare"></i>
                        <span data-compare-label>
                            {{ $compared ? __('client.compare.added') : __('client.compare.add') }}
                        </span>
                    </button>
                </form>
            </div>

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


    @if ($product->specifications->isNotEmpty())
        <section class="bg-card border border-border rounded-2xl p-6 md:p-8 mb-12">
            <h2 class="text-lg font-bold text-foreground mb-5">{{ __('admin/product.spec.section') }}</h2>

            @php $grouped = $product->specifications->groupBy(fn($sp) => $sp->group ?: ''); @endphp

            <div class="space-y-6">
                @foreach ($grouped as $group => $specs)
                    <div>
                        @if ($group)
                            <p class="text-sm font-semibold text-primary mb-2">{{ $group }}</p>
                        @endif

                        <dl class="divide-y divide-border rounded-xl overflow-hidden border border-border">
                            @foreach ($specs as $spec)
                                <div class="grid grid-cols-3 gap-4 px-4 py-3 odd:bg-muted/40">
                                    <dt class="text-sm text-muted-foreground">{{ $spec->name }}</dt>
                                    <dd class="col-span-2 text-sm text-foreground font-medium">{{ $spec->value }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <section class="bg-card border border-border rounded-2xl p-6 md:p-8 mb-12">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
            <div>
                <h2 class="text-lg font-bold text-foreground">{{ __('client.review.title') }}</h2>
                <p class="text-sm text-muted-foreground mt-0.5">
                    {{ __('client.review.subtitle', ['count' => number_format($product->reviews_count)]) }}
                </p>
            </div>

            @if ($canReview)
                <button type="button" id="write-review-btn"
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold btn-primary rounded-xl">
                    <i class="fa-solid fa-pen"></i>
                    {{ __('client.review.write') }}
                </button>
            @endif
        </div>

        <div class="grid md:grid-cols-3 gap-6 mb-6">
            <div class="flex flex-col items-center justify-center bg-muted/40 rounded-xl p-6">
                <p class="text-4xl font-bold text-foreground">{{ number_format((float) $product->rating, 1) }}</p>
                @include('components.rating', ['rating' => $product->rating, 'size' => 'text-base', 'showValue' => false])
                <p class="text-xs text-muted-foreground mt-2">{{ __('client.review.average') }}</p>
            </div>

            <div class="md:col-span-2 space-y-2">
                @foreach ($ratingBreakdown as $star => $row)
                    <div class="flex items-center gap-3">
                        <span class="w-10 text-xs text-muted-foreground whitespace-nowrap">{{ $star }} <i class="fa-solid fa-star text-amber-400"></i></span>
                        <span class="flex-1 h-2 bg-muted rounded-full overflow-hidden">
                            <span class="block h-full bg-amber-400 rounded-full" style="width: {{ $row['percent'] }}%"></span>
                        </span>
                        <span class="w-10 text-right text-xs text-muted-foreground">{{ $row['count'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        @auth
            @if ($canReview)
                <form action="{{ route('shop.reviews.store', $product->slug) }}" method="POST"
                    id="review-form" class="{{ $errors->any() ? '' : 'hidden' }} bg-muted/40 border border-border rounded-xl p-5 mb-6 space-y-4">
                    @csrf

                    <div>
                        <p class="text-sm font-medium text-foreground mb-2">
                            {{ __('client.review.fields.rating') }} <span class="text-red-500">*</span>
                        </p>
                        <div class="flex items-center gap-1" id="rating-picker">
                            @for ($i = 1; $i <= 5; $i++)
                                <label class="cursor-pointer">
                                    <input type="radio" name="rating" value="{{ $i }}" @checked((int) old('rating') === $i)
                                        class="peer sr-only rating-input">
                                    <i class="fa-solid fa-star text-2xl text-muted-foreground/30 peer-checked:text-amber-400 transition-colors"
                                        data-star="{{ $i }}"></i>
                                </label>
                            @endfor
                        </div>
                        @error('rating')
                            <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="title" class="block text-sm font-medium text-foreground mb-1.5">
                            {{ __('client.review.fields.title') }}
                        </label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}"
                            placeholder="{{ __('client.review.fields.title_placeholder') }}"
                            class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all {{ $errors->has('title') ? 'is-invalid' : 'border-border' }}">
                        @error('title')
                            <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="comment" class="block text-sm font-medium text-foreground mb-1.5">
                            {{ __('client.review.fields.comment') }}
                        </label>
                        <textarea id="comment" name="comment" rows="4"
                            placeholder="{{ __('client.review.fields.comment_placeholder') }}"
                            class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all {{ $errors->has('comment') ? 'is-invalid' : 'border-border' }}">{{ old('comment') }}</textarea>
                        @error('comment')
                            <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <p class="text-xs text-muted-foreground">{{ __('client.review.pending_note') }}</p>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-bold btn-primary rounded-xl">
                            <i class="fa-solid fa-paper-plane"></i>
                            {{ __('client.review.submit') }}
                        </button>
                    </div>
                </form>
            @else
                <p class="text-sm text-muted-foreground bg-muted/40 border border-border rounded-xl p-4 mb-6">
                    <i class="fa-solid fa-circle-info text-primary mr-1.5"></i>{{ __('client.review.purchase_prompt') }}
                </p>
            @endif
        @else
            <p class="text-sm text-muted-foreground bg-muted/40 border border-border rounded-xl p-4 mb-6">
                <i class="fa-solid fa-circle-info text-primary mr-1.5"></i>
                <a href="{{ route('auth.client.showFormLogin') }}" class="text-primary font-medium hover:underline">
                    {{ __('common.actions.login') }}</a>
                — {{ __('client.review.login_prompt') }}
            </p>
        @endauth

        @if ($reviews->isEmpty())
            <p class="py-10 text-center text-sm text-muted-foreground">{{ __('client.review.empty') }}</p>
        @else
            <div class="space-y-5">
                @foreach ($reviews as $review)
                    <div class="flex gap-4 pb-5 border-b border-border last:border-0 last:pb-0">
                        <span class="w-10 h-10 shrink-0 rounded-full bg-primary/10 text-primary font-semibold flex items-center justify-center">
                            {{ Str::upper(Str::substr($review->user?->fullname ?? '?', 0, 1)) }}
                        </span>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <span class="font-medium text-foreground">{{ $review->user?->fullname }}</span>
                                @if ($review->order_id)
                                    <span class="px-2 py-0.5 text-[11px] font-medium bg-success-soft text-success rounded-full">
                                        <i class="fa-solid fa-circle-check"></i> {{ __('client.review.verified') }}
                                    </span>
                                @endif
                                <span class="text-xs text-muted-foreground">{{ $review->created_at?->format('d/m/Y') }}</span>
                            </div>

                            @include('components.rating', ['rating' => $review->rating, 'showValue' => false])

                            @if ($review->title)
                                <p class="font-medium text-foreground mt-2">{{ $review->title }}</p>
                            @endif

                            @if ($review->comment)
                                <p class="text-sm text-muted-foreground mt-1 leading-relaxed">{{ $review->comment }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @include('components.pagination', ['paginator' => $reviews->withQueryString()])
        @endif
    </section>

    @unless ($outOfStock)
        <div class="buy-bar md:hidden">
            <div class="flex items-center gap-3 px-4 py-3">
                <div class="min-w-0">
                    <p class="text-[11px] text-muted-foreground">{{ __('common.labels.price') }}</p>
                    <p class="text-lg price-main truncate" data-sticky-price>{{ format_price($price) }}</p>
                </div>

                <button type="submit" form="add-to-cart-form"
                    class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-3 text-sm font-bold btn-outline rounded-xl">
                    <i class="fa-solid fa-cart-plus text-xs"></i>
                    {{ __('client.product.add_to_cart') }}
                </button>

                <button type="submit" form="add-to-cart-form" name="buy_now" value="1"
                    class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-3 text-sm font-bold btn-accent rounded-xl">
                    <i class="fa-solid fa-bolt text-xs"></i>
                    {{ __('client.product.buy_now') }}
                </button>
            </div>
        </div>
    @endunless

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

            $qty.on('change blur', function() {
                const value = parseInt($qty.val(), 10);
                $qty.val(Number.isNaN(value) ? 1 : Math.min(max, Math.max(1, value)));
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
                $('[data-sticky-price]').text(formatPrice(price));

                if (base > price) {
                    const percent = Math.round(((base - price) / base) * 100);
                    $('#base-display').text(formatPrice(base)).removeClass('hidden');
                    $('#discount-badge').text('-' + percent + '%').removeClass('hidden');
                } else {
                    $('#base-display').addClass('hidden');
                    $('#discount-badge').addClass('hidden');
                }
            });

            $('#write-review-btn').on('click', function() {
                $('#review-form').removeClass('hidden');
                $('html, body').animate({ scrollTop: $('#review-form').offset().top - 100 }, 300);
            });

            $('#rating-picker').on('mouseleave', function() {
                paintStars($('.rating-input:checked').val() || 0);
            });

            $('#rating-picker i').on('mouseenter', function() {
                paintStars($(this).data('star'));
            });

            $('.rating-input').on('change', function() {
                paintStars($(this).val());
            });

            function paintStars(upTo) {
                $('#rating-picker i').each(function() {
                    $(this).toggleClass('text-amber-400', $(this).data('star') <= upTo)
                        .toggleClass('text-muted-foreground/30', $(this).data('star') > upTo);
                });
            }

            paintStars($('.rating-input:checked').val() || 0);

            function formatPrice(value) {
                return new Intl.NumberFormat('vi-VN').format(value) + 'đ';
            }
        });
    </script>
@endpush
