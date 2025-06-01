<?php

use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\ClientAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// ================= HOME - CLIENT =====================
Route::get('/', [HomeController::class, 'index'])->name('index');
Route::get('/shops', [HomeController::class, 'shops'])->name('shops');
Route::get('/shop-detail/{id}/', [HomeController::class, 'shopDetail'])->name('shop-detail');
Route::get('/cart', [HomeController::class, 'cart'])->name('cart');
Route::get('/checkout', [HomeController::class, 'checkout'])->name('checkout');

Route::get('/about',  function () {
    return view('client.about');
})->name('about');

Route::get('/thanks-you', function () {
    return view('client.thanks-you');
})->name('thanks-you');

// ===================== AUTHENTICATE ===================
Route::name('auth.')
    ->group(function () {
        Route::name(
            'client.'
        )
            ->controller(ClientAuthController::class)
            ->group(function () {
                Route::get('/login', 'showFormLogin')->name('showFormLogin');
                Route::get('/register', 'showFormRegister')->name('showFormRegister');
                Route::get('/forgot-password', 'showFormForgotPassword')->name('showFormForgotPassword');
                Route::get('/otp', 'showFormOtp')->name('showFormOtp');
                Route::get('/new-password', 'showFormNewPassword')->name('showFormNewPassword');
            });


        Route::name('admin.')
            ->prefix('admin')
            ->controller(AdminAuthController::class)
            ->group(function () {
                Route::get('/login', 'showFormLogin')->name('showFormLogin');
                Route::get('/logout', 'logout')->middleware('auth')->name('logout');
                Route::post('/handle', 'handleLogin')->name('handleLogin');
                Route::get('/forgot-password', 'showFormForgotPassword')->name('showFormForgotPassword');
                Route::post('/send-otp', 'sendOtp')->name('sendOtp');
                Route::get('/otp', 'showFormOtp')->name('showFormOtp')->middleware('check.reset.flow');
                Route::post('/resend-otp', 'resendOtp')->name('resendOtp');

                Route::post('/verify-otp', 'verifyOtp')->name('verifyOtp');
                Route::get('/new-password', 'showFormNewPassword')->name('showFormNewPassword')->middleware('check.reset.flow');;
                Route::post('/update-password', 'updatePassword')->name('updatePassword')->middleware('check.reset.flow');;
            });
    });

// ========================= ADMIN ===========================
Route::prefix('/admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/',[DashboardController::class,'dashboard'])->name('dashboard');
    });