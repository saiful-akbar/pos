<?php

declare(strict_types=1);

namespace App\Domain\Module\Repositories;

use App\Domain\Module\Entities\ModuleEntity;

interface ModuleRepositoryInterface
{
    /**
     * Mengambil daftar module berdasarkan id role.
     *
     * @return ModuleEntity[]
     */
    public function getByRoleId(string $roleId): array;
}
