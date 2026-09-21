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
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\WardController;
use Illuminate\Support\Facades\Route;

Route::prefix('/admin')
    ->middleware(['auth:admin', 'admin.active'])
    ->name('admin.')
    ->group(function () {
        Route::prefix('profile')->name('profile.')->controller(ProfileController::class)->group(function () {
            Route::get('/', 'edit')->name('edit');
            Route::patch('/', 'update')->name('update');
            Route::patch('/password', 'updatePassword')->name('password.update');
        });

        Route::prefix('notifications')
            ->name('notifications.')
            ->controller(NotificationController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/read-all', 'readAll')->name('read-all');
                Route::delete('/read', 'destroyRead')->name('destroy-read');
                Route::post('/{id}/read', 'read')->name('read');
            });

        Route::middleware('can:dashboard.view')->group(function () {
            Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');

            Route::prefix('administrators')
                ->name('administrators.')
                ->middleware('can:administrators.manage')
                ->controller(AdministratorController::class)
                ->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/create', 'create')->name('create');
                    Route::post('/', 'store')->name('store');
                    Route::get('/{id}/edit', 'edit')->name('edit');
                    Route::patch('/{id}', 'update')->name('update');
                    Route::delete('/{id}', 'destroy')->name('destroy');
                });

            Route::prefix('roles')
                ->name('roles.')
                ->middleware('can:roles.manage')
                ->controller(RoleController::class)
                ->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::put('/', 'update')->name('update');
                    Route::post('/reset', 'reset')->name('reset');
                    Route::get('/activity', 'activity')->name('activity');
                });

            Route::prefix('products')
                ->name('products.')
                ->controller(ProductController::class)
                ->group(function () {
                    Route::get('/', 'index')->middleware('can:products.view')->name('index');
                    Route::get('/import', 'importForm')->middleware('can:products.import')->name('import');
                    Route::post('/import/preview', 'importPreview')->middleware('can:products.import')->name('import.preview');
                    Route::post('/import', 'import')->middleware('can:products.import')->name('import.store');
                    Route::get('/import/template', 'importTemplate')->middleware('can:products.import')->name('import.template');
                    Route::get('/trash', 'trash')->middleware('can:products.delete')->name('trash');
                    Route::get('/create', 'create')->middleware('can:products.create')->name('create');
                    Route::get('/confirm', 'confirmDetail')->middleware('admin.can_write:products.create,products.update,product_data')->name('confirm-detail');
                    Route::post('/confirm/{id?}', 'confirm')->middleware('admin.can_write:products.create,products.update,product_data')->name('confirm');
                    Route::post('/save', 'save')->middleware('admin.can_write:products.create,products.update,product_data')->name('save');
                    Route::post('/restore/{id}', 'restore')->middleware('can:products.delete')->name('restore');
                    Route::delete('/force/{id}', 'forceDestroy')->middleware('can:products.force_delete')->name('force-destroy');
                    Route::get('/{id}/edit', 'edit')->middleware('can:products.update')->name('edit');
                    Route::get('/{id}', 'show')->middleware('can:products.view')->name('show');
                    Route::delete('/{id}', 'destroy')->middleware('can:products.delete')->name('destroy');
                });

            foreach ([
                'users' => UserController::class,
                'branches' => BranchController::class,
                'categories' => CategoryController::class,
                'coupons' => CouponController::class,
                'attributes' => AttributeController::class,
                'tags' => TagController::class,
            ] as $slug => $controller) {
                $view = 'can:' . $slug . '.view';
                $manage = 'can:' . $slug . '.manage';
                $forceDelete = 'can:' . $slug . '.force_delete';

                Route::prefix($slug)
                    ->name($slug . '.')
                    ->controller($controller)
                    ->group(function () use ($view, $manage, $forceDelete) {
                        Route::get('/', 'index')->middleware($view)->name('index');
                        Route::get('/trash', 'trash')->middleware($manage)->name('trash');
                        Route::get('/create', 'create')->middleware($manage)->name('create');
                        Route::get('/confirm', 'confirmDetail')->middleware($manage)->name('confirm-detail');
                        Route::post('/confirm/{id?}', 'confirm')->middleware($manage)->name('confirm');
                        Route::post('/save', 'save')->middleware($manage)->name('save');
                        Route::post('/restore/{id}', 'restore')->middleware($manage)->name('restore');
                        Route::delete('/force/{id}', 'forceDestroy')->middleware($forceDelete)->name('force-destroy');
                        Route::get('/{id}/edit', 'edit')->middleware($manage)->name('edit');
                        Route::get('/{id}', 'show')->middleware($view)->name('show');
                        Route::delete('/{id}', 'destroy')->middleware($manage)->name('destroy');
                    });
            }

            Route::prefix('reviews')->name('reviews.')->controller(ReviewController::class)->group(function () {
                Route::get('/', 'index')->middleware('can:reviews.view')->name('index');
                Route::post('/{id}/approve', 'approve')->middleware('can:reviews.moderate')->name('approve');
                Route::post('/{id}/reject', 'reject')->middleware('can:reviews.moderate')->name('reject');
                Route::delete('/{id}', 'destroy')->middleware('can:reviews.delete')->name('destroy');
            });

            Route::prefix('questions')
                ->name('questions.')
                ->controller(QuestionController::class)
                ->group(function () {
                    Route::get('/', 'index')->middleware('can:questions.view')->name('index');
                    Route::post('/{id}/answer', 'answer')->middleware('can:questions.answer')->name('answer');
                    Route::post('/{id}/toggle', 'toggle')->middleware('can:questions.answer')->name('toggle');
                    Route::delete('/{id}', 'destroy')->middleware('can:questions.delete')->name('destroy');
                });

            Route::prefix('orders')->name('orders.')->controller(OrderController::class)->group(function () {
                Route::get('/', 'index')->middleware('can:orders.view')->name('index');
                Route::get('/{id}', 'show')->middleware('can:orders.view')->name('show');
                Route::patch('/{id}/status', 'updateStatus')->middleware('can:orders.update_status')->name('update-status');
                Route::post('/{id}/mark-paid', 'markPaid')->middleware('can:orders.mark_paid')->name('mark-paid');
            });

            Route::prefix('provinces')->name('provinces.')->middleware('can:locations.view')->controller(ProvinceController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{id}', 'show')->name('show');
            });

            Route::prefix('wards')->name('wards.')->middleware('can:locations.view')->controller(WardController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{id}', 'show')->name('show');
            });
        });
    });
