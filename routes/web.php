<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::controller(AuthController::class)
        ->middleware('throttle:global')
        ->name('login')
        ->prefix('/login')
        ->group(function (): void {
            Route::get('/', 'login');
            Route::post('/', 'store')->name('.store');
        });
});

Route::middleware('auth')->group(function (): void {
    Route::controller(AuthController::class)
        ->middleware('throttle:global')
        ->name('logout')
        ->prefix('/logout')
        ->group(function (): void {
            Route::delete('/', 'logout');
        });

    Route::controller(DashboardController::class)
        ->middleware('throttle:global')
        ->name('dashboard')
        ->prefix('/dashboard')
        ->group(function (): void {
            Route::get('/', 'index');
        });

    Route::controller(SettingController::class)
        ->middleware('throttle:global')
        ->name('setting')
        ->prefix('/setting')
        ->group(function (): void {
            Route::get('/', 'index');
        });

    Route::name('master')->prefix('/master')->group(function (): void {
        Route::controller(RoleController::class)
            ->middleware('throttle:global')
            ->name('.roles')
            ->prefix('/roles')
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
            ->prefix('/users')
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
            ->prefix('/categories')
            ->group(function (): void {
                Route::get('/', 'index')->middleware('module:master.categories,read');
                Route::get('/create', 'create')->name('.create')->middleware('module:master.categories,create');
                Route::post('/', 'store')->name('.store')->middleware('module:master.categories,create');
                Route::get('/{id}/edit', 'edit')->name('.edit')->middleware('module:master.categories,update');
                Route::put('/{id}', 'update')->name('.update')->middleware('module:master.categories,update');
                Route::get('/export/template', 'templateExport')->name('.export.template')->middleware('module:master.categories,create');
                Route::post('/import', 'importExcel')->name('.import')->middleware('module:master.categories,create');
            });
    });
});

Route::redirect('/', '/dashboard');
