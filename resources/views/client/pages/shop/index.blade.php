@extends('client.layouts.app')

@section('title', __('common.app_name') . ' - ' . __('client.shop.title'))

@section('content')
    <nav class="flex items-center gap-2 text-sm text-muted-foreground mb-6">
        <a href="{{ route('index') }}" class="hover:text-primary transition-colors">{{ __('client.nav.home') }}</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <span class="text-foreground font-medium">{{ __('client.shop.breadcrumb') }}</span>
    </nav>

    <div class="flex flex-col lg:flex-row gap-6">
        <aside class="lg:w-64 shrink-0">
            <button type="button" id="filter-toggle"
                class="lg:hidden w-full flex items-center justify-between px-4 py-3 bg-card border border-border rounded-xl font-medium mb-3">
                <span><i class="fa-solid fa-sliders mr-2 text-primary"></i>{{ __('client.shop.filters') }}</span>
                <i class="fa-solid fa-chevron-down text-xs"></i>
            </button>

            <form action="{{ route('shop.index') }}" method="GET" id="filter-panel"
                class="hidden lg:block bg-card border border-border rounded-xl p-5 space-y-6 lg:sticky lg:top-24">
                <div>
                    <label for="keyword"
                        class="block text-sm font-semibold text-foreground mb-2">{{ __('client.shop.keyword') }}</label>
                    <div class="relative">
                        <i
                            class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground"></i>
                        <input type="search" id="keyword" name="keyword" value="{{ request('keyword') }}"
                            placeholder="{{ __('client.shop.keyword_placeholder') }}"
                            class="w-full pl-9 pr-3 py-2 text-sm border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    </div>
                </div>

                <div>
                    <p class="text-sm font-semibold text-foreground mb-2">{{ __('client.shop.category') }}</p>
                    <div class="space-y-1 max-h-56 overflow-y-auto pr-1">
                        <label
                            class="flex items-center gap-2 px-2 py-1.5 rounded-lg cursor-pointer hover:bg-muted transition-colors">
                            <input type="radio" name="category_id" value="" @checked(! request('category_id'))
                                class="accent-primary">
                            <span class="text-sm text-muted-foreground">{{ __('common.labels.all') }}</span>
                        </label>
                        @foreach ($categories as $category)
                            <label
                                class="flex items-center gap-2 px-2 py-1.5 rounded-lg cursor-pointer hover:bg-muted transition-colors">
                                <input type="radio" name="category_id" value="{{ $category->id }}"
                                    @checked((string) request('category_id') === (string) $category->id) class="accent-primary">
                                <span class="text-sm text-muted-foreground">{{ $category->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label for="branch_id"
                        class="block text-sm font-semibold text-foreground mb-2">{{ __('client.shop.brand') }}</label>
                    <select id="branch_id" name="branch_id"
                        class="w-full px-3 py-2 text-sm border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                        <option value="">{{ __('client.shop.all_brands') }}</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" @selected((string) request('branch_id') === (string) $branch->id)>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <p class="text-sm font-semibold text-foreground mb-2">{{ __('client.shop.price_range') }}</p>
                    <div class="flex items-center gap-2">
                        <input type="number" name="min_price" min="0" step="100000"
                            value="{{ request('min_price') }}" placeholder="{{ __('client.shop.price_from') }}"
                            class="w-full px-3 py-2 text-sm border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                        <span class="text-muted-foreground">-</span>
                        <input type="number" name="max_price" min="0" step="100000"
                            value="{{ request('max_price') }}" placeholder="{{ __('client.shop.price_to') }}"
                            class="w-full px-3 py-2 text-sm border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    </div>
                    @error('max_price')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_sale" value="1" @checked(request('is_sale'))
                        class="h-4 w-4 rounded accent-primary">
                    <span class="text-sm text-muted-foreground">{{ __('client.shop.only_sale') }}</span>
                </label>

                <input type="hidden" name="sort" value="{{ request('sort') }}">

                <div class="flex gap-2 pt-1">
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors">
                        {{ __('common.actions.apply') }}
                    </button>
                    <a href="{{ route('shop.index') }}"
                        class="px-4 py-2.5 text-sm font-medium text-muted-foreground border border-border rounded-lg hover:bg-muted transition-colors">
                        {{ __('common.actions.reset') }}
                    </a>
                </div>
            </form>
        </aside>

        <div class="flex-1 min-w-0">
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-card border border-border rounded-xl px-4 py-3 mb-5">
                <div>
                    <h1 class="text-lg font-bold text-foreground">{{ __('client.shop.title') }}</h1>
                    <p class="text-sm text-muted-foreground">
                        {{ __('client.shop.found', ['count' => $products->total()]) }}
                    </p>
                </div>

                <form action="{{ route('shop.index') }}" method="GET" class="flex items-center gap-2">
                    @foreach (Arr::except($filters, ['sort']) as $name => $value)
                        @if ($value !== null && $value !== '')
                            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                        @endif
                    @endforeach

                    <label for="sort"
                        class="text-sm text-muted-foreground whitespace-nowrap">{{ __('client.shop.sort') }}</label>
                    <select id="sort" name="sort" onchange="this.form.submit()"
                        class="px-3 py-2 text-sm border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                        @foreach (['newest', 'popular', 'price_asc', 'price_desc', 'oldest'] as $value)
                            <option value="{{ $value }}" @selected(request('sort', 'newest') === $value)>
                                {{ __('client.shop.sorts.' . $value) }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            @php
                $activeChips = [];
                if (request('keyword')) {
                    $activeChips[] = ['label' => request('keyword'), 'param' => 'keyword'];
                }
                if (request('category_id') && $categories->firstWhere('id', request('category_id'))) {
                    $activeChips[] = ['label' => $categories->firstWhere('id', request('category_id'))->name, 'param' => 'category_id'];
                }
                if (request('branch_id') && $branches->firstWhere('id', request('branch_id'))) {
                    $activeChips[] = ['label' => $branches->firstWhere('id', request('branch_id'))->name, 'param' => 'branch_id'];
                }
                if (request('is_sale')) {
                    $activeChips[] = ['label' => __('client.shop.only_sale'), 'param' => 'is_sale'];
                }
                if (request('min_price') || request('max_price')) {
                    $activeChips[] = [
                        'label' => format_price(request('min_price', 0)) . ' - ' . format_price(request('max_price', 0)),
                        'param' => 'price',
                    ];
                }
            @endphp

            @if ($activeChips)
                <div class="flex flex-wrap items-center gap-2 mb-5">
                    @foreach ($activeChips as $chip)
                        @php
                            $remove = $chip['param'] === 'price'
                                ? Arr::except(request()->query(), ['min_price', 'max_price', 'page'])
                                : Arr::except(request()->query(), [$chip['param'], 'page']);
                        @endphp
                        <a href="{{ route('shop.index', $remove) }}"
                            class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium bg-primary/10 text-primary rounded-full hover:bg-primary/20 transition-colors">
                            {{ $chip['label'] }}
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endforeach

                    <a href="{{ route('shop.index') }}"
                        class="text-xs font-medium text-muted-foreground hover:text-foreground transition-colors">
                        {{ __('common.actions.clear_filter') }}
                    </a>
                </div>
            @endif

            @if ($products->isEmpty())
                <div class="bg-card border border-dashed border-border rounded-xl py-20 text-center">
                    <i class="fa-solid fa-magnifying-glass text-5xl text-muted-foreground/25 mb-4"></i>
                    <p class="text-lg font-semibold text-foreground mb-1">{{ __('client.shop.empty_title') }}</p>
                    <p class="text-sm text-muted-foreground mb-5">{{ __('client.shop.empty_description') }}</p>
                    <a href="{{ route('shop.index') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors">
                        <i class="fa-solid fa-rotate-left"></i> {{ __('common.actions.clear_filter') }}
                    </a>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach ($products as $product)
                        @include('components.product-card', ['product' => $product])
                    @endforeach
                </div>

                @include('components.pagination', ['paginator' => $products->withQueryString()])
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('#filter-toggle').on('click', function() {
                $('#filter-panel').toggleClass('hidden');
                $(this).find('.fa-chevron-down').toggleClass('rotate-180');
            });
        });
    </script>
@endpush
