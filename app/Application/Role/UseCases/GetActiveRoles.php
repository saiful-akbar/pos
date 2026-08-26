<?php

declare(strict_types=1);

namespace App\Application\Role\UseCases;

use App\Domain\Role\Entities\RoleEntity;
use App\Domain\Role\Repositories\RoleRepositoryInterface;

class GetActiveRoles
{
    /**
     * Create a new class instance.
     */
    public function __construct(private RoleRepositoryInterface $repository) {}

    /**
     * Exdcute method.
     *
     * @return RoleEntity[]
     */
    public function execute(): array
    {
        return $this->repository->getActive();
    }
}
