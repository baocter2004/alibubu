@extends('client.layouts.app')

@section('title', __('common.app_name') . ' - ' . __('client.home.title'))

@section('content')
    @php
        $heroProduct = $featuredProducts->first() ?? $trendingProducts->first() ?? $saleProducts->first();
    @endphp

    <section class="grain tech-grid relative overflow-hidden ink-panel rounded-[1.75rem] mb-14">
        <span class="tech-orb w-72 h-72 -top-24 -right-16 bg-accent/25"></span>
        <span class="tech-orb w-80 h-80 -bottom-32 -left-20 bg-sky-400/20"></span>

        <div class="relative grid lg:grid-cols-12 gap-8 px-5 py-10 sm:px-6 sm:py-12 md:px-12 md:py-16 lg:py-20 text-white">
            <div class="lg:col-span-7 xl:col-span-6 flex flex-col justify-center">
                <span class="inline-flex items-center gap-2.5 self-start px-3 py-1.5 mb-6 text-[11px] font-semibold tracking-wider uppercase rounded-full bg-white/10 border border-white/15">
                    <span class="pulse-dot"></span>
                    {{ __('client.home.hero.badge') }}
                </span>

                <h1
                    class="text-[1.85rem] sm:text-[2.5rem] leading-[1.08] md:text-5xl lg:text-[4.25rem] font-extrabold tracking-tight text-balance break-words mb-5">
                    {{ __('client.home.heading') }}
                    <span class="block text-accent">{{ __('client.home.heading_highlight') }}</span>
                </h1>

                <p class="text-base md:text-lg text-white/65 max-w-md mb-8 leading-relaxed">
                    {{ __('client.home.description') }}
                </p>

                <form action="{{ route('shop.index') }}" method="GET" class="max-w-lg mb-8">
                    <div class="flex items-center gap-2 p-1.5 bg-white rounded-2xl shadow-lg">
                        <div class="relative flex-1 min-w-0">
                            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-muted-foreground"></i>
                            <input type="search" name="keyword" value="{{ request('keyword') }}"
                                aria-label="{{ __('client.home.hero.search_placeholder') }}"
                                placeholder="{{ __('client.home.hero.search_placeholder') }}"
                                class="w-full pl-11 pr-3 py-3 text-sm text-foreground bg-transparent placeholder:text-muted-foreground focus:outline-none">
                        </div>
                        <button type="submit"
                            class="shrink-0 px-5 md:px-7 py-3 text-sm font-bold btn-accent rounded-xl whitespace-nowrap">
                            {{ __('client.home.hero.search_cta') }}
                        </button>
                    </div>
                </form>

                @if ($categories->isNotEmpty())
                    <div class="flex flex-wrap items-center gap-2 mb-7">
                        <span class="text-[11px] font-semibold uppercase tracking-[0.14em] text-white/45 mr-1">
                            {{ __('client.home.hero.quick_links') }}
                        </span>
                        @foreach ($categories->take(4) as $category)
                            <a href="{{ route('shop.index', ['category_id' => $category->id]) }}"
                                class="tech-chip text-white/80 hover:bg-white/15 hover:text-white transition-colors">
                                <i class="{{ $category->icon ?: 'fa-solid fa-tag' }} text-accent text-[11px]"></i>
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                @endif

                <dl class="flex flex-wrap items-end gap-x-8 gap-y-4 pt-6 border-t border-white/10">
                    @foreach ([['products', number_format($stats['products'])], ['customers', number_format($stats['customers']) . '+'], ['rating', $stats['rating']]] as [$key, $value])
                        <div class="relative pr-8 last:pr-0">
                            <dt class="sr-only">{{ __('client.home.stats.' . $key) }}</dt>
                            <dd class="text-2xl md:text-3xl font-extrabold tabular tracking-tight">{{ $value }}</dd>
                            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-white/55 mt-0.5">
                                {{ __('client.home.stats.' . $key) }}
                            </p>
                        </div>
                    @endforeach
                </dl>
            </div>

            @if ($heroProduct)
                <div class="lg:col-span-5 xl:col-span-6 flex items-center lg:justify-end">
                    <a href="{{ route('shop.show', $heroProduct->slug) }}"
                        class="group relative w-full max-w-sm lg:max-w-md">
                        <span class="absolute -inset-4 rounded-[2rem] bg-white/5 -rotate-2 transition-transform duration-500 group-hover:-rotate-3"></span>

                        <span class="relative block bg-white rounded-[1.5rem] p-6 shadow-2xl rotate-1 transition-transform duration-500 group-hover:rotate-0 group-hover:-translate-y-1">
                            <span class="flex items-center justify-between mb-4">
                                <span class="eyebrow flex items-center gap-2">
                                    <span class="pulse-dot"></span>
                                    {{ __('client.home.featured.title') }}
                                </span>
                                @if ($heroProduct->discount_percent > 0)
                                    <span class="px-2 py-1 text-[11px] font-bold badge-sale">
                                        -{{ $heroProduct->discount_percent }}%
                                    </span>
                                @endif
                            </span>

                            <span class="block aspect-square mb-4 overflow-hidden rounded-xl bg-muted/40">
                                @if ($heroProduct->thumbnail)
                                    <img src="{{ Storage::disk('public')->url($heroProduct->thumbnail) }}"
                                        alt="{{ $heroProduct->name }}" width="400" height="400"
                                        class="w-full h-full object-contain p-4 transition-transform duration-700 group-hover:scale-105">
                                @else
                                    <span class="w-full h-full flex items-center justify-center">
                                        <i class="fa-solid fa-box-open text-6xl text-muted-foreground/20"></i>
                                    </span>
                                @endif
                            </span>

                            <span class="block text-sm font-semibold text-foreground line-clamp-2 mb-2 leading-snug">
                                {{ $heroProduct->name }}
                            </span>

                            <span class="flex items-baseline gap-2">
                                <span class="text-xl price-main">{{ format_price($heroProduct->effective_price) }}</span>
                                @if ($heroProduct->discount_percent > 0)
                                    <span class="text-xs text-muted-foreground line-through tabular">
                                        {{ format_price($heroProduct->base_price) }}
                                    </span>
                                @endif
                            </span>
                        </span>
                    </a>
                </div>
            @endif
        </div>
    </section>

    <section class="mb-16">
        <ul class="grid grid-cols-1 sm:grid-cols-3 gap-px bg-border rounded-2xl overflow-hidden">
            @foreach ([['fa-certificate', 'trust_warranty'], ['fa-truck-fast', 'trust_shipping'], ['fa-rotate-left', 'trust_returns']] as [$icon, $key])
                <li class="flex items-center gap-3.5 bg-card px-5 py-5">
                    <i class="fa-solid {{ $icon }} text-lg text-accent"></i>
                    <span class="text-sm font-semibold text-foreground">{{ __('client.home.hero.' . $key) }}</span>
                </li>
            @endforeach
        </ul>
    </section>

    @if ($categories->isNotEmpty())
        @php
            $leadCategory = $categories->first();
            $restCategories = $categories->slice(1)->take(6);
        @endphp

        <section class="mb-16">
            <div class="flex flex-wrap items-end justify-between gap-4 mb-7">
                <div class="max-w-lg">
                    <p class="eyebrow mb-2">{{ __('client.home.categories.subtitle') }}</p>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-foreground">
                        {{ __('client.home.categories.title') }}
                    </h2>
                </div>
                <a href="{{ route('shop.index') }}"
                    class="link-draw text-sm font-semibold text-primary self-end pb-1">
                    {{ __('common.actions.view_all') }}
                    <i class="fa-solid fa-arrow-right ml-1 text-xs"></i>
                </a>
            </div>

            <div class="grid gap-4 md:grid-cols-3" data-reveal-group>
                <a href="{{ route('shop.index', ['category_id' => $leadCategory->id]) }}"
                    class="reveal grain group relative md:row-span-2 flex flex-col justify-end min-h-56 md:min-h-full overflow-hidden ink-panel rounded-2xl p-7 text-white">
                    <i class="{{ $leadCategory->icon ?: 'fa-solid fa-tag' }} absolute -right-6 -top-4 text-8xl text-white/[0.07] transition-transform duration-500 group-hover:scale-110"></i>

                    <span class="relative">
                        <span class="block text-xl font-bold mb-1">{{ $leadCategory->name }}</span>
                        <span class="block text-sm text-white/55 mb-4">
                            {{ __('client.home.categories.count', ['count' => $leadCategory->products_count]) }}
                        </span>
                        <span class="inline-flex items-center gap-2 text-sm font-semibold text-accent">
                            {{ __('common.actions.view_all') }}
                            <i class="fa-solid fa-arrow-right text-xs transition-transform duration-300 group-hover:translate-x-1"></i>
                        </span>
                    </span>
                </a>

                @foreach ($restCategories as $category)
                    <a href="{{ route('shop.index', ['category_id' => $category->id]) }}"
                        class="reveal group flex items-center gap-4 card-surface card-interactive p-4">
                        <span class="w-12 h-12 shrink-0 rounded-xl bg-primary-soft text-primary flex items-center justify-center transition-colors duration-300 group-hover:bg-primary group-hover:text-white">
                            <i class="{{ $category->icon ?: 'fa-solid fa-tag' }}"></i>
                        </span>
                        <span class="min-w-0">
                            <span class="block text-sm font-semibold text-foreground line-clamp-1">{{ $category->name }}</span>
                            <span class="block text-xs text-muted-foreground tabular">
                                {{ __('client.home.categories.count', ['count' => $category->products_count]) }}
                            </span>
                        </span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @if ($saleProducts->isNotEmpty())
        <section class="mb-16">
            <div class="flex flex-wrap items-end justify-between gap-4 mb-7">
                <div class="flex items-center gap-4">
                    <span class="w-12 h-12 shrink-0 rounded-xl bg-price/10 text-price flex items-center justify-center">
                        <i class="fa-solid fa-bolt text-lg"></i>
                    </span>
                    <div>
                        <p class="eyebrow mb-1">{{ __('client.home.deals.subtitle') }}</p>
                        <h2 class="text-2xl md:text-3xl font-extrabold text-foreground">
                            {{ __('client.home.deals.title') }}
                        </h2>
                    </div>
                </div>
                <a href="{{ route('shop.index', ['is_sale' => 1]) }}"
                    class="link-draw text-sm font-semibold text-primary self-end pb-1">
                    {{ __('common.actions.view_all') }}
                    <i class="fa-solid fa-arrow-right ml-1 text-xs"></i>
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-5" data-reveal-group>
                @foreach ($saleProducts as $product)
                    @include('components.product-card', ['product' => $product, 'reveal' => true])
                @endforeach
            </div>
        </section>
    @endif

    @if ($featuredProducts->isNotEmpty())
        <section class="mb-16">
            <div class="flex flex-wrap items-end justify-between gap-4 mb-7">
                <div class="max-w-lg">
                    <p class="eyebrow mb-2">{{ __('client.home.featured.subtitle') }}</p>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-foreground">
                        {{ __('client.home.featured.title') }}
                    </h2>
                </div>
                <a href="{{ route('shop.index') }}"
                    class="link-draw text-sm font-semibold text-primary self-end pb-1">
                    {{ __('common.actions.view_all') }}
                    <i class="fa-solid fa-arrow-right ml-1 text-xs"></i>
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-5" data-reveal-group>
                @foreach ($featuredProducts as $product)
                    @include('components.product-card', ['product' => $product, 'reveal' => true])
                @endforeach
            </div>
        </section>
    @endif

    @if ($brands->isNotEmpty())
        <section class="mb-16">
            <div class="flex items-center gap-5 mb-6">
                <h2 class="text-lg font-bold text-foreground whitespace-nowrap">{{ __('client.home.brands.title') }}</h2>
                <span class="rule flex-1"></span>
                <span class="text-xs text-muted-foreground whitespace-nowrap hidden sm:block">
                    {{ __('client.home.brands.subtitle') }}
                </span>
            </div>

            <div class="flex flex-wrap gap-2.5">
                @foreach ($brands as $brand)
                    <a href="{{ route('shop.index', ['branch_id' => $brand->id]) }}"
                        class="group inline-flex items-center gap-2.5 pl-4 pr-3 py-2.5 bg-card border border-border rounded-full hover:border-primary/35 hover:bg-primary-soft transition-colors">
                        <span class="text-sm font-semibold text-foreground">{{ $brand->name }}</span>
                        <span class="px-1.5 py-0.5 text-[11px] font-bold tabular text-muted-foreground bg-muted rounded-full group-hover:bg-white transition-colors">
                            {{ $brand->products_count }}
                        </span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @if ($trendingProducts->isNotEmpty())
        <section class="mb-16">
            <div class="flex flex-wrap items-end justify-between gap-4 mb-7">
                <div class="max-w-lg">
                    <p class="eyebrow mb-2">{{ __('client.home.trending.subtitle') }}</p>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-foreground">
                        {{ __('client.home.trending.title') }}
                    </h2>
                </div>
                <a href="{{ route('shop.index', ['sort' => 'popular']) }}"
                    class="link-draw text-sm font-semibold text-primary self-end pb-1">
                    {{ __('common.actions.view_all') }}
                    <i class="fa-solid fa-arrow-right ml-1 text-xs"></i>
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-5" data-reveal-group>
                @foreach ($trendingProducts as $product)
                    @include('components.product-card', ['product' => $product, 'reveal' => true])
                @endforeach
            </div>
        </section>
    @endif

    <section class="mb-4">
        <div class="grid md:grid-cols-2 items-center gap-8 bg-accent-soft border border-accent/20 rounded-2xl p-7 md:p-10">
            <div>
                <p class="eyebrow mb-2">{{ __('client.home.newsletter.subtitle') }}</p>
                <h2 class="text-2xl md:text-3xl font-extrabold text-foreground">
                    {{ __('client.home.newsletter.title') }}
                </h2>
            </div>

            <form action="{{ route('auth.client.showFormRegister') }}" method="GET"
                class="flex flex-col sm:flex-row gap-3 md:justify-end">
                <input type="email" name="email" required
                    aria-label="{{ __('client.home.newsletter.placeholder') }}"
                    placeholder="{{ __('client.home.newsletter.placeholder') }}"
                    class="flex-1 md:max-w-xs px-4 py-3 text-sm bg-white border border-accent/25 rounded-xl text-foreground placeholder:text-muted-foreground focus:outline-none focus:border-accent transition-colors">
                <button type="submit"
                    class="px-6 py-3 text-sm font-bold btn-accent rounded-xl whitespace-nowrap">
                    {{ __('client.home.newsletter.submit') }}
                </button>
            </form>
        </div>
    </section>
@endsection
