<div id="mobile-menu" class="fixed inset-0 z-50 hidden md:hidden">
    <div id="menu-backdrop" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

    <div id="menu-panel"
        class="absolute top-0 left-0 bottom-0 w-80 max-w-[85vw] bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300">

        <div class="flex items-center justify-between px-5 py-4 border-b border-border">
            <span class="flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-primary flex items-center justify-center text-white font-bold text-sm">A</span>
                <span class="font-bold text-foreground">{{ __('common.app_name') }}</span>
            </span>
            <button id="menu-close" type="button"
                class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-muted transition-colors"
                aria-label="{{ __('client.nav.close_menu') }}">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <div class="px-5 py-4 border-b border-border">
            <form action="{{ route('shop.index') }}" method="GET" class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground"></i>
                <input type="search" name="keyword" value="{{ request('keyword') }}"
                    placeholder="{{ __('client.nav.search_placeholder') }}"
                    class="w-full pl-9 pr-4 py-2.5 text-sm bg-muted border border-transparent rounded-xl focus:outline-none focus:bg-white focus:border-primary transition-all">
            </form>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4">
            <p class="px-3 pb-2 text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                {{ __('client.nav.categories') }}
            </p>

            @forelse ($navCategories as $category)
                <a href="{{ route('shop.index', ['category_id' => $category->id]) }}"
                    class="flex items-center gap-3 px-3 py-3 rounded-xl text-sm font-medium text-foreground hover:bg-muted transition-colors">
                    <span class="w-9 h-9 rounded-lg bg-primary/10 text-primary flex items-center justify-center shrink-0">
                        <i class="{{ $category->icon ?: 'fa-solid fa-tag' }} text-sm"></i>
                    </span>
                    <span class="flex-1 truncate">{{ $category->name }}</span>
                    <i class="fa-solid fa-chevron-right text-[10px] text-muted-foreground"></i>
                </a>
            @empty
                <p class="px-3 py-6 text-sm text-muted-foreground text-center">{{ __('common.empty.title') }}</p>
            @endforelse

            <div class="border-t border-border my-4"></div>

            <a href="{{ route('about') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-muted-foreground hover:text-foreground hover:bg-muted transition-colors">
                <i class="fa-solid fa-circle-info w-4 text-center"></i>
                {{ __('client.nav.about') }}
            </a>

            @auth
                <form action="{{ route('auth.client.logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-muted-foreground hover:text-red-600 hover:bg-red-50 transition-colors">
                        <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>
                        {{ __('common.actions.logout') }}
                    </button>
                </form>
            @else
                <a href="{{ route('auth.client.showFormRegister') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-muted-foreground hover:text-foreground hover:bg-muted transition-colors">
                    <i class="fa-solid fa-user-plus w-4 text-center"></i>
                    {{ __('common.actions.register') }}
                </a>
            @endauth
        </nav>
    </div>
</div>
