@extends('client.layouts.app')

@section('title', __('common.app_name') . ' - ' . __('client.about.title'))

@section('content')
    <nav class="flex items-center gap-2 text-sm text-muted-foreground mb-6">
        <a href="{{ route('index') }}" class="hover:text-primary transition-colors">{{ __('client.nav.home') }}</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <span class="text-foreground font-medium">{{ __('client.about.title') }}</span>
    </nav>

    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-primary via-blue-600 to-indigo-700 mb-8">
        <span class="absolute -top-20 -right-16 w-72 h-72 rounded-full bg-white/10"></span>
        <span class="absolute -bottom-24 -left-16 w-80 h-80 rounded-full bg-white/5"></span>

        <div class="relative px-6 py-12 md:px-12 md:py-16 text-white max-w-3xl">
            <span class="inline-flex items-center gap-2 px-3 py-1 text-xs font-semibold rounded-full bg-white/15 backdrop-blur mb-5">
                <i class="fa-solid fa-shield-halved"></i>
                {{ __('client.about.hero_badge') }}
            </span>

            <h1 class="text-3xl md:text-5xl font-bold leading-tight mb-4">{{ __('client.about.hero_title') }}</h1>
            <p class="text-white/75 leading-relaxed mb-7">{{ __('client.about.hero_text') }}</p>

            <a href="{{ route('shop.index') }}"
                class="inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold text-primary bg-white rounded-xl hover:bg-white/90 transition-colors">
                <i class="fa-solid fa-bag-shopping"></i>
                {{ __('client.about.cta_shop') }}
            </a>
        </div>
    </section>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-12">
        @php
            $cards = [
                ['icon' => 'fa-mobile-screen-button', 'value' => number_format($stats['products']), 'key' => 'products'],
                ['icon' => 'fa-award', 'value' => number_format($stats['brands']), 'key' => 'brands'],
                ['icon' => 'fa-users', 'value' => number_format($stats['customers']) . '+', 'key' => 'customers'],
                ['icon' => 'fa-calendar-check', 'value' => max(1, now()->year - 2019), 'key' => 'years'],
            ];
        @endphp

        @foreach ($cards as $card)
            <div class="bg-card border border-border rounded-2xl p-5 text-center">
                <span class="inline-flex w-12 h-12 rounded-xl bg-primary/10 text-primary items-center justify-center mb-3">
                    <i class="fa-solid {{ $card['icon'] }}"></i>
                </span>
                <p class="text-2xl font-bold text-foreground">{{ $card['value'] }}</p>
                <p class="text-xs text-muted-foreground mt-1">{{ __('client.about.stats.' . $card['key']) }}</p>
            </div>
        @endforeach
    </div>

    <section class="mb-12">
        <div class="text-center mb-8">
            <h2 class="text-xl md:text-2xl font-bold text-foreground">{{ __('client.about.values_title') }}</h2>
            <p class="text-sm text-muted-foreground mt-1">{{ __('client.about.values_subtitle') }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            @foreach ([['fa-certificate', 'genuine'], ['fa-tags', 'price'], ['fa-headset', 'support']] as [$icon, $key])
                <div class="bg-card border border-border rounded-2xl p-6 hover:border-primary/40 hover:shadow-md transition-all">
                    <span class="inline-flex w-12 h-12 rounded-xl bg-primary/10 text-primary items-center justify-center mb-4">
                        <i class="fa-solid {{ $icon }} text-lg"></i>
                    </span>
                    <h3 class="font-bold text-foreground mb-2">{{ __('client.about.values.' . $key . '_title') }}</h3>
                    <p class="text-sm text-muted-foreground leading-relaxed">
                        {{ __('client.about.values.' . $key . '_text') }}
                    </p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-12">
        <div class="bg-card border border-border rounded-2xl p-6 md:p-8">
            <h2 class="text-xl font-bold text-foreground mb-4">{{ __('client.about.story_title') }}</h2>
            <p class="text-muted-foreground leading-relaxed mb-4">{{ __('client.about.story_text_1') }}</p>
            <p class="text-muted-foreground leading-relaxed">{{ __('client.about.story_text_2') }}</p>
        </div>

        <div class="bg-card border border-border rounded-2xl p-6 md:p-8">
            <h2 class="text-xl font-bold text-foreground mb-1">{{ __('client.about.contact_title') }}</h2>
            <p class="text-sm text-muted-foreground mb-5">{{ __('client.about.contact_subtitle') }}</p>

            <ul class="space-y-4">
                @foreach ([['fa-location-dot', __('client.footer.address')], ['fa-phone', __('client.footer.hotline')], ['fa-envelope', __('client.footer.email')]] as [$icon, $value])
                    <li class="flex items-start gap-3">
                        <span class="w-10 h-10 shrink-0 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                            <i class="fa-solid {{ $icon }} text-sm"></i>
                        </span>
                        <span class="text-sm text-muted-foreground pt-2.5">{{ $value }}</span>
                    </li>
                @endforeach
            </ul>

            <div class="flex items-center gap-3 mt-6 pt-6 border-t border-border">
                @foreach (['facebook-f', 'instagram', 'tiktok', 'youtube'] as $social)
                    <a href="#" aria-label="{{ ucfirst($social) }}"
                        class="w-10 h-10 rounded-xl bg-muted text-muted-foreground flex items-center justify-center hover:bg-primary hover:text-white transition-colors">
                        <i class="fa-brands fa-{{ $social }}"></i>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endsection
