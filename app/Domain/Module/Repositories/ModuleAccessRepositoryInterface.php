<?php

declare(strict_types=1);

namespace App\Domain\Module\Repositories;

use App\Application\Module\DTOs\CreateModuleAccessDTO;
use App\Application\Module\DTOs\UpdateModuleAccessDTO;

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
}
