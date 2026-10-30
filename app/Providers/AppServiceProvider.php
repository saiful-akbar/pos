<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
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
    public function boot(): void
    {
        RateLimiter::for('global', function (Request $request) {
            $user = $request->user();
            $ip = $request->ip();

            return Limit::perMinute($user === null ? 100 : 500)->by($user?->id ?? $ip);
        });

        if (str_starts_with(config('app.url'), 'https')) {
            URL::forceRootUrl(config('app.url'));
            URL::forceScheme('https');
        }
    }

    /**
     * Registrasi custom provider
     *
     * @return void
     */
    #[Override]
    public function provides()
    {
        return [
            RepositoryServiceProvider::class,
        ];
    }
}
