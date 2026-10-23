<?php

declare(strict_types=1);

namespace App\Application\Stock\UseCases;

use App\Domain\Stock\Entities\DailyStockEntity;
use App\Domain\Stock\Repositories\DailyStockRepositoryInterface;

class FindDailyStockById
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private DailyStockRepositoryInterface $dailyStockRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(string $id): ?DailyStockEntity
    {
        return $this->dailyStockRepository->findById(
            id: $id,
        );
    }
}
