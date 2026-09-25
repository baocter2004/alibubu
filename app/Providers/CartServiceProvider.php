<?php

namespace App\Providers;

use App\Services\Client\CartService;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class CartServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::listen(Login::class, function (Login $event) {
            if ($event->guard !== 'user') {
                return;
            }

            app(CartService::class)->mergeGuestCartIntoUser();
        });
    }
}
