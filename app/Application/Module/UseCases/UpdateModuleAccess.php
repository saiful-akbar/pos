<?php

declare(strict_types=1);

namespace App\Application\Module\UseCases;

use App\Domain\Module\Repositories\ModuleAccessRepositoryInterface;
use App\Domain\Module\Repositories\ModuleRepositoryInterface;

class UpdateModuleAccess
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private ModuleRepositoryInterface $moduleRepository,
        private ModuleAccessRepositoryInterface $accessRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(string $roleId, array $modules): void
    {
        // Ambil akses yang dimiliki saat ini.
        $currentModules = $this->moduleRepository->getByRoleId(
            roleId: $roleId,
        );

        foreach ($modules as $module) {
            foreach ($currentModules as $currentModule) {
            }
        }
    }
}
