<?php

use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\WardController;
use Illuminate\Support\Facades\Route;

Route::prefix('/admin')
    ->middleware('auth:admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');

        Route::prefix('profile')->name('profile.')->controller(ProfileController::class)->group(function () {
            Route::get('/', 'edit')->name('edit');
            Route::patch('/', 'update')->name('update');
            Route::patch('/password', 'updatePassword')->name('password.update');
        });

        foreach ([
            'users' => UserController::class,
            'branches' => BranchController::class,
            'categories' => CategoryController::class,
            'products' => ProductController::class,
            'coupons' => CouponController::class,
            'attributes' => AttributeController::class,
            'tags' => TagController::class,
        ] as $slug => $controller) {
            Route::prefix($slug)
                ->name($slug . '.')
                ->controller($controller)
                ->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/trash', 'trash')->name('trash');
                    Route::get('/create', 'create')->name('create');
                    Route::get('/confirm', 'confirmDetail')->name('confirm-detail');
                    Route::post('/confirm/{id?}', 'confirm')->name('confirm');
                    Route::post('/save', 'save')->name('save');
                    Route::post('/restore/{id}', 'restore')->name('restore');
                    Route::delete('/force/{id}', 'forceDestroy')->name('force-destroy');
                    Route::get('/{id}/edit', 'edit')->name('edit');
                    Route::get('/{id}', 'show')->name('show');
                    Route::delete('/{id}', 'destroy')->name('destroy');
                });
        }

        Route::prefix('reviews')->name('reviews.')->controller(ReviewController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/{id}/approve', 'approve')->name('approve');
            Route::post('/{id}/reject', 'reject')->name('reject');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

        Route::prefix('orders')->name('orders.')->controller(OrderController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{id}', 'show')->name('show');
            Route::patch('/{id}/status', 'updateStatus')->name('update-status');
            Route::post('/{id}/mark-paid', 'markPaid')->name('mark-paid');
        });

        Route::prefix('provinces')->name('provinces.')->controller(ProvinceController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{id}', 'show')->name('show');
        });

        Route::prefix('wards')->name('wards.')->controller(WardController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{id}', 'show')->name('show');
        });
    });
