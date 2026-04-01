<?php

use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\ClientAuthController;
use Illuminate\Support\Facades\Route;

// ================ TEST  =====================
Route::get('/test', function () {
    return view('admin.test');
});

// ====================  VERIFY EMAIL ===================
Route::get('/email/verify/{id}/{hash}', [ClientAuthController::class, 'verifyEmail'])->middleware(['auth', 'signed'])
    ->name('verification.verify');
Route::get('/email/verify-success', [ClientAuthController::class, 'showVerifySuccess'])
    ->middleware('auth')
    ->name('verification.success');

// ===================== AUTHENTICATE ===================
Route::get('/new-password', [ClientAuthController::class, 'showFormNewPassword'])->name('password.reset');
Route::get('/new-password/{token}/{email}', [ClientAuthController::class, 'showFormNewPassword'])->name('password.reset');

Route::name('auth.')
    ->group(function () {
        Route::name('client.')
            ->controller(ClientAuthController::class)
            ->group(function () {
                Route::get('/login', 'showFormLogin')->name('showFormLogin');
                Route::post('/login', 'handleLogin')->name('handleLogin');
                Route::get('/google', 'redirectToGoogle')->name('redirectToGoogle');
                Route::get('/google/callback', 'handleGoogleCallback')->name('handleGoogleCallback');
                Route::get('/register', 'showFormRegister')->name('showFormRegister');
                Route::post('/register', 'handleRegister')->name('handleRegister');
                Route::get('/forgot-password', 'showFormForgotPassword')->name('showFormForgotPassword');
                Route::post('/forgot-password', 'sendResetLinkEmail')->name('sendResetLinkEmail');
                Route::get('/reset-password', 'showResetPassword')->name('showResetPassword');
                Route::post('/reset-password', 'reset')->name('reset');
                Route::post('/logout', 'logout')->name('logout');
            });


        Route::name('admin.')
            ->prefix('admin')
            ->controller(AdminAuthController::class)
            ->group(function () {
                Route::get('/login', 'showFormLogin')->name('showFormLogin');
                Route::post('/login', 'handleLogin')->name('handleLogin');
                Route::post('/logout', 'logout')->middleware('auth')->name('logout');
                Route::post('/handle', 'handleLogin')->name('handleLogin');
                Route::get('/forgot-password', 's   howFormForgotPassword')->name('showFormForgotPassword');
                Route::post('/send-otp', 'sendOtp')->name('sendOtp');
                Route::get('/otp', 'showFormOtp')->name('showFormOtp');
                Route::post('/resend-otp', 'resendOtp')->name('resendOtp');

                Route::post('/verify-otp', 'verifyOtp')->name('verifyOtp');
                Route::get('/new-password', 'showFormNewPassword')->name('showFormNewPassword');
                Route::post('/update-password', 'updatePassword')->name('updatePassword');
            });
    });
