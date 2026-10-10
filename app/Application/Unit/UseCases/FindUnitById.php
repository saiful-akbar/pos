<?php

declare(strict_types=1);

namespace App\Application\Unit\UseCases;

use App\Domain\Unit\Entities\UnitEntity;
use App\Domain\Unit\Repositories\UnitRepositoryInterface;

final class FindUnitById
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private UnitRepositoryInterface $unitRepository,
    ) {}

    /**
     * Execute method
     */
    public function execute(string $id): ?UnitEntity
    {
        return $this->unitRepository->findById(
            id: $id,
        );
    }
}
