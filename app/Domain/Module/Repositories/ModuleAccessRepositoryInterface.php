<?php

declare(strict_types=1);

namespace App\Domain\Module\Repositories;

use App\Application\Module\DTOs\ModuleAccessDTO;

interface ModuleAccessRepositoryInterface
{
    /**
     * Insert data akses module (role_module)
     */
    public function createAccess(ModuleAccessDTO $dto): void;

    /**
     * Update data module access (role_module)
     */
    public function updateAccess(ModuleAccessDTO $dto): void;

    /**
     * Update data module access (role_module)
     */
    public function deleteAccess(string $roleId, string $moduleId): void;
}
