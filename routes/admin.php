<?php

use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\WardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProvinceController;
use Illuminate\Support\Facades\Route;

Route::prefix('/admin')
    ->middleware('auth:admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');

        Route::prefix('/users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/trash', [UserController::class, 'trash'])->name('trash');

            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');

            Route::post('/confirm/{id?}', [UserController::class, 'confirm'])->name('confirm');
            Route::get('/confirm', [UserController::class, 'confirmDetail'])->name('confirm-detail');

            Route::post('/save', [UserController::class, 'save'])->name('save');
            Route::get('/{id}', [UserController::class, 'show'])->name('show');

            Route::post('/restore/{id}', [UserController::class, 'restore'])->name('restore');

            Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
            Route::delete('/force/{id}', [UserController::class, 'forceDestroy'])->name('force-destroy');
        });

        Route::prefix('provinces')->name('provinces.')->group(function () {
            Route::get('/', [ProvinceController::class, 'index'])->name('index');
            Route::get('/{id}', [ProvinceController::class, 'show'])->name('show');
        });

        Route::prefix('wards')->name('wards.')->group(function () {
            Route::get('/', [WardController::class, 'index'])->name('index');
            Route::get('/{id}', [WardController::class, 'show'])->name('show');
        });

        Route::prefix('/branches')
            ->name('branches.')
            ->group(function () {
                Route::get('/', [BranchController::class, 'index'])->name('index');
                Route::get('/trash', [BranchController::class, 'trash'])->name('trash');
                Route::get('/create', [BranchController::class, 'create'])->name('create');
                Route::get('/{id}/edit', [BranchController::class, 'edit'])->name('edit');

                Route::post('/confirm/{id?}', [BranchController::class, 'confirm'])->name('confirm');
                Route::get('/confirm', [BranchController::class, 'confirmDetail'])->name('confirm-detail');

                Route::post('/save', [BranchController::class, 'save'])->name('save');
                Route::get('/{id}', [BranchController::class, 'show'])->name('show');

                Route::post('/restore/{id}', [BranchController::class, 'restore'])->name('restore');

                Route::delete('/{id}', [BranchController::class, 'destroy'])->name('destroy');
                Route::delete('/force/{id}', [BranchController::class, 'forceDestroy'])->name('force-destroy');
            });
    });
