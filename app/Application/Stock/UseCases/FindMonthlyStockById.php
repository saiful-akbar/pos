<?php

declare(strict_types=1);

namespace App\Application\Stock\UseCases;

use App\Domain\Stock\Entities\MonthlyStockEntity;
use App\Domain\Stock\Repositories\MonthlyStockRepositoryInterface;

class FindMonthlyStockById
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private MonthlyStockRepositoryInterface $monthlyStockRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(string $id): ?MonthlyStockEntity
    {
        return $this->monthlyStockRepository->findById(
            id: $id,
        );
    }
}
