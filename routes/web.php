<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingController;
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
});

Route::redirect('/', '/dashboard');
