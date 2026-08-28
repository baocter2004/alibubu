<header class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-border shadow-sm">
    <div class="hidden md:block bg-foreground text-white text-xs py-1.5">
        <div class="max-w-7xl mx-auto px-4 flex justify-between items-center">
            <span><i class="fa-solid fa-truck mr-1"></i> {{ __('client.nav.free_shipping') }}</span>
            <div class="flex gap-4">
                <a href="{{ route('about') }}" class="hover:text-primary transition-colors">
                    {{ __('client.nav.about') }}
                </a>
                <a href="{{ route('shop.index', ['is_sale' => 1]) }}" class="hover:text-primary transition-colors">
                    {{ __('client.nav.deals') }}
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center gap-4 h-16">
            <div class="flex items-center gap-4">
                <button id="menu-open" type="button"
                    class="md:hidden flex items-center justify-center w-10 h-10 rounded-lg hover:bg-muted transition-colors"
                    aria-label="{{ __('client.nav.open_menu') }}">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>

                <a href="{{ route('index') }}" class="flex items-center gap-2 shrink-0">
                    <span
                        class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center text-white font-bold text-sm">A</span>
                    <span class="font-bold text-lg text-foreground tracking-tight">{{ __('common.app_name') }}</span>
                </a>

                <nav class="hidden md:flex items-center gap-6 ml-4">
                    <a href="{{ route('index') }}"
                        class="text-sm font-medium transition-colors {{ request()->routeIs('index') ? 'text-primary' : 'text-muted-foreground hover:text-foreground' }}">
                        {{ __('client.nav.home') }}
                    </a>
                    <a href="{{ route('shop.index') }}"
                        class="text-sm font-medium transition-colors {{ request()->routeIs('shop.*') ? 'text-primary' : 'text-muted-foreground hover:text-foreground' }}">
                        {{ __('client.nav.shop') }}
                    </a>
                    <a href="{{ route('shop.index', ['is_sale' => 1]) }}"
                        class="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors">
                        {{ __('client.nav.deals') }}
                    </a>
                    <a href="{{ route('about') }}"
                        class="text-sm font-medium transition-colors {{ request()->routeIs('about') ? 'text-primary' : 'text-muted-foreground hover:text-foreground' }}">
                        {{ __('client.nav.about') }}
                    </a>
                </nav>
            </div>

            <form action="{{ route('shop.index') }}" method="GET" class="hidden lg:block flex-1 max-w-sm">
                <div class="relative">
                    <i
                        class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground"></i>
                    <input type="search" name="keyword" value="{{ request('keyword') }}"
                        placeholder="{{ __('client.nav.search_placeholder') }}"
                        class="w-full pl-9 pr-4 py-2 text-sm bg-muted border border-transparent rounded-xl focus:outline-none focus:bg-white focus:border-primary transition-all">
                </div>
            </form>

            <div class="flex items-center gap-1">
                @include('components.locale-switcher')

                <a href="{{ route('cart.index') }}"
                    class="flex items-center justify-center w-10 h-10 rounded-lg hover:bg-muted transition-colors relative"
                    aria-label="{{ __('client.nav.cart') }}">
                    <i class="fa-solid fa-bag-shopping text-lg"></i>
                    @if ($cartCount > 0)
                        <span
                            class="absolute -top-0.5 -right-0.5 bg-primary text-white text-[10px] font-bold min-w-4.5 h-4.5 rounded-full flex items-center justify-center px-1 border-2 border-white">
                            {{ $cartCount > 99 ? '99+' : $cartCount }}
                        </span>
                    @endif
                </a>

                @auth
                    <div class="hidden md:flex items-center gap-1">
                        <span class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium text-foreground">
                            <i class="fa-regular fa-user"></i>
                            {{ Auth::user()->fullname }}
                        </span>
                        <form action="{{ route('auth.client.logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-muted-foreground hover:text-foreground hover:bg-muted transition-colors">
                                <i class="fa-solid fa-right-from-bracket"></i>
                                {{ __('common.actions.logout') }}
                            </button>
                        </form>
                    </div>
                @else
                    <div class="hidden sm:flex items-center gap-2 ml-1">
                        <a href="{{ route('auth.client.showFormLogin') }}"
                            class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-foreground hover:bg-muted transition-colors">
                            <i class="fa-regular fa-user"></i>
                            {{ __('common.actions.login') }}
                        </a>
                        <a href="{{ route('auth.client.showFormRegister') }}"
                            class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-primary hover:bg-primary/90 shadow-sm transition-colors">
                            <i class="fa-solid fa-user-plus"></i>
                            {{ __('common.actions.register') }}
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</header>
