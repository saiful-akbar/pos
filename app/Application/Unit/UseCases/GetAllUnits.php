<?php

declare(strict_types=1);

namespace App\Application\Unit\UseCases;

use App\Domain\Unit\Entities\UnitEntity;
use App\Domain\Unit\Repositories\UnitRepositoryInterface;

class GetAllUnits
{
    /**
     * Create a new class instance.
     */
    public function __construct(private UnitRepositoryInterface $repository) {}

    /**
     * Execute method.
     *
     * @return UnitEntity[]
     */
    public function execute(): array
    {
        return $this->repository->getAll();
    }
}
