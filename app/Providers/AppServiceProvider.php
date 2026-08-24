<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Override;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {}

    /**
     * Registrasi custom provider
     *
     * @return void
     */
    #[Override]
    public function provides()
    {
        return [
            RepositoryServiceProvider::class
        ];
    }
}
