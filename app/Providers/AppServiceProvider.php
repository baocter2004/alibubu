<?php

namespace App\Providers;

use App\Models\Category;
use App\Services\Client\CartService;
use App\Services\Client\CompareService;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CartService::class);
        $this->app->singleton(CompareService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        RedirectIfAuthenticated::redirectUsing(function (Request $request) {
            return $request->is('admin', 'admin/*')
                ? route('admin.dashboard')
                : route('index');
        });

        View::composer('client.layouts.*', function ($view) {
            $view->with('cartCount', app(CartService::class)->count());
            $view->with('compareItems', app(CompareService::class)->summary());
            $view->with('navCategories', Cache::remember(
                'nav.categories.' . app()->getLocale(),
                now()->addMinutes(10),
                fn () => Category::query()
                    ->where('is_active', true)
                    ->whereNull('parent_id')
                    ->orderBy('ordinal')
                    ->get(['id', 'name', 'icon'])
            ));
        });
    }
}
