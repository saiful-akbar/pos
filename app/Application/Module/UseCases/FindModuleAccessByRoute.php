<?php

declare(strict_types=1);

namespace App\Application\Module\UseCases;

use App\Domain\Module\Entities\ModuleAccessEntity;
use App\Domain\Module\Repositories\ModuleAccessRepositoryInterface;

class FindModuleAccessByRoute
{
    /**
     * Create a new class instance.
     */
    public function __construct(private ModuleAccessRepositoryInterface $repository) {}

    /**
     * Execute method.
     */
    public function execute(string $roleId, string $route): ?ModuleAccessEntity
    {
        return $this->repository->findAccessByRoute(
            roleId: $roleId,
            route: $route,
        );
    }
}
