<?php

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
});

Route::controller(ClientAuthController::class)
    ->middleware('guest')
    ->group(function () {
        Route::get('/forgot-password', 'showFormForgotPassword')->name('password.request');
        Route::post('/forgot-password', 'sendResetLinkEmail')->name('password.email');
        Route::get('/reset-password/{token}', 'showFormNewPassword')->name('password.reset');
        Route::post('/reset-password', 'reset')->name('password.update');
    });

Route::controller(AdminAuthController::class)
    ->prefix('admin')
    ->middleware('guest:admin')
    ->group(function () {
        Route::get('/forgot-password', 'showFormForgotPassword')->name('admin.password.request');
        Route::post('/forgot-password', 'sendResetLinkEmail')->name('admin.password.email');
        Route::get('/reset-password/{token}', 'showFormNewPassword')->name('admin.password.reset');
        Route::post('/reset-password', 'updatePassword')->name('admin.password.update');
    });

Route::name('auth.')->group(function () {
    Route::name('client.')
        ->controller(ClientAuthController::class)
        ->group(function () {
            Route::middleware('guest')->group(function () {
                Route::get('/login', 'showFormLogin')->name('showFormLogin');
                Route::post('/login', 'handleLogin')->name('handleLogin');
                Route::get('/register', 'showFormRegister')->name('showFormRegister');
                Route::post('/register', 'handleRegister')->name('handleRegister');
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
                Route::post('/login', 'handleLogin')->name('handleLogin');
            });

            Route::post('/logout', 'logout')->middleware('auth:admin')->name('logout');
        });
});
