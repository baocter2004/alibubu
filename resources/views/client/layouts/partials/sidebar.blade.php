<div id="mobile-menu" class="fixed inset-0 z-50 hidden">
    <div id="menu-backdrop" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

    <div id="menu-panel"
        class="absolute top-0 left-0 bottom-0 w-75 bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300">

        <div class="flex items-center justify-between px-5 py-4 border-b border-border">
            <div class="flex items-center gap-2">
                <span
                    class="w-7 h-7 rounded-lg bg-primary flex items-center justify-center text-white font-bold text-sm">A</span>
                <span class="font-bold text-foreground">{{ __('common.app_name') }}</span>
            </div>
            <button id="menu-close" type="button"
                class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-muted transition-colors"
                aria-label="{{ __('client.nav.close_menu') }}">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <div class="px-5 py-4 border-b border-border">
            <form action="{{ route('shop.index') }}" method="GET" class="relative">
                <i
                    class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground"></i>
                <input type="search" name="keyword" value="{{ request('keyword') }}"
                    placeholder="{{ __('client.nav.search_placeholder') }}"
                    class="w-full pl-9 pr-4 py-2.5 text-sm bg-muted border border-transparent rounded-xl focus:outline-none focus:bg-white focus:border-primary transition-all">
            </form>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-3">
            <p class="px-3 text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-2">
                {{ __('client.nav.menu') }}
            </p>

            @php
                $menuItems = [
                    ['route' => 'index', 'params' => [], 'icon' => 'fa-house', 'label' => __('client.nav.home')],
                    ['route' => 'shop.index', 'params' => [], 'icon' => 'fa-display', 'label' => __('client.nav.shop')],
                    ['route' => 'shop.index', 'params' => ['is_sale' => 1], 'icon' => 'fa-star', 'label' => __('client.nav.deals')],
                    ['route' => 'about', 'params' => [], 'icon' => 'fa-circle-info', 'label' => __('client.nav.about')],
                ];
            @endphp

            @foreach ($menuItems as $item)
                <a href="{{ route($item['route'], $item['params']) }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium mb-1 transition-colors {{ request()->routeIs($item['route']) && empty($item['params']) ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:text-foreground hover:bg-muted' }}">
                    <i class="fa-solid {{ $item['icon'] }} w-4 text-center"></i>
                    {{ $item['label'] }}
                </a>
            @endforeach

            <div class="border-t border-border my-4"></div>
            <p class="px-3 text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-2">
                {{ __('client.nav.account') }}
            </p>

            <a href="{{ route('cart.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-muted-foreground hover:text-foreground hover:bg-muted transition-colors mb-1">
                <i class="fa-solid fa-bag-shopping w-4 text-center"></i>
                {{ __('client.nav.cart') }}
                @if ($cartCount > 0)
                    <span
                        class="ml-auto bg-primary text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $cartCount }}</span>
                @endif
            </a>

            @auth
                <span class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-foreground font-medium">
                    <i class="fa-regular fa-user w-4 text-center"></i>
                    {{ Auth::user()->fullname }}
                </span>

                @foreach ([['account.profile', 'fa-user', __('client.account.nav.profile')], ['account.orders', 'fa-receipt', __('client.account.nav.orders')], ['account.addresses', 'fa-location-dot', __('client.account.nav.addresses')]] as [$route, $icon, $label])
                    <a href="{{ route($route) }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-muted-foreground hover:text-foreground hover:bg-muted transition-colors mb-1">
                        <i class="fa-solid {{ $icon }} w-4 text-center"></i>
                        {{ $label }}
                    </a>
                @endforeach
                <form action="{{ route('auth.client.logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-muted-foreground hover:text-foreground hover:bg-muted transition-colors">
                        <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>
                        {{ __('common.actions.logout') }}
                    </button>
                </form>
            @else
                <a href="{{ route('auth.client.showFormLogin') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-muted-foreground hover:text-foreground hover:bg-muted transition-colors mb-1">
                    <i class="fa-regular fa-user w-4 text-center"></i>
                    {{ __('common.actions.login') }}
                </a>
                <a href="{{ route('auth.client.showFormRegister') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-muted-foreground hover:text-foreground hover:bg-muted transition-colors">
                    <i class="fa-solid fa-user-plus w-4 text-center"></i>
                    {{ __('common.actions.register') }}
                </a>
            @endauth
        </nav>
    </div>
</div>
