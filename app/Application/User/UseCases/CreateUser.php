<?php

declare(strict_types=1);

namespace App\Application\User\UseCases;

use App\Application\User\DTOs\CreateUserDTO;
use App\Domain\User\Entities\UserEntity;
use App\Domain\User\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CreateUser
{
    /**
     * Create a new class instance.
     */
    public function __construct(private UserRepositoryInterface $repository) {}

    /**
     * Execute method
     */
    public function execute(CreateUserDTO $dto): UserEntity
    {
        return DB::transaction(fn () => $this->repository->create($dto));
    }
}
