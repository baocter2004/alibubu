<?php

use App\Const\SecurityConst;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\ClientAuthController;
use App\Http\Controllers\Common\LocaleController;
use Illuminate\Support\Facades\Route;

Route::get('/locale/{locale}', LocaleController::class)->name('locale.switch');

Route::middleware('auth')->group(function () {
    Route::get('/email/verify/{id}/{hash}', [ClientAuthController::class, 'verifyEmail'])
        ->middleware('signed')
        ->name('verification.verify');

    Route::get('/email/verify-success', [ClientAuthController::class, 'showVerifySuccess'])
        ->name('verification.success');

    Route::post('/email/verification-notification', [ClientAuthController::class, 'resendVerification'])
        ->middleware('throttle:' . SecurityConst::LIMITER_VERIFICATION)
        ->name('verification.send');
});

Route::controller(ClientAuthController::class)
    ->middleware('guest')
    ->group(function () {
        Route::get('/forgot-password', 'showFormForgotPassword')->name('password.request');
        Route::post('/forgot-password', 'sendResetLinkEmail')->middleware('throttle:' . SecurityConst::LIMITER_PASSWORD_EMAIL)->name('password.email');
        Route::get('/reset-password/{token}', 'showFormNewPassword')->name('password.reset');
        Route::post('/reset-password', 'reset')->middleware('throttle:' . SecurityConst::LIMITER_PASSWORD_RESET)->name('password.update');
    });

Route::controller(AdminAuthController::class)
    ->prefix('admin')
    ->middleware('guest:admin')
    ->group(function () {
        Route::get('/forgot-password', 'showFormForgotPassword')->name('admin.password.request');
        Route::post('/forgot-password', 'sendResetLinkEmail')->middleware('throttle:' . SecurityConst::LIMITER_ADMIN_PASSWORD)->name('admin.password.email');
        Route::get('/reset-password/{token}', 'showFormNewPassword')->name('admin.password.reset');
        Route::post('/reset-password', 'updatePassword')->middleware('throttle:' . SecurityConst::LIMITER_ADMIN_PASSWORD)->name('admin.password.update');
    });

Route::name('auth.')->group(function () {
    Route::name('client.')
        ->controller(ClientAuthController::class)
        ->group(function () {
            Route::middleware('guest')->group(function () {
                Route::get('/login', 'showFormLogin')->name('showFormLogin');
                Route::post('/login', 'handleLogin')->middleware('throttle:' . SecurityConst::LIMITER_LOGIN)->name('handleLogin');
                Route::get('/register', 'showFormRegister')->name('showFormRegister');
                Route::post('/register', 'handleRegister')->middleware('throttle:' . SecurityConst::LIMITER_REGISTER)->name('handleRegister');
                Route::get('/google', 'redirectToGoogle')->name('redirectToGoogle');
                Route::get('/google/callback', 'handleGoogleCallback')->name('handleGoogleCallback');
            });

            Route::post('/logout', 'logout')->middleware('auth')->name('logout');
        });

    Route::name('admin.')
        ->prefix('admin')
        ->controller(AdminAuthController::class)
        ->group(function () {
            Route::middleware('guest:admin')->group(function () {
                Route::get('/login', 'showFormLogin')->name('showFormLogin');
                Route::post('/login', 'handleLogin')->middleware('throttle:' . SecurityConst::LIMITER_ADMIN_LOGIN)->name('handleLogin');
            });

            Route::post('/logout', 'logout')->middleware('auth:admin')->name('logout');
        });
});
