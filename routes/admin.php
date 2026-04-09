<?php

// ========================= ADMIN ===========================

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('/admin')
    // ->middleware(['auth:admin', 'adminLogin'])
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');

        Route::prefix('/users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
            Route::post('/confirm/{id?}', [UserController::class, 'confirm'])->name('confirm');
            Route::get('/confirm', [UserController::class, 'confirmDetail'])->name('confirm-detail');
            Route::post('/save', [UserController::class, 'save'])->name('save');
            Route::get('/{id}', [UserController::class, 'show'])->name('show');
            Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
        });
    });
