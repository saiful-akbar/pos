<?php

declare(strict_types=1);

namespace App\Application\StockOpname\UseCases;

use App\Domain\StockOpname\Entities\DailyStockOpnameEntity;
use App\Domain\StockOpname\Repositories\DailyStockOpnameRepositoryInterface;

class FindDailyStockOpnameById
{
    /**
     * Create a new class instance.
     */
    public function __construct(private DailyStockOpnameRepositoryInterface $dailyStockOpnameRepository) {}

    /**
     * Execute method.
     */
    public function execute(string $id): ?DailyStockOpnameEntity
    {
        return $this->dailyStockOpnameRepository->findById(
            id: $id,
        );
    }
}
