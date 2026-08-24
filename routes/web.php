<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::controller(AuthController::class)
        ->name('login')
        ->prefix('/login')
        ->group(function (): void {
            Route::get('/', 'login');
            Route::post('/', 'store')->name('.store');
        });
});

Route::middleware('auth')->group(function (): void {
    Route::delete('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::controller(DashboardController::class)
        ->name('dashboard')
        ->prefix('/dashboard')
        ->group(function (): void {
            Route::get('/', 'index');
        });
});

Route::redirect('/', '/dashboard');
