<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DailyStockOpnameController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MonthlyStockOpnameController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
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
            Route::get('/sales', 'monthlySaleSummary')->name('.sales');
            Route::get('/sales/products/best', 'getBestSellingProduct')->name('.products.best');
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

    Route::name('masters')->prefix('masters')->group(function (): void {
        Route::controller(RoleController::class)
            ->middleware('throttle:global')
            ->name('.roles')
            ->prefix('roles')
            ->group(function (): void {
                $modules = 'module:masters.roles';

                Route::get('/', 'index')->middleware("$modules,read");
                Route::get('/create', 'create')->name('.create')->middleware("$modules,create");
                Route::post('/', 'store')->name('.store')->middleware("$modules,create");
                Route::get('/{id}/edit', 'edit')->name('.edit')->middleware("$modules,update");
                Route::put('/{id}', 'update')->name('.update')->middleware("$modules,update");
            });

        Route::controller(UserController::class)
            ->middleware('throttle:global')
            ->name('.users')
            ->prefix('users')
            ->group(function (): void {
                $modules = 'module:masters.users';

                Route::get('/', 'index')->middleware("$modules,read");
                Route::get('/create', 'create')->name('.create')->middleware("$modules,create");
                Route::post('/', 'store')->name('.store')->middleware("$modules,create");
                Route::get('/{id}/edit', 'edit')->name('.edit')->middleware("$modules,update");
                Route::put('/{id}', 'update')->name('.update')->middleware("$modules,update");
                Route::delete('/{id}', 'remove')->name('.remove')->middleware("$modules,remove");
            });

        Route::controller(SupplierController::class)
            ->middleware('throttle:global')
            ->name('.suppliers')
            ->prefix('suppliers')
            ->group(function (): void {
                $module = 'module:masters.suppliers';

                Route::get('/', 'index')->middleware("$module,read");
                Route::get('/export', 'exportExcel')->name('.export')->middleware("$module,read");
                Route::post('/', 'store')->name('.store')->middleware("$module,create");
                Route::put('/{id}', 'update')->name('.update')->middleware("$module,update");
                Route::delete('/{id}', 'remove')->name('.remove')->middleware("$module,remove");
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
                Route::delete('/{id}', 'remove')->name('.remove')->middleware('module:master.categories,remove');
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
                Route::delete('/{id}', 'remove')->name('.remove')->middleware('module:master.units,remove');
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
                Route::get('/{id}/edit', 'edit')->name('.edit')->middleware('module:master.products,update');
                Route::put('/{id}', 'update')->name('.update')->middleware('module:master.products,update');
                Route::delete('/{id}', 'remove')->name('.remove')->middleware('module:master.products,remove');
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
                Route::delete('/{id}', 'remove')->name('.remove')->middleware('module:master.customers,remove');
            });
    });

    Route::name('transactions')->prefix('/transactions')->group(function (): void {
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
                Route::get('/{id}', 'detail')->name('.detail')->middleware('module:transactions.purchases,read');
                Route::delete('/{id}', 'remove')->name('.remove')->middleware('module:transactions.purchases,remove');

                Route::name('.payments')
                    ->prefix('/{id}/payments')
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
                $module = 'module:transactions.sales';

                Route::get('/', 'index')->middleware("$module,read");
                Route::post('/', 'storeSale')->name('.store')->middleware("$module,create");

                Route::name('.carts')
                    ->prefix('/carts')
                    ->group(function () use ($module): void {
                        Route::get('/', 'carts')->middleware("$module,create");
                        Route::post('/', 'storeCart')->name('.store')->middleware("$module,create");
                        Route::get('/{cartId}', 'showCart')->name('.show')->middleware("$module,create");
                        Route::delete('/{cartId}', 'deleteCart')->name('.delete')->middleware("$module,create");
                        Route::get('/{cartId}/details', 'getCartDetailsTable')->name('.details')->middleware("$module,create");
                        Route::post('/{cartId}/details', 'storeCartDetail')->name('.details.store')->middleware("$module,create");
                        Route::delete('/{cartId}/details/{cartDetailId}', 'deleteCartDetail')->name('.details.delete')->middleware("$module,create");
                        Route::patch('/{cartId}/details/{cartDetailId}', 'updateCartDetail')->name('.details.update')->middleware("$module,create");
                    });

                Route::name('.return')
                    ->prefix('/return')
                    ->group(function () use ($module): void {
                        Route::get('/', 'return')->middleware("$module,read");
                        Route::post('/', 'storeReturn')->name('.store')->middleware("$module,update");
                        Route::get('/carts', 'returnCart')->name('.carts')->middleware("$module,update");
                        Route::post('/carts', 'storeReturnCart')->name('.carts.store')->middleware("$module,update");
                        Route::delete('/casrts', 'deleteReturnCart')->name('.carts.delete')->middleware("$module,update");
                        Route::get('/carts/details', 'getReturnCartDetails')->name('.carts.details')->middleware("$module,update");
                        Route::post('/carts/details', 'storeReturnCartDetail')->name('.carts.details.store')->middleware("$module,update");
                        Route::patch('/carts/details/{detailId}', 'updateReturnCartDetail')->name('.carts.details.update')->middleware("$module,update");
                        Route::delete('/carts/details/{detailId}', 'deleteReturnCartDetail')->name('.carts.details.delete')->middleware("$module,update");
                        Route::delete('/{returnId}', 'removeSaleReturn')->name('.remove')->middleware("$module,remove");
                    });

                Route::delete('/{saleId}', 'removeSale')->name('.remove')->middleware("$module,remove");
                Route::get('/{saleId}/receipt', 'printReceipt')->name('.receipt')->middleware("$module,read");
                Route::get('/{saleId}/details', 'getDetails')->name('.details')->middleware("$module,read");
            });
    });

    Route::name('stockOpnames')->prefix('/stock-opnames')->group(function (): void {
        Route::controller(DailyStockOpnameController::class)
            ->name('.daily')
            ->prefix('/daily')
            ->group(function (): void {
                $module = 'module:stockOpnames.daily';

                Route::get('/', 'index')->middleware("$module,read");
                Route::get('/export', 'exportTemplateInput')->name('.export')->middleware("$module,create");
                Route::post('/import', 'importFromExcel')->name('.import')->middleware("$module,create");
                Route::get('/{id}', 'show')->name('.show')->middleware("$module,read");
                Route::delete('/{id}', 'remove')->name('.remove')->middleware("$module,remove");
                Route::delete('/{id}/delete', 'delete')->name('.delete')->middleware("$module,delete");
                Route::patch('/{id}/active', 'setActiveStock')->name('.active')->middleware("$module,create");
                Route::post('/{id}/monthly', 'storeMonthlyStock')->name('.monthly')->middleware("$module,create");
                Route::get('/{id}/edit', 'edit')->name('.edit')->middleware("$module,update");
                Route::post('/{id}/details', 'storeDetail')->name('.details.store')->middleware("$module,update");
                Route::delete('/{id}/details/{detailId}', 'deleteDetail')->name('.details.delete')->middleware("$module,update");
                Route::patch('/{id}/details/{detailId}', 'updateDetail')->name('.details.update')->middleware("$module,update");
            });

        Route::controller(MonthlyStockOpnameController::class)
            ->name('.monthly')
            ->prefix('/monthly')
            ->group(function (): void {
                $module = 'module:stockOpnames.monthly';

                Route::get('/', 'index')->middleware("$module,read");
                Route::post('/', 'store')->name('.store')->middleware("$module,create");
                Route::patch('/{id}', 'update')->name('.update')->middleware("$module,update");
            });
    });

    Route::controller(ReportController::class)
        ->name('reports')
        ->prefix('/reports')
        ->group(function (): void {
            Route::get('purchases', 'purchaseSummaries')->name('.purchases')->middleware('module:reports.purchases,read');
            Route::get('purchases/details', 'purchaseDetails')->name('.purchases.details')->middleware('module:reports.purchases,read');
            Route::get('sales', 'saleSummaries')->name('.sales')->middleware('module:reports.sales,read');
            Route::get('sales/details', 'saleDetails')->name('.sales.details')->middleware('module:reports.sales,read');
            Route::get('returns', 'returnSummaries')->name('.returns')->middleware('module:reports.returns,read');
            Route::get('returns/details', 'returnDetails')->name('.returns.details')->middleware('module:reports.returns,read');
            Route::get('/stocks', 'monitoringMonthlyStock')->name('.stocks')->middleware('module:reports.stocks,read');
        });
});

Route::redirect('/', '/dashboard');
