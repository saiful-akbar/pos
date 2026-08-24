<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\Module\UseCases\GetModulesByRoleId;
use App\Domain\Module\Repositories\ModuleRepositoryInterface;
use App\Repositories\ModuleRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Repository bindings
     */
    private array $repositories = [
        ModuleRepositoryInterface::class => [
            GetModulesByRoleId::class => ModuleRepository::class,
        ],
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        foreach ($this->repositories as $interface => $services) {
            foreach ($services as $service => $repository) {
                $this->app->when($service)->needs($interface)->give($repository);
            }
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void {}
}
