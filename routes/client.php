<?php

use App\Http\Controllers\Client\AccountController;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Client\CheckoutController;
use App\Http\Controllers\Client\CouponController;
use App\Http\Controllers\Client\ReviewController;
use App\Http\Controllers\Client\ShopController;
use App\Http\Controllers\Client\WishlistController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('index');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/thanks-you', [HomeController::class, 'thankYou'])->name('thanks-you');

Route::prefix('shop')->name('shop.')->group(function () {
    Route::controller(ShopController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{slug}', 'show')->name('show');
    });

    Route::post('/{slug}/reviews', [ReviewController::class, 'store'])
        ->middleware('auth')
        ->name('reviews.store');

    Route::post('/{slug}/wishlist', [WishlistController::class, 'toggle'])
        ->middleware('auth')
        ->name('wishlist.toggle');
});

Route::prefix('cart')->name('cart.')->controller(CartController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/', 'store')->name('store');
    Route::patch('/{key}', 'update')->name('update');
    Route::delete('/clear', 'clear')->name('clear');
    Route::delete('/{key}', 'destroy')->name('destroy');
});

Route::prefix('account')
    ->name('account.')
    ->middleware('auth')
    ->controller(AccountController::class)
    ->group(function () {
        Route::get('/', 'profile')->name('profile');
        Route::patch('/profile', 'updateProfile')->name('profile.update');
        Route::patch('/password', 'updatePassword')->name('password.update');
        Route::get('/orders', 'orders')->name('orders');
        Route::get('/orders/{id}', 'showOrder')->name('orders.show');
        Route::get('/wishlist', [WishlistController::class, 'index'])->withoutMiddleware([])->name('wishlist');
        Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
        Route::get('/addresses', 'addresses')->name('addresses');
        Route::post('/addresses', 'storeAddress')->name('addresses.store');
        Route::patch('/addresses/{id}', 'updateAddress')->name('addresses.update');
        Route::delete('/addresses/{id}', 'destroyAddress')->name('addresses.destroy');
    });

Route::prefix('coupon')->name('coupon.')->controller(CouponController::class)->group(function () {
    Route::post('/', 'store')->name('store');
    Route::delete('/', 'destroy')->name('destroy');
});

Route::prefix('checkout')->name('checkout.')->controller(CheckoutController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/', 'store')->name('store');
});
