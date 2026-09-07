<?php

use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AdministratorController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\WardController;
use App\Const\AdminConst;
use Illuminate\Support\Facades\Route;

$allAdminRoles = implode(',', AdminConst::allRoleIds());
$managementRoles = implode(',', AdminConst::managementRoleIds());
$superAdminRole = (string) AdminConst::ROLE_SUPER_ADMIN;

Route::prefix('/admin')
    ->middleware('auth:admin')
    ->name('admin.')
    ->group(function () use ($allAdminRoles, $managementRoles, $superAdminRole) {
        Route::get('/', [DashboardController::class, 'dashboard'])
            ->middleware('admin.role:' . $allAdminRoles)
            ->name('dashboard');

        Route::prefix('profile')->name('profile.')->controller(ProfileController::class)->group(function () {
            Route::get('/', 'edit')->name('edit');
            Route::patch('/', 'update')->name('update');
            Route::patch('/password', 'updatePassword')->name('password.update');
        });

        Route::prefix('administrators')
            ->name('administrators.')
            ->middleware('admin.role:' . $superAdminRole)
            ->controller(AdministratorController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}/edit', 'edit')->name('edit');
                Route::patch('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('destroy');
            });

        Route::prefix('products')
            ->name('products.')
            ->middleware('admin.role:' . $allAdminRoles)
            ->controller(ProductController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/import', 'importForm')->name('import');
                Route::post('/import/preview', 'importPreview')->name('import.preview');
            Route::post('/import', 'import')->name('import.store');
                Route::get('/import/template', 'importTemplate')->name('import.template');
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

        foreach ([
            'users' => UserController::class,
            'branches' => BranchController::class,
            'categories' => CategoryController::class,
            'coupons' => CouponController::class,
            'attributes' => AttributeController::class,
            'tags' => TagController::class,
        ] as $slug => $controller) {
            $roles = $managementRoles;
            Route::prefix($slug)
                ->name($slug . '.')
                ->middleware('admin.role:' . $roles)
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

        Route::prefix('reviews')->name('reviews.')->middleware('admin.role:' . $allAdminRoles)->controller(ReviewController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/{id}/approve', 'approve')->name('approve');
            Route::post('/{id}/reject', 'reject')->name('reject');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

        Route::prefix('notifications')
            ->name('notifications.')
            ->middleware('admin.role:' . $allAdminRoles)
            ->controller(NotificationController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/read-all', 'readAll')->name('read-all');
                Route::post('/{id}/read', 'read')->name('read');
            });

        Route::prefix('questions')
            ->name('questions.')
            ->middleware('admin.role:' . $allAdminRoles)
            ->controller(QuestionController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/{id}/answer', 'answer')->name('answer');
                Route::post('/{id}/toggle', 'toggle')->name('toggle');
                Route::delete('/{id}', 'destroy')->name('destroy');
            });

        Route::prefix('orders')->name('orders.')->middleware('admin.role:' . $allAdminRoles)->controller(OrderController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{id}', 'show')->name('show');
            Route::patch('/{id}/status', 'updateStatus')->name('update-status');
            Route::post('/{id}/mark-paid', 'markPaid')->name('mark-paid');
        });

        Route::prefix('provinces')->name('provinces.')->middleware('admin.role:' . $managementRoles)->controller(ProvinceController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{id}', 'show')->name('show');
        });

        Route::prefix('wards')->name('wards.')->middleware('admin.role:' . $managementRoles)->controller(WardController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{id}', 'show')->name('show');
        });
    });
