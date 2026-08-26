<?php

declare(strict_types=1);

namespace App\Domain\User\Repositories;

use App\Application\User\DTOs\CreateUserDTO;
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
     *
     * @param CreateUserDTO $dto
     * @return UserEntity
     */
    public function create(CreateUserDTO $dto): UserEntity;
}
