<?php

namespace App\Providers;

use App\Const\OrderConst;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class CheckoutServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for(OrderConst::LIMITER_CHECKOUT, fn (Request $request) => [
            Limit::perMinute((int) config('order.rate_limits.checkout_per_minute'))->by($this->key($request)),
            Limit::perHour((int) config('order.rate_limits.checkout_per_hour'))->by($this->key($request)),
        ]);

        RateLimiter::for(OrderConst::LIMITER_COUPON, fn (Request $request) => [
            Limit::perMinute((int) config('order.rate_limits.coupon_per_minute'))->by($this->key($request)),
        ]);

        RateLimiter::for(OrderConst::LIMITER_CART, fn (Request $request) => [
            Limit::perMinute((int) config('order.rate_limits.cart_per_minute'))->by($this->key($request)),
        ]);

        RateLimiter::for(OrderConst::LIMITER_PAYMENT_RETRY, fn (Request $request) => [
            Limit::perMinute((int) config('order.rate_limits.payment_retry_per_minute'))->by($this->key($request)),
        ]);
    }

    protected function key(Request $request): string
    {
        return (string) ($request->user()?->getAuthIdentifier() ?: $request->ip());
    }
}
