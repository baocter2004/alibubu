<?php

// ========================= ADMIN ===========================

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('/admin')
    ->middleware(['auth', 'adminLogin'])
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');
    });
