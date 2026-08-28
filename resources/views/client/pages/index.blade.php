@extends('client.layouts.app')

@section('title', __('common.app_name') . ' - ' . __('client.home.title'))

@section('content')
    <section
        class="rounded-2xl overflow-hidden bg-gradient-to-br from-blue-50 via-white to-blue-50 border border-border mb-10">
        <div class="flex flex-col md:flex-row items-center gap-8 px-6 py-10 md:px-12 md:py-14">
            <div class="flex-1 text-center md:text-left">
                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-primary/10 text-primary mb-4">
                    <i class="fa-solid fa-fire mr-1"></i> {{ __('client.home.badge') }}
                </span>
                <h1 class="text-3xl md:text-5xl font-bold text-foreground leading-tight mb-4">
                    {{ __('client.home.heading') }}<br>
                    <span class="text-primary">{{ __('client.home.heading_highlight') }}</span>
                </h1>
                <p class="text-muted-foreground mb-7 max-w-md mx-auto md:mx-0">{{ __('client.home.description') }}</p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center md:justify-start">
                    <a href="{{ route('shop.index') }}"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary text-white font-semibold rounded-xl hover:bg-primary/90 transition-colors">
                        {{ __('client.home.cta_primary') }}
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    <a href="{{ route('shop.index', ['is_sale' => 1]) }}"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white text-foreground font-semibold rounded-xl border border-border hover:bg-muted transition-colors">
                        {{ __('client.home.cta_secondary') }}
                    </a>
                </div>
            </div>
            <div class="flex-1 flex justify-center">
                <div
                    class="w-64 h-64 md:w-80 md:h-80 bg-white rounded-2xl shadow-lg flex items-center justify-center border border-border">
                    <i class="fa-solid fa-bag-shopping text-8xl text-primary/20"></i>
                </div>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
        @php
            $stats = [
                ['icon' => 'fa-store', 'value' => '10.000+', 'label' => __('client.home.stats.products')],
                ['icon' => 'fa-users', 'value' => '50.000+', 'label' => __('client.home.stats.customers')],
                ['icon' => 'fa-star', 'value' => '4.9/5', 'label' => __('client.home.stats.rating')],
                ['icon' => 'fa-truck', 'value' => '24h', 'label' => __('client.home.stats.delivery')],
            ];
        @endphp

        @foreach ($stats as $stat)
            <div class="bg-card border border-border rounded-xl p-4 text-center">
                <div class="text-2xl mb-1 text-primary"><i class="fa-solid {{ $stat['icon'] }}"></i></div>
                <div class="text-xl font-bold text-foreground">{{ $stat['value'] }}</div>
                <div class="text-xs text-muted-foreground">{{ $stat['label'] }}</div>
            </div>
        @endforeach
    </div>

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

            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3">
                @foreach ($categories as $category)
                    <a href="{{ route('shop.index', ['category_id' => $category->id]) }}"
                        class="flex flex-col items-center gap-2 p-3 bg-card border border-border rounded-xl hover:border-primary hover:bg-primary/5 transition-all group">
                        <i
                            class="{{ $category->icon ?: 'fa-solid fa-tag' }} text-2xl text-muted-foreground group-hover:text-primary group-hover:scale-110 transition-all"></i>
                        <span
                            class="text-xs font-medium text-foreground text-center line-clamp-2">{{ $category->name }}</span>
                        <span class="text-[10px] text-muted-foreground">
                            {{ __('client.home.categories.count', ['count' => $category->products_count]) }}
                        </span>
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
                    <x-product-card :product="$product" />
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
                    class="text-sm font-medium text-primary hover:underline">
                    {{ __('common.actions.view_all') }}
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($trendingProducts as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </section>
    @endif

    <section class="mb-12">
        <div
            class="rounded-2xl bg-primary p-8 md:p-12 flex flex-col md:flex-row items-center justify-between gap-6 text-white">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold mb-2">{{ __('client.home.newsletter.title') }}</h2>
                <p class="text-white/70">{{ __('client.home.newsletter.subtitle') }}</p>
            </div>
            <form action="{{ route('auth.client.showFormRegister') }}" method="GET"
                class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <input type="email" name="email" placeholder="{{ __('client.home.newsletter.placeholder') }}"
                    class="flex-1 md:w-64 px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder:text-white/50 focus:outline-none focus:border-white transition-colors text-sm">
                <button type="submit"
                    class="px-6 py-3 bg-white text-primary font-semibold rounded-xl hover:bg-white/90 transition-colors whitespace-nowrap">
                    {{ __('client.home.newsletter.submit') }}
                </button>
            </form>
        </div>
    </section>
@endsection
