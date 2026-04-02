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
        <li class="sidebar-items">
            <a href="" class="sidebar-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <i class="fa-solid text-lg fa-box"></i>
                Products
            </a>
        </li>
        <li class="sidebar-items">
            <a href="" class="sidebar-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <i class="fa-solid text-lg fa-cart-shopping"></i>
                Orders
            </a>
        </li>
        <li class="sidebar-items">
            <a href="" class="sidebar-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                <i class="fa-solid text-lg fa-users"></i>
                Customers
            </a>
        </li>
    </ul>
</aside>
