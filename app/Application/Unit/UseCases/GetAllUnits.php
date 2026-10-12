<?php

declare(strict_types=1);

namespace App\Application\Unit\UseCases;

use App\Domain\Unit\Repositories\UnitRepositoryInterface;

class GetAllUnits
{
    /**
     * Create a new class instance.
     */
    public function __construct(private UnitRepositoryInterface $unitRepository) {}

    /**
     * Execute method
     */
    public function execute(?bool $isActive): array
    {
        return $this->unitRepository->getAll(
            isActive: $isActive,
        );
    }
}
