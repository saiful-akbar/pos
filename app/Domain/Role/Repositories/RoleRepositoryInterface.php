<?php

declare(strict_types=1);

namespace App\Domain\Role\Repositories;

use App\Application\Role\DTOs\CreateRoleDTO;
use App\Domain\Role\Entities\RoleEntity;
use Illuminate\Database\Eloquent\Builder;

interface RoleRepositoryInterface
{
    /**
     * Mengambil semua data role dalam
     * bentuk query builder untuk datatable.
     *
     * @return Builder
     */
    public function getAllQuery(): Builder;

    /**
     * Membuat role baru.
     *
     * @param CreateRoleDTO $dto
     * @return RoleEntity
     */
    public function create(CreateRoleDTO $dto): RoleEntity;
}
