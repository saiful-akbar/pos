<?php

declare(strict_types=1);

namespace App\Application\User\UseCases;

use App\Domain\User\Entities\UserEntity;
use App\Domain\User\Repositories\UserRepositoryInterface;

class FindUserById
{
    /**
     * Create a new class instance.
     */
    public function __construct(private UserRepositoryInterface $repository) {}

    /**
     * Execute method.
     */
    public function execute(string $id): ?UserEntity
    {
        return $this->repository->findById(
            id: $id,
        );
    }
}
