<?php

declare(strict_types=1);

namespace App\Domain\User\Repositories;

use App\Application\User\DTOs\CreateUserDTO;
use App\Application\User\DTOs\UpdateUserDTO;
use App\Domain\User\Entities\UserEntity;
use Illuminate\Database\Eloquent\Builder;

interface UserRepositoryInterface
{
    /**
     * Mengambil data user dalam form query builder
     * untuk dataTable.
     */
    public function GetQuery(): Builder;

    /**
     * Mengambil data user berdasarkan id.
     */
    public function findById(string $id): ?UserEntity;

    /**
     * Menambah user baru.
     */
    public function create(CreateUserDTO $dto): UserEntity;

    /**
     * Update user
     */
    public function update(UpdateUserDTO $dto): void;
}
