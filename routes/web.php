<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::controller(AuthController::class)
        ->middleware('throttle:global')
        ->name('login')
        ->prefix('login')
        ->group(function (): void {
            Route::get('/', 'login');
            Route::post('/', 'store')->name('.store');
        });
});

Route::middleware('auth')->group(function (): void {
    Route::controller(AuthController::class)
        ->middleware('throttle:global')
        ->name('logout')
        ->prefix('logout')
        ->group(function (): void {
            Route::delete('/', 'logout');
        });

    Route::controller(DashboardController::class)
        ->middleware('throttle:global')
        ->name('dashboard')
        ->prefix('dashboard')
        ->group(function (): void {
            Route::get('/', 'index');
        });

    Route::name('settings')->prefix('settings')->group(function (): void {
        Route::controller(SettingController::class)
            ->middleware('throttle:global')
            ->name('.applications')
            ->prefix('applications')
            ->group(function (): void {
                Route::get('/', 'index')->middleware('module:settings.applications,read');
                Route::get('/edit', 'edit')->name('.edit')->middleware('module:settings.applications,update');
                Route::put('/update', 'update')->name('.update')->middleware('module:settings.applications,update');
            });
    });

    Route::name('master')->prefix('master')->group(function (): void {
        Route::controller(RoleController::class)
            ->middleware('throttle:global')
            ->name('.roles')
            ->prefix('roles')
            ->group(function (): void {
                Route::get('/', 'index')->middleware('module:master.roles,read');
                Route::get('/create', 'create')->middleware('module:master.roles,create')->name('.create');
                Route::post('/', 'store')->middleware('module:master.roles,create')->name('.store');
                Route::get('/{id}/edit', 'edit')->middleware('module:master.roles,update')->name('.edit');
                Route::put('/{id}', 'update')->middleware('module:master.roles,update')->name('.update');
            });

        Route::controller(UserController::class)
            ->middleware('throttle:global')
            ->name('.users')
            ->prefix('users')
            ->group(function (): void {
                Route::get('/', 'index')->middleware('module:master.users,read');
                Route::get('/create', 'create')->name('.create')->middleware('module:master.users,create');
                Route::post('/', 'store')->name('.store')->middleware('module:master.users,create');
                Route::get('/{id}/edit', 'edit')->name('.edit')->middleware('module:master.users,update');
                Route::put('/{id}', 'update')->name('.update')->middleware('module:master.users,update');
            });

        Route::controller(CategoryController::class)
            ->middleware('throttle:global')
            ->name('.categories')
            ->prefix('categories')
            ->group(function (): void {
                Route::get('/', 'index')->middleware('module:master.categories,read');
                Route::post('/import', 'importExcel')->name('.import')->middleware('module:master.categories,create');
                Route::get('/export/template', 'templateExport')->name('.export.template')->middleware('module:master.categories,create');
                Route::get('/create', 'create')->name('.create')->middleware('module:master.categories,create');
                Route::post('/', 'store')->name('.store')->middleware('module:master.categories,create');
                Route::get('/export', 'exportExcel')->name('.export')->middleware('module:master.categories,read');
                Route::get('/{id}/edit', 'edit')->name('.edit')->middleware('module:master.categories,update');
                Route::put('/{id}', 'update')->name('.update')->middleware('module:master.categories,update');
            });

        Route::controller(UnitController::class)
            ->middleware('throttle:global')
            ->name('.units')
            ->prefix('units')
            ->group(function (): void {
                Route::get('/', 'index')->middleware('module:master.units,read');
                Route::get('/export/template', 'templateExport')->name('.export.template')->middleware('module:master.units,create');
                Route::post('/import', 'importExcel')->name('.import')->middleware('module:master.units,create');
                Route::get('/create', 'create')->name('.create')->middleware('module:master.units,create');
                Route::post('/', 'store')->name('.store')->middleware('module:master.units,create');
                Route::get('/export', 'exportExcel')->name('.export')->middleware('module:master.units,read');
                Route::get('/{id}/edit', 'edit')->name('.edit')->middleware('module:master.units,update');
                Route::put('/{id}', 'update')->name('.update')->middleware('module:master.units,update');
            });

        Route::controller(ProductController::class)
            ->middleware('throttle:global')
            ->name('.products')
            ->prefix('products')
            ->group(function (): void {
                Route::get('/', 'index')->middleware('module:master.products,read');
                Route::post('/', 'store')->name('.store')->middleware('module:master.products,create');
                Route::get('/active', 'getActiveTable')->name('.active');
                Route::get('/create', 'create')->name('.create')->middleware('module:master.products,create');
                Route::get('/export', 'exportExcel')->name('.export')->middleware('module:master.products,read');
                Route::get('/export/template', 'templateImport')->name('.export.template')->middleware('module:master.products,create');
                Route::get('/export/template/dicount', 'templateImportUpdateDiscount')->name('.export.template.discount')->middleware('module:master.products,update');
                Route::get('/export/template/selling-price', 'templateImportUpdateSellingPrice')->name('.export.template.sellingPrice')->middleware('module:master.products,update');
                Route::post('/import', 'importExcel')->name('.import')->middleware('module:master.products,create');
                Route::patch('/import/discount', 'importUpdateDiscount')->name('.import.discount')->middleware('module:master.products,update');
                Route::patch('/import/selling-price', 'importUpdateSellingPrice')->name('.import.sellingPrice')->middleware('module:master.products,update');
                Route::patch('/reset-discount', 'resetDiscount')->name('.resetDiscount')->middleware('module:master.products,update');
                Route::put('/{id}', 'update')->name('.update')->middleware('module:master.products,update');
                Route::get('/{id}/edit', 'edit')->name('.edit')->middleware('module:master.products,update');
            });

        Route::controller(CustomerController::class)
            ->middleware('throttle:global')
            ->name('.customers')
            ->prefix('customers')
            ->group(function (): void {
                Route::get('/', 'index')->middleware('module:master.customers,read');
                Route::post('/', 'store')->name('.store')->middleware('module:master.customers,create');
                Route::get('/create', 'create')->name('.create')->middleware('module:master.customers,create');
                Route::get('/export', 'exportExcel')->name('.export')->middleware('module:master.customers,read');
                Route::get('/{id}/edit', 'edit')->name('.edit')->middleware('module:master.customers,update');
                Route::put('/{id}', 'update')->name('.update')->middleware('module:master.customers,update');
            });

        Route::controller(SupplierController::class)
            ->middleware('throttle:global')
            ->name('.suppliers')
            ->prefix('suppliers')
            ->group(function (): void {
                Route::get('/', 'index')->middleware('module:master.suppliers,read');
                Route::post('/', 'store')->name('.store')->middleware('module:master.suppliers,create');
                Route::get('/create', 'create')->name('.create')->middleware('module:master.suppliers,create');
                Route::get('/export', 'exportExcel')->name('.export')->middleware('module:master.suppliers,read');
                Route::get('/{id}/edit', 'edit')->name('.edit')->middleware('module:master.suppliers,update');
                Route::put('/{id}', 'update')->name('.update')->middleware('module:master.suppliers,update');
            });
    });

    Route::name('transactions')->prefix('transactions')->group(function (): void {
        Route::controller(PurchaseController::class)
            ->name('.purchases')
            ->prefix('purchases')
            ->group(function (): void {
                Route::get('/', 'index')->middleware('module:transactions.purchases,read');
                Route::post('/', 'save')->name('.save')->middleware('module:transactions.purchases,create');
                Route::get('/cart', 'createCart')->name('.cart')->middleware('module:transactions.purchases,create');
                Route::post('/cart', 'addCartDetail')->name('.cart.store')->middleware('module:transactions.purchases,create');
                Route::patch('/cart/{cartDetailId}', 'updateCartDetail')->name('.cart.update')->middleware('module:transactions.purchases,create');
                Route::delete('/cart/cancel', 'cancelCart')->name('.cart.cancel')->middleware('module:transactions.purchases,create');
                Route::delete('/cart/{cartDetailId}', 'deleteCartDetail')->name('.cart.delete')->middleware('module:transactions.purchases,create');
                Route::get('/{purchaseId}', 'detail')->name('.detail')->middleware('module:transactions.purchases,read');

                Route::name('.payments')
                    ->prefix('/{purchaseId}/payments')
                    ->middleware('module:transactions.purchases,create')
                    ->group(function (): void {
                        Route::get('/', 'payment');
                        Route::post('/', 'createPayment')->name('.create');
                        Route::delete('/{paymentId}', 'deletePayment')->name('.delete');
                    });
            });

        Route::controller(SaleController::class)
            ->name('.sales')
            ->prefix('sales')
            ->group(function (): void {
                Route::get('/', 'index');
            });
    });
});

Route::redirect('/', '/dashboard');
