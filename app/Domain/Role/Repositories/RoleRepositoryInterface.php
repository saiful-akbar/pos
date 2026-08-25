<?php

declare(strict_types=1);

namespace App\Domain\Role\Repositories;

use App\Application\Role\DTOs\CreateRoleDTO;
use App\Application\Role\DTOs\UpdateRoleDTO;
use App\Domain\Role\Entities\RoleEntity;
use Illuminate\Database\Eloquent\Builder;

interface RoleRepositoryInterface
{
    /**
     * Mengambil semua data role dalam
     * bentuk query builder untuk datatable.
     */
    public function getAllQuery(): Builder;

    /**
     * Membuat role baru.
     */
    public function create(CreateRoleDTO $dto): RoleEntity;

    /**
     * Mengambil data role berdasrkan id.
     */
    public function findById(string $id): ?RoleEntity;

    /**
     * Update role.
     */
    public function update(UpdateRoleDTO $dto): void;
}
