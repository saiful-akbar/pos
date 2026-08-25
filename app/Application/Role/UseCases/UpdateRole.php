<?php

declare(strict_types=1);

namespace App\Application\Role\UseCases;

use App\Application\Role\DTOs\UpdateRoleDTO;
use App\Domain\Role\Repositories\RoleRepositoryInterface;

class UpdateRole
{
    /**
     * Create a new class instance.
     */
    public function __construct(private RoleRepositoryInterface $repository) {}

    /**
     * Execute method.
     */
    public function execute(UpdateRoleDTO $dto): void
    {
        $this->repository->update($dto);
    }
}
