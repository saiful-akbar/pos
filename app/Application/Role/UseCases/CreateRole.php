<?php

declare(strict_types=1);

namespace App\Application\Role\UseCases;

use App\Application\Role\DTOs\CreateRoleDTO;
use App\Domain\Role\Repositories\RoleRepositoryInterface;

class CreateRole
{
    /**
     * Create a new class instance.
     */
    public function __construct(private RoleRepositoryInterface $repository) {}

    /**
     * Execute method.
     *
     * @param CreateRoleDTO $dto
     * @return void
     */
    public function execute(CreateRoleDTO $dto): void
    {
        $this->repository->create($dto);
    }
}
