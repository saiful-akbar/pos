<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CashDashboardController;
use App\Http\Controllers\CashReconciliationController;
use App\Http\Controllers\CashReportController;
use App\Http\Controllers\CashTransactionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DailyStockController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MonthlyStockController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SaleReportController;
use App\Http\Controllers\SaleReturnController;
use App\Http\Controllers\SaleReturnReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StockReportController;
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
            Route::get('/summary', 'summary')->name('.summary');
            Route::get('/sales', 'monthlySaleSummary')->name('.sales');
            Route::get('/products', 'products')->name('.products');
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

        Route::controller(CustomerController::class)
            ->middleware('throttle:global')
            ->name('.customers')
            ->prefix('customers')
            ->group(function (): void {
                $module = 'module:masters.customers';

                Route::get('/', 'index')->middleware("$module,read");
                Route::get('/export', 'exportExcel')->name('.export')->middleware("$module,read");
                Route::post('/', 'store')->name('.store')->middleware("$module,create");
                Route::patch('/{id}', 'update')->name('.update')->middleware("$module,update");
                Route::delete('/{id}', 'remove')->name('.remove')->middleware("$module,remove");
            });

        Route::controller(CategoryController::class)
            ->middleware('throttle:global')
            ->name('.categories')
            ->prefix('categories')
            ->group(function (): void {
                $module = 'module:masters.categories';

                Route::get('/', 'index')->middleware("$module,read");
                Route::get('/export', 'exportExcel')->name('.export')->middleware("$module,read");
                Route::post('/import', 'importExcel')->name('.import')->middleware("$module,create");
                Route::get('/import/template', 'templateImportExcel')->name('.import.template')->middleware("$module,create");
                Route::post('/', 'store')->name('.store')->middleware("$module,create");
                Route::patch('/{id}', 'update')->name('.update')->middleware("$module,update");
                Route::delete('/{id}', 'remove')->name('.remove')->middleware("$module,remove");
            });

        Route::controller(UnitController::class)
            ->middleware('throttle:global')
            ->name('.units')
            ->prefix('units')
            ->group(function (): void {
                $module = 'module:masters.units';

                Route::get('/', 'index')->middleware("$module,read");
                Route::get('/export', 'exportExcel')->name('.export')->middleware("$module,read");
                Route::post('/', 'store')->name('.store')->middleware("$module,create");
                Route::post('/import', 'importExcel')->name('.import')->middleware("$module,create");
                Route::get('/import/template', 'templateImportExcel')->name('.import.template')->middleware("$module,create");
                Route::patch('/{id}', 'update')->name('.update')->middleware("$module,update");
                Route::delete('/{id}', 'remove')->name('.remove')->middleware("$module,remove");
            });

        Route::controller(ProductController::class)
            ->middleware('throttle:global')
            ->name('.products')
            ->prefix('products')
            ->group(function (): void {
                $module = 'module:masters.products';

                Route::get('/', 'index')->middleware("$module,read");
                Route::get('/export', 'exportExcel')->name('.export')->middleware("$module,read");
                Route::get('/create', 'create')->name('.create')->middleware("$module,create");
                Route::post('/', 'store')->name('.store')->middleware("$module,create");
                Route::post('/import', 'importExcel')->name('.import')->middleware("$module,create");
                Route::get('/import/template', 'downloadTemplate')->name('.import.template')->middleware("$module,create");
                Route::patch('/discounts/reset', 'resetDiscounts')->name('.discounts.reset')->middleware("$module,update");
                Route::patch('/discounts/import', 'importUpdateDiscounts')->name('.discounts.import')->middleware("$module,update");
                Route::get('/discounts/import/template', 'templateImportUpdateDiscounts')->name('.discounts.import.template')->middleware("$module,update");
                Route::patch('/prices/import', 'importUpdatePrices')->name('.prices.import')->middleware("$module,update");
                Route::get('/prices/import/template', 'templateImportUpdatePrices')->name('.prices.import.template')->middleware("$module,update");
                Route::get('/{productId}/prices', 'prices')->name('.prices')->middleware("$module,read");
                Route::get('/{productId}/prices/table', 'pricesTable')->name('.prices.table')->middleware("$module,read");
                Route::get('/{productId}/prices/create', 'createPrices')->name('.prices.create')->middleware("$module,create");
                Route::post('/{productId}/prices', 'storePrice')->name('.prices.store')->middleware("$module,create");
                Route::delete('/{productId}/prices/{priceId}', 'deletePrice')->name('.prices.delete')->middleware("$module,create");
                Route::get('/{productId}/edit', 'edit')->name('.edit')->middleware("$module,update");
                Route::patch('/{productId}', 'update')->name('.update')->middleware("$module,update");
                Route::delete('/{productId}', 'remove')->name('.remove')->middleware("$module,remove");
            });
    });

    Route::name('transactions')->prefix('/transactions')->group(function (): void {
        Route::controller(PurchaseController::class)
            ->name('.purchases')
            ->prefix('purchases')
            ->group(function (): void {
                $module = 'module:transactions.purchases';

                Route::get('/', 'index')->middleware("$module,read");
                Route::post('/', 'store')->name('.store')->middleware("$module,create");
                Route::get('/carts', 'carts')->name('.carts')->middleware("$module,create");
                Route::delete('/carts/{cartId}', 'deleteCart')->name('.carts.delete')->middleware("$module,create");
                Route::get('/carts/{cartId}/details', 'cartDetails')->name('.carts.details')->middleware("$module,read");
                Route::get('/carts/{cartId}/details/table', 'cartDetailsTable')->name('.carts.details.table')->middleware("$module,read");
                Route::post('/carts/{cartId}/details', 'storeCartDetail')->name('.carts.details.store')->middleware("$module,create");
                Route::delete('/carts/{cartId}/details/{cartDetailId}', 'deleteCartDetail')->name('.carts.details.delete')->middleware("$module,create");
                Route::patch('/carts/{cartId}/details/{cartDetailId}', 'updateCartDetail')->name('.carts.details.update')->middleware("$module,create");
                Route::get('/{purchaseId}', 'show')->name('.show')->middleware("$module,read");
                Route::get('/{purchaseId}/details', 'details')->name('.details')->middleware("$module,read");
                Route::get('/{purchaseId}/payments', 'payments')->name('.payments')->middleware("$module,read");
                Route::post('/{purchaseId}/payments', 'storePayment')->name('.payments.store')->middleware("$module,create");
                Route::delete('/{purchaseId}/payments/{paymentId}', 'deletePayment')->name('.payments.delete')->middleware("$module,delete");
            });

        Route::controller(SaleController::class)
            ->name('.sales')
            ->prefix('sales')
            ->group(function (): void {
                $module = 'module:transactions.sales';

                Route::get('/', 'index')->middleware("$module,read");
                Route::post('/', 'store')->name('.store')->middleware("$module,create");
                Route::get('/{saleId}/receipt', 'receipt')->name('.receipt')->middleware("$module,read");
                Route::get('/{saleId}/pdf', 'pdf')->name('.pdf')->middleware("$module,read");
                Route::get('/{saleId}/details', 'details')->name('.details')->middleware("$module,read");
                Route::get('/carts', 'carts')->name('.carts')->middleware("$module,create");
                Route::patch('/carts/{cartId}', 'updateCart')->name('.carts.update')->middleware("$module,create,update");
                Route::delete('/carts/{cartId}', 'deleteCart')->name('.carts.delete')->middleware("$module,create,delete");
                Route::get('/carts/{cartId}/details', 'cartDetails')->name('.carts.details')->middleware("$module,read,create");
                Route::post('/carts/{cartId}/details', 'storeCartDetail')->name('.carts.details.store')->middleware("$module,create");
                Route::patch('/carts/{cartId}/details/{cartDetailId}/quantity', 'updateCartDetailByQuantity')->name('.carts.details.update.quantity')->middleware("$module,create,update");
                Route::patch('/carts/{cartId}/details/{cartDetailId}/price', 'updateCartDetailByPrice')->name('.carts.details.update.price')->middleware("$module,create,update");
                Route::delete('/carts/{cartId}/details/{cartDetailId}', 'deleteCartDetail')->name('.carts.details.delete')->middleware("$module,create,delete");
            });

        Route::controller(SaleReturnController::class)
            ->name('.returns')
            ->prefix('returns')
            ->group(function (): void {
                $module = 'module:transactions.returns';

                Route::get('/', 'index')->middleware("$module,read");
                Route::get('/carts', 'carts')->name('.carts')->middleware("$module,create");
                Route::post('/carts', 'storeCart')->name('.carts.store')->middleware("$module,create");
                Route::post('/carts/{cartId}/complete', 'store')->name('.carts.complete')->middleware("$module,create");
                Route::delete('/carts/{cartId}', 'deleteCart')->name('.carts.delete')->middleware("$module,create,delete");
                Route::get('/carts/{cartId}/info', 'cartInfo')->name('.carts.info')->middleware("$module,read,create");
                Route::get('/carts/{cartId}/details', 'cartDetails')->name('.carts.details')->middleware("$module,read,create");
                Route::post('/carts/{cartId}/details', 'storeCartDetail')->name('.carts.details.store')->middleware("$module,create");
                Route::patch('/carts/{cartId}/details/{cartDetailId}/quantity', 'updateCartDetailQuantity')->name('.carts.details.update.quantity')->middleware("$module,create,update");
                Route::delete('/carts/{cartId}/details/{cartDetailId}', 'deleteCartDetail')->name('.carts.details.delete')->middleware("$module,create,delete");
            });
    });

    Route::name('cash')->prefix('cash')->group(function (): void {
        Route::controller(CashDashboardController::class)
            ->name('.dashboard')
            ->prefix('dashboard')
            ->group(function (): void {
                $module = 'module:cash.dashboard';

                Route::get('/', 'index')->middleware("$module,read");
                Route::get('/summary', 'summary')->name('.summary')->middleware("$module,read");
            });

        Route::controller(CashTransactionController::class)
            ->name('.transactions')
            ->prefix('transactions')
            ->group(function (): void {
                $module = 'module:cash.transactions';

                Route::get('/', 'index')->middleware("$module,read");
                Route::get('/totals', 'totals')->name('.totals')->middleware("$module,read");
                Route::post('/', 'store')->name('.store')->middleware("$module,create");
                Route::get('/{transactionId}', 'show')->name('.show')->middleware("$module,read");
                Route::patch('/{transactionId}', 'update')->name('.update')->middleware("$module,update");
                Route::delete('/{transactionId}', 'destroy')->name('.destroy')->middleware("$module,remove");
            });

        Route::controller(CashReconciliationController::class)
            ->name('.reconciliation')
            ->prefix('reconciliation')
            ->group(function (): void {
                $module = 'module:cash.reconciliation';

                Route::get('/', 'index')->middleware("$module,read");
                Route::get('/data', 'data')->name('.data')->middleware("$module,read");
                Route::get('/history', 'history')->name('.history')->middleware("$module,read");
                Route::post('/', 'store')->name('.store')->middleware("$module,create");
                Route::patch('/', 'update')->name('.update')->middleware("$module,update");
            });
    });

    Route::name('reports')->prefix('reports')->group(function (): void {
        Route::controller(PurchaseReportController::class)
            ->name('.purchases')
            ->prefix('purchases')
            ->group(function (): void {
                $module = 'module:reports.purchases';

                Route::get('/', 'index')->middleware("$module,read");
                Route::get('/summary', 'summary')->name('.summary')->middleware("$module,read");
                Route::get('/export/excel', 'exportExcel')->name('.export.excel')->middleware("$module,read");
                Route::get('/export/pdf', 'exportPdf')->name('.export.pdf')->middleware("$module,read");
                Route::get('/{purchaseId}/details', 'transactionDetails')->name('.transactions.details')->middleware("$module,read");
                Route::get('/{purchaseId}/payments', 'transactionPayments')->name('.transactions.payments')->middleware("$module,read");
                Route::get('/{purchaseId}/export/excel', 'exportTransactionExcel')->name('.transactions.export.excel')->middleware("$module,read");
                Route::get('/{purchaseId}/export/pdf', 'exportTransactionPdf')->name('.transactions.export.pdf')->middleware("$module,read");
            });

        Route::controller(SaleReportController::class)
            ->name('.sales')
            ->prefix('sales')
            ->group(function (): void {
                $module = 'module:reports.sales';

                Route::get('/', 'index')->middleware("$module,read");
                Route::get('/summary', 'summary')->name('.summary')->middleware("$module,read");
                Route::get('/export/excel', 'exportExcel')->name('.export.excel')->middleware("$module,read");
                Route::get('/export/pdf', 'exportPdf')->name('.export.pdf')->middleware("$module,read");
                Route::get('/{saleId}/details', 'transactionDetails')->name('.transactions.details')->middleware("$module,read");
                Route::get('/{saleId}/export/pdf', 'exportTransactionPdf')->name('.transactions.export.pdf')->middleware("$module,read");
            });

        Route::controller(SaleReturnReportController::class)
            ->name('.returns')
            ->prefix('returns')
            ->group(function (): void {
                $module = 'module:reports.returns';

                Route::get('/', 'index')->middleware("$module,read");
                Route::get('/summary', 'summary')->name('.summary')->middleware("$module,read");
                Route::get('/analysis', 'analysis')->name('.analysis')->middleware("$module,read");
                Route::get('/export/excel', 'exportExcel')->name('.export.excel')->middleware("$module,read");
                Route::get('/export/pdf', 'exportPdf')->name('.export.pdf')->middleware("$module,read");
                Route::get('/{saleReturnId}/details', 'details')->name('.details')->middleware("$module,read");
            });

        Route::controller(CashReportController::class)
            ->name('.cash')
            ->prefix('cash')
            ->group(function (): void {
                $module = 'module:reports.cash';

                Route::get('/', 'index')->middleware("$module,read");
                Route::get('/totals', 'totals')->name('.totals')->middleware("$module,read");
                Route::get('/monthly', 'monthly')->name('.monthly')->middleware("$module,read");
                Route::get('/export/excel', 'exportExcel')->name('.export.excel')->middleware("$module,read");
                Route::get('/export/pdf', 'exportPdf')->name('.export.pdf')->middleware("$module,read");
            });

        Route::controller(StockReportController::class)
            ->name('.stocks')
            ->prefix('stocks')
            ->group(function (): void {
                $module = 'module:reports.stocks';

                Route::get('/', 'index')->middleware("$module,read");
                Route::get('/summary', 'summary')->name('.summary')->middleware("$module,read");
                Route::get('/export/excel', 'exportExcel')->name('.export.excel')->middleware("$module,read");
                Route::get('/export/pdf', 'exportPdf')->name('.export.pdf')->middleware("$module,read");
                Route::get('/{productId}/mutations', 'mutations')->name('.mutations')->middleware("$module,read");
            });
    });

    Route::name('stocks')->prefix('stocks')->group(function (): void {
        Route::controller(DailyStockController::class)
            ->name('.daily')
            ->prefix('daily')
            ->group(function (): void {
                $module = 'module:stocks.daily';

                Route::get('/', 'index')->middleware("$module,read");
                Route::get('/import/template', 'downloadTemplateImport')->name('.import.template')->middleware("$module,create");
                Route::post('/import', 'importExcel')->name('.import')->middleware("$module,create");
                Route::get('/{dailyStockId}/details', 'details')->name('.details')->middleware("$module,read");
                Route::get('/{dailyStockId}/details/export', 'exportDetails')->name('.details.export')->middleware("$module,read");
                Route::delete('/{dailyStockId}', 'destroy')->name('.destroy')->middleware("$module,remove");
                Route::post('/{dailyStockId}/import-update', 'importUpdate')->name('.import-update')->middleware("$module,update");
                Route::post('/{dailyStockId}/monthly', 'storeMonthly')->name('.monthly.store')->middleware("$module,update");
                Route::patch('/{dailyStockId}/activate', 'activate')->name('.activate')->middleware("$module,update");
            });

        Route::controller(MonthlyStockController::class)
            ->name('.monthly')
            ->prefix('monthly')
            ->group(function (): void {
                $module = 'module:stocks.monthly';

                Route::get('/', 'index')->middleware("$module,read");
                Route::post('/', 'store')->name('.store')->middleware("$module,create");
                Route::get('/{monthlyStockId}/details', 'details')->name('.details')->middleware("$module,read");
                Route::get('/{monthlyStockId}/details/export', 'exportDetails')->name('.details.export')->middleware("$module,read");
                Route::patch('/{monthlyStockId}/activate', 'activate')->name('.activate')->middleware("$module,update");
                Route::delete('/{monthlyStockId}', 'destroy')->name('.destroy')->middleware("$module,remove");
            });
    });
});

Route::redirect('/', '/dashboard');
