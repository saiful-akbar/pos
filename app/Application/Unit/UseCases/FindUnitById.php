<?php

declare(strict_types=1);

namespace App\Application\Unit\UseCases;

use App\Domain\Unit\Entities\UnitEntity;
use App\Domain\Unit\Repositories\UnitRepositoryInterface;

class FindUnitById
{
    /**
     * Create a new class instance.
     */
    public function __construct(private UnitRepositoryInterface $repository) {}

    /**
     * Execute method.
     */
    public function execute(string $id): ?UnitEntity
    {
        return $this->repository->findById($id);
    }
}
