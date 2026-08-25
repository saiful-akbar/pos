<?php

declare(strict_types=1);

namespace App\Application\Module\UseCases;

use App\Domain\Module\Entities\ModuleEntity;
use App\Domain\Module\Repositories\ModuleRepositoryInterface;

class GetAllModulesByRoleId
{
    /**
     * Create a new class instance.
     */
    public function __construct(private ModuleRepositoryInterface $repository) {}

    /**
     * Execute method
     *
     * @return ModuleEntity[]
     */
    public function execute(string $roleId): array
    {
        return $this->repository->getAllByRoleId(
            roleId: $roleId,
        );
    }
}
