<aside class="bg-foreground" id="sidebar">
    <div class="header-sidebar">
        <h1 class="header-sidebar-title">Alibubu</h1>
        <span class="header-sidebar-note">ENTERPRISE ADMIN</span>
    </div>
    <ul class="w-full px-3 space-y-2">
        <li class="sidebar-items @if (Route::is('admin.dashboard')) active @endif">
            <a href="{{ route('admin.dashboard') }}"
                class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid text-lg fa-chart-bar"></i>
                Dashboard
            </a>
        </li>
        <li class="sidebar-items {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
            <a href="" class="sidebar-link">
                <i class="fa-solid text-lg fa-box"></i>
                Products
            </a>
        </li>
        <li class="sidebar-items {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
            <a href="" class="sidebar-link">
                <i class="fa-solid text-lg fa-cart-shopping"></i>
                Orders
            </a>
        </li>
        <li class="sidebar-items {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <a href="{{ route('admin.users.index') }}" class="sidebar-link">
                <i class="fa-solid text-lg fa-users"></i>
                Users
            </a>
        </li>
    </ul>
</aside>
