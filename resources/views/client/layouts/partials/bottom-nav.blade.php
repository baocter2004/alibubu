@php
    $items = [
        ['route' => 'index', 'params' => [], 'active' => 'index', 'icon' => 'fa-house', 'label' => __('client.nav.home')],
        ['route' => 'shop.index', 'params' => [], 'active' => 'shop.*', 'icon' => 'fa-grip', 'label' => __('client.nav.shop')],
        ['route' => 'shop.index', 'params' => ['is_sale' => 1], 'active' => null, 'icon' => 'fa-fire', 'label' => __('client.nav.deals')],
        ['route' => 'cart.index', 'params' => [], 'active' => 'cart.*', 'icon' => 'fa-bag-shopping', 'label' => __('client.nav.cart'), 'badge' => $cartCount],
        ['route' => Auth::check() ? 'account.profile' : 'auth.client.showFormLogin', 'params' => [], 'active' => 'account.*', 'icon' => 'fa-user', 'label' => __('client.nav.account')],
    ];
@endphp

<nav class="md:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur border-t border-border pb-[env(safe-area-inset-bottom)]">
    <div class="grid grid-cols-5">
        @foreach ($items as $item)
            @php $isActive = $item['active'] && request()->routeIs($item['active']); @endphp
            <a href="{{ route($item['route'], $item['params']) }}"
                class="relative flex flex-col items-center gap-1 py-2.5 text-[11px] font-medium transition-colors {{ $isActive ? 'text-primary' : 'text-muted-foreground' }}">
                <span class="relative">
                    <i class="fa-solid {{ $item['icon'] }} text-base"></i>
                    @if (! empty($item['badge']) && $item['badge'] > 0)
                        <span
                            class="absolute -top-1.5 -right-2.5 min-w-4 h-4 px-1 bg-primary text-white text-[9px] font-bold rounded-full flex items-center justify-center">
                            {{ $item['badge'] > 9 ? '9+' : $item['badge'] }}
                        </span>
                    @endif
                </span>
                {{ $item['label'] }}
            </a>
        @endforeach
    </div>
</nav>
