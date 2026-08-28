@php
    $links = [
        ['route' => 'account.profile', 'icon' => 'fa-user', 'label' => __('client.account.nav.profile')],
        ['route' => 'account.orders', 'icon' => 'fa-receipt', 'label' => __('client.account.nav.orders')],
        ['route' => 'account.wishlist', 'icon' => 'fa-heart', 'label' => __('client.account.nav.wishlist')],
        ['route' => 'account.addresses', 'icon' => 'fa-location-dot', 'label' => __('client.account.nav.addresses')],
    ];
@endphp

<aside class="lg:w-64 shrink-0">
    <div class="bg-card border border-border rounded-2xl p-5 mb-4">
        <div class="flex items-center gap-3">
            <span class="w-12 h-12 rounded-full bg-primary/10 text-primary text-lg font-bold flex items-center justify-center">
                {{ Str::upper(Str::substr(Auth::user()->fullname, 0, 1)) }}
            </span>
            <span class="min-w-0">
                <span class="block font-semibold text-foreground truncate">{{ Auth::user()->fullname }}</span>
                <span class="block text-xs text-muted-foreground truncate">{{ Auth::user()->email }}</span>
            </span>
        </div>
    </div>

    <nav class="bg-card border border-border rounded-2xl p-2">
        @foreach ($links as $link)
            <a href="{{ route($link['route']) }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs($link['route'] . '*') ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:text-foreground hover:bg-muted' }}">
                <i class="fa-solid {{ $link['icon'] }} w-4 text-center"></i>
                {{ $link['label'] }}
            </a>
        @endforeach

        <form action="{{ route('auth.client.logout') }}" method="POST">
            @csrf
            <button type="submit"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-muted-foreground hover:text-red-600 hover:bg-red-50 transition-colors">
                <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>
                {{ __('common.actions.logout') }}
            </button>
        </form>
    </nav>
</aside>
