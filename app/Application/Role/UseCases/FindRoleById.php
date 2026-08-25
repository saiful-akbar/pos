<?php

declare(strict_types=1);

namespace App\Application\Role\UseCases;

use App\Domain\Role\Entities\RoleEntity;
use App\Domain\Role\Repositories\RoleRepositoryInterface;

class FindRoleById
{
    /**
     * Create a new class instance.
     */
    public function __construct(private RoleRepositoryInterface $repository) {}

    /**
     * Execute method.
     */
    public function execute(string $id): ?RoleEntity
    {
        return $this->repository->findById($id);
    }
}
