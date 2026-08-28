<footer class="bg-foreground text-white mt-16">
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="col-span-2 md:col-span-1">
                <div class="flex items-center gap-2 mb-4">
                    <span
                        class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center text-white font-bold text-sm">A</span>
                    <span class="font-bold text-lg">{{ __('common.app_name') }}</span>
                </div>
                <p class="text-sm text-white/60 leading-relaxed mb-5">{{ __('client.footer.tagline') }}</p>
                <div class="flex items-center gap-3">
                    @foreach (['facebook-f', 'instagram', 'tiktok', 'youtube'] as $social)
                        <a href="#" aria-label="{{ ucfirst($social) }}"
                            class="w-9 h-9 rounded-lg bg-white/10 flex items-center justify-center hover:bg-primary transition-colors">
                            <i class="fa-brands fa-{{ $social }}"></i>
                        </a>
                    @endforeach
                </div>
            </div>

            <div>
                <h4 class="text-xs font-semibold uppercase tracking-widest text-white/40 mb-4">
                    {{ __('client.footer.shop') }}
                </h4>
                <ul class="space-y-3">
                    @php
                        $shopLinks = [
                            ['url' => route('shop.index'), 'label' => __('client.footer.links.all_products')],
                            ['url' => route('shop.index', ['is_sale' => 1]), 'label' => __('client.footer.links.deals')],
                            ['url' => route('shop.index', ['sort' => 'newest']), 'label' => __('client.footer.links.new_arrivals')],
                            ['url' => route('shop.index', ['sort' => 'popular']), 'label' => __('client.footer.links.best_sellers')],
                        ];
                    @endphp
                    @foreach ($shopLinks as $link)
                        <li>
                            <a href="{{ $link['url'] }}"
                                class="text-sm text-white/60 hover:text-white transition-colors">{{ $link['label'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h4 class="text-xs font-semibold uppercase tracking-widest text-white/40 mb-4">
                    {{ __('client.footer.support') }}
                </h4>
                <ul class="space-y-3">
                    @foreach (['help_center', 'shipping', 'returns', 'warranty'] as $link)
                        <li>
                            <a href="#" class="text-sm text-white/60 hover:text-white transition-colors">
                                {{ __('client.footer.links.' . $link) }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h4 class="text-xs font-semibold uppercase tracking-widest text-white/40 mb-4">
                    {{ __('client.footer.contact') }}
                </h4>
                <ul class="space-y-3">
                    <li class="flex items-start gap-2.5 text-sm text-white/60">
                        <i class="fa-solid fa-location-dot mt-0.5 shrink-0 text-primary"></i>
                        {{ __('client.footer.address') }}
                    </li>
                    <li class="flex items-center gap-2.5 text-sm text-white/60">
                        <i class="fa-solid fa-phone shrink-0 text-primary"></i>
                        {{ __('client.footer.hotline') }}
                    </li>
                    <li class="flex items-center gap-2.5 text-sm text-white/60">
                        <i class="fa-solid fa-envelope shrink-0 text-primary"></i>
                        {{ __('client.footer.email') }}
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="max-w-7xl mx-auto px-4 py-5 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-white/40 text-center sm:text-left">
                {{ __('client.footer.copyright', ['year' => now()->year]) }}
            </p>
            <div class="flex items-center gap-4">
                @foreach (['terms', 'privacy', 'cookie'] as $link)
                    <a href="#" class="text-xs text-white/40 hover:text-white/70 transition-colors">
                        {{ __('client.footer.' . $link) }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</footer>
