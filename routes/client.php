<?php

// ================= HOME - CLIENT =====================

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('index');
Route::get('/shops', [HomeController::class, 'shops'])->name('shops');
Route::get('/shop-detail/{id}/', [HomeController::class, 'shopDetail'])->name('shop-detail');
Route::get('/cart', [HomeController::class, 'cart'])->name('cart');
Route::get('/checkout', [HomeController::class, 'checkout'])->name('checkout');

Route::get('/about',  function () {
    return view('client.pages.about');
})->name('about');

Route::get('/thanks-you', function () {
    return view('client.pages.thank-you');
})->name('thanks-you');
