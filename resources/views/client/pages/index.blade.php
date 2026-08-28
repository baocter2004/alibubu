@extends('client.layouts.app')

@section('title', __('common.app_name') . ' - ' . __('client.home.title'))

@section('content')
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-primary via-blue-600 to-indigo-700 mb-8">
        <span class="absolute -top-24 -right-16 w-80 h-80 rounded-full bg-white/10"></span>
        <span class="absolute -bottom-28 -left-20 w-96 h-96 rounded-full bg-white/5"></span>

        <div class="relative px-6 py-12 md:px-12 md:py-16 text-white">
            <span class="inline-flex items-center gap-2 px-3 py-1 text-xs font-semibold rounded-full bg-white/15 backdrop-blur mb-5">
                <i class="fa-solid fa-shield-halved"></i>
                {{ __('client.home.hero.badge') }}
            </span>

            <h1 class="text-3xl md:text-5xl font-bold leading-tight mb-3 max-w-2xl">
                {{ __('client.home.heading') }}<br>
                <span class="text-white/80">{{ __('client.home.heading_highlight') }}</span>
            </h1>

            <p class="text-white/70 max-w-lg mb-7">{{ __('client.home.description') }}</p>

            <form action="{{ route('shop.index') }}" method="GET" class="max-w-xl mb-8">
                <div class="flex flex-col sm:flex-row gap-2 p-2 bg-white/10 backdrop-blur rounded-2xl border border-white/20">
                    <div class="relative flex-1">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-white/50"></i>
                        <input type="search" name="keyword" value="{{ request('keyword') }}"
                            placeholder="{{ __('client.home.hero.search_placeholder') }}"
                            class="w-full pl-11 pr-4 py-3 text-sm bg-transparent text-white placeholder:text-white/50 focus:outline-none">
                    </div>
                    <button type="submit"
                        class="px-6 py-3 text-sm font-semibold text-primary bg-white rounded-xl hover:bg-white/90 transition-colors whitespace-nowrap">
                        {{ __('client.home.hero.search_cta') }}
                    </button>
                </div>
            </form>

            <div class="flex flex-wrap gap-x-6 gap-y-3">
                @foreach ([['fa-certificate', 'trust_warranty'], ['fa-truck-fast', 'trust_shipping'], ['fa-rotate-left', 'trust_returns']] as [$icon, $key])
                    <span class="inline-flex items-center gap-2 text-sm text-white/80">
                        <i class="fa-solid {{ $icon }} text-white/60"></i>
                        {{ __('client.home.hero.' . $key) }}
                    </span>
                @endforeach
            </div>
        </div>
    </section>

    @if ($categories->isNotEmpty())
        <section class="mb-12">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-xl md:text-2xl font-bold text-foreground">{{ __('client.home.categories.title') }}</h2>
                    <p class="text-sm text-muted-foreground mt-0.5">{{ __('client.home.categories.subtitle') }}</p>
                </div>
                <a href="{{ route('shop.index') }}" class="text-sm font-medium text-primary hover:underline">
                    {{ __('common.actions.view_all') }}
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                @foreach ($categories as $category)
                    <a href="{{ route('shop.index', ['category_id' => $category->id]) }}"
                        class="group flex flex-col items-center gap-3 p-5 bg-card border border-border rounded-2xl hover:border-primary hover:shadow-md transition-all">
                        <span
                            class="w-14 h-14 rounded-2xl bg-primary/10 text-primary flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors">
                            <i class="{{ $category->icon ?: 'fa-solid fa-tag' }} text-xl"></i>
                        </span>
                        <span class="text-sm font-semibold text-foreground text-center line-clamp-2">{{ $category->name }}</span>
                        <span class="text-xs text-muted-foreground">
                            {{ __('client.home.categories.count', ['count' => $category->products_count]) }}
                        </span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @if ($saleProducts->isNotEmpty())
        <section class="mb-12">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <span class="w-11 h-11 rounded-xl bg-red-100 text-red-500 flex items-center justify-center">
                        <i class="fa-solid fa-bolt text-lg"></i>
                    </span>
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold text-foreground">{{ __('client.home.deals.title') }}</h2>
                        <p class="text-sm text-muted-foreground mt-0.5">{{ __('client.home.deals.subtitle') }}</p>
                    </div>
                </div>
                <a href="{{ route('shop.index', ['is_sale' => 1]) }}"
                    class="text-sm font-medium text-primary hover:underline">{{ __('common.actions.view_all') }}</a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($saleProducts as $product)
                    @include('components.product-card', ['product' => $product])
                @endforeach
            </div>
        </section>
    @endif

    @if ($brands->isNotEmpty())
        <section class="mb-12">
            <div class="mb-5">
                <h2 class="text-xl md:text-2xl font-bold text-foreground">{{ __('client.home.brands.title') }}</h2>
                <p class="text-sm text-muted-foreground mt-0.5">{{ __('client.home.brands.subtitle') }}</p>
            </div>

            <div class="grid grid-cols-3 sm:grid-cols-5 lg:grid-cols-10 gap-3">
                @foreach ($brands as $brand)
                    <a href="{{ route('shop.index', ['branch_id' => $brand->id]) }}"
                        class="flex flex-col items-center justify-center gap-1.5 py-4 px-2 bg-card border border-border rounded-xl hover:border-primary hover:bg-primary/5 transition-all">
                        <span class="text-sm font-semibold text-foreground text-center line-clamp-1">{{ $brand->name }}</span>
                        <span class="text-[11px] text-muted-foreground">{{ $brand->products_count }}</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @if ($featuredProducts->isNotEmpty())
        <section class="mb-12">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-xl md:text-2xl font-bold text-foreground">{{ __('client.home.featured.title') }}</h2>
                    <p class="text-sm text-muted-foreground mt-0.5">{{ __('client.home.featured.subtitle') }}</p>
                </div>
                <a href="{{ route('shop.index') }}" class="text-sm font-medium text-primary hover:underline">
                    {{ __('common.actions.view_all') }}
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($featuredProducts as $product)
                    @include('components.product-card', ['product' => $product])
                @endforeach
            </div>
        </section>
    @endif

    @if ($trendingProducts->isNotEmpty())
        <section class="mb-12">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-xl md:text-2xl font-bold text-foreground">{{ __('client.home.trending.title') }}</h2>
                    <p class="text-sm text-muted-foreground mt-0.5">{{ __('client.home.trending.subtitle') }}</p>
                </div>
                <a href="{{ route('shop.index', ['sort' => 'popular']) }}"
                    class="text-sm font-medium text-primary hover:underline">{{ __('common.actions.view_all') }}</a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($trendingProducts as $product)
                    @include('components.product-card', ['product' => $product])
                @endforeach
            </div>
        </section>
    @endif

    <section class="mb-12">
        <div
            class="relative overflow-hidden rounded-3xl bg-foreground p-8 md:p-12 flex flex-col md:flex-row items-center justify-between gap-6 text-white">
            <span class="absolute -top-16 -right-10 w-64 h-64 rounded-full bg-primary/20"></span>

            <div class="relative">
                <h2 class="text-2xl md:text-3xl font-bold mb-2">{{ __('client.home.newsletter.title') }}</h2>
                <p class="text-white/60">{{ __('client.home.newsletter.subtitle') }}</p>
            </div>

            <form action="{{ route('auth.client.showFormRegister') }}" method="GET"
                class="relative flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <input type="email" name="email" placeholder="{{ __('client.home.newsletter.placeholder') }}"
                    class="flex-1 md:w-64 px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder:text-white/40 focus:outline-none focus:border-white transition-colors text-sm">
                <button type="submit"
                    class="px-6 py-3 bg-primary text-white font-semibold rounded-xl hover:bg-primary/90 transition-colors whitespace-nowrap">
                    {{ __('client.home.newsletter.submit') }}
                </button>
            </form>
        </div>
    </section>
@endsection
