<?php

declare(strict_types=1);

namespace App\Domain\Module\Repositories;

use App\Application\Module\DTOs\CreateModuleAccessDTO;
use App\Application\Module\DTOs\UpdateModuleAccessDTO;
use App\Domain\Module\Entities\ModuleAccessEntity;

interface ModuleAccessRepositoryInterface
{
    /**
     * Insert data akses module (role_module)
     */
    public function createAccess(CreateModuleAccessDTO $dto): void;

    /**
     * Update data module access (role_module)
     */
    public function updateAccess(UpdateModuleAccessDTO $dto): void;

    /**
     * Update data module access (role_module)
     */
    public function deleteAccess(string $roleId, string $moduleId): void;

    /**
     * Mengambil data module access
     */
    public function findAccessByRoute(string $roleId, string $route): ?ModuleAccessEntity;
}
