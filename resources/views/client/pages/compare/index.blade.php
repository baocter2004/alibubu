@extends('client.layouts.app')

@section('title', __('common.app_name') . ' - ' . __('client.compare.title'))

@section('content')
    <span class="hidden" data-compare-page></span>

    <nav class="flex items-center gap-2 text-sm text-muted-foreground mb-6">
        <a href="{{ route('index') }}" class="hover:text-primary transition-colors">{{ __('client.nav.home') }}</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <a href="{{ route('shop.index') }}" class="hover:text-primary transition-colors">{{ __('client.shop.breadcrumb') }}</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <span class="text-foreground font-medium">{{ __('client.compare.breadcrumb') }}</span>
    </nav>

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
        <div>
            <p class="eyebrow mb-2">{{ __('client.compare.breadcrumb') }}</p>
            <h1 class="text-2xl md:text-3xl font-extrabold text-foreground">{{ __('client.compare.title') }}</h1>
            @if ($products->isNotEmpty())
                <p class="text-sm text-muted-foreground mt-1">
                    {{ __('client.compare.subtitle', ['count' => $products->count()]) }}
                </p>
            @endif
        </div>

        @if ($products->count() > 1)
            <div class="flex items-center gap-3">
                <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" id="only-diff" class="h-4 w-4 rounded accent-primary">
                    <span class="text-sm text-muted-foreground">{{ __('client.compare.only_diff') }}</span>
                </label>

                <form action="{{ route('compare.clear') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-muted-foreground border border-border rounded-lg hover:border-danger hover:text-danger transition-colors">
                        <i class="fa-regular fa-trash-can"></i>
                        {{ __('client.compare.clear') }}
                    </button>
                </form>
            </div>
        @endif
    </div>

    @if ($products->isEmpty())
        <div class="bg-card border border-dashed border-border rounded-3xl py-20 text-center">
            <span class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-muted mb-5">
                <i class="fa-solid fa-code-compare text-3xl text-muted-foreground/40"></i>
            </span>
            <p class="text-lg font-semibold text-foreground mb-1">{{ __('client.compare.empty_title') }}</p>
            <p class="text-sm text-muted-foreground mb-6 max-w-md mx-auto">
                {{ __('client.compare.empty_description', ['max' => $maxItems]) }}
            </p>
            <a href="{{ route('shop.index') }}"
                class="inline-flex items-center gap-2 px-6 py-3 text-sm font-bold btn-primary rounded-xl">
                <i class="fa-solid fa-bag-shopping"></i>
                {{ __('client.compare.empty_cta') }}
            </a>
        </div>
    @else
        @if ($products->count() < 2)
            <p class="flex items-center gap-2 px-4 py-3 mb-5 text-sm text-foreground bg-accent-soft border border-accent/25 rounded-xl">
                <i class="fa-solid fa-circle-info text-accent"></i>
                {{ __('client.compare.need_more') }}
            </p>
        @endif

        <div class="card-surface overflow-hidden">
            <div class="compare-scroll">
                <table class="compare-table">
                    <thead>
                        <tr>
                            <th class="col-label p-4 text-left align-bottom">
                                <span class="text-xs font-bold uppercase tracking-wider text-muted-foreground">
                                    {{ __('common.labels.information') }}
                                </span>
                            </th>

                            @foreach ($products as $product)
                                <th class="p-4 text-left align-top min-w-56 max-w-72">
                                    <div class="relative">
                                        <form action="{{ route('compare.destroy', $product->id) }}" method="POST"
                                            class="absolute top-0 right-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="w-7 h-7 rounded-lg text-muted-foreground hover:bg-red-50 hover:text-danger transition-colors"
                                                aria-label="{{ __('client.compare.remove') }}">
                                                <i class="fa-solid fa-xmark text-sm"></i>
                                            </button>
                                        </form>

                                        <a href="{{ route('shop.show', $product->slug) }}"
                                            class="block w-24 h-24 mb-3 bg-white border border-border rounded-xl overflow-hidden flex items-center justify-center">
                                            @if ($product->thumbnail)
                                                <img src="{{ Storage::disk('public')->url($product->thumbnail) }}"
                                                    alt="{{ $product->name }}" loading="lazy"
                                                    class="w-full h-full object-contain p-2">
                                            @else
                                                <i class="fa-solid fa-box-open text-2xl text-muted-foreground/25"></i>
                                            @endif
                                        </a>

                                        <a href="{{ route('shop.show', $product->slug) }}"
                                            class="block text-sm font-semibold text-foreground line-clamp-2 leading-snug hover:text-primary transition-colors">
                                            {{ $product->name }}
                                        </a>

                                        <div class="flex flex-wrap items-baseline gap-2 mt-2 mb-3">
                                            <span class="text-lg price-main">{{ format_price($product->effective_price) }}</span>
                                            @if ($product->discount_percent > 0)
                                                <span class="text-xs text-muted-foreground line-through tabular">
                                                    {{ format_price($product->base_price) }}
                                                </span>
                                            @endif
                                        </div>

                                        @if ($product->hasVariants() || ! $product->inStock())
                                            <a href="{{ route('shop.show', $product->slug) }}"
                                                class="w-full inline-flex items-center justify-center gap-2 px-3 py-2.5 text-sm font-bold btn-outline rounded-xl">
                                                <i class="fa-solid fa-sliders text-xs"></i>
                                                {{ __('client.product.choose') }}
                                            </a>
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
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    @foreach ($sections as $section)
                        <tbody>
                            <tr class="compare-group">
                                <th class="col-label px-4 py-2.5 text-left" colspan="1">{{ $section['title'] }}</th>
                                <th class="px-4 py-2.5 text-left" colspan="{{ $products->count() }}"></th>
                            </tr>

                            @foreach ($section['rows'] as $row)
                                <tr data-compare-row @if ($row['same']) data-same="1" @endif>
                                    <th class="col-label px-4 py-3 text-left text-sm font-medium text-muted-foreground">
                                        {{ $row['label'] }}
                                    </th>

                                    @foreach ($row['cells'] as $index => $cell)
                                        <td class="px-4 py-3 text-sm text-foreground {{ $row['best'] === $index ? 'compare-best' : '' }}">
                                            @if ($cell['text'] === null || $cell['text'] === '')
                                                <span class="text-muted-foreground/50">—</span>
                                            @else
                                                <span class="{{ $row['best'] === $index ? 'font-semibold text-success' : '' }}">
                                                    {{ $cell['text'] }}
                                                </span>
                                                @if ($row['best'] === $index)
                                                    <span class="ml-1.5 px-1.5 py-0.5 text-[10px] font-bold badge-flag bg-success text-white">
                                                        {{ __('client.compare.best') }}
                                                    </span>
                                                @endif
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    @endforeach
                </table>
            </div>
        </div>

        <p class="mt-4 text-xs text-muted-foreground">
            <i class="fa-solid fa-circle-info mr-1"></i>{{ __('client.compare.hint_same_category') }}
        </p>
    @endif
@endsection

@push('scripts')
    <script>
        $(function() {
            $('#only-diff').on('change', function() {
                $('[data-compare-row][data-same="1"]').toggleClass('hidden', this.checked);
            });
        });
    </script>
@endpush
