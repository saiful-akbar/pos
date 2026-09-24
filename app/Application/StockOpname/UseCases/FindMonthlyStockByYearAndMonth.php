<?php

declare(strict_types=1);

namespace App\Application\StockOpname\UseCases;

use App\Domain\StockOpname\Entities\MonthlyStockOpnameEntity;
use App\Domain\StockOpname\Repositories\MonthlyStockOpnameRepositoryInterface;

class FindMonthlyStockByYearAndMonth
{
    /**
     * Create a new class instance.
     */
    public function __construct(private MonthlyStockOpnameRepositoryInterface $monthlyStockRepository) {}

    /**
     * Execute method
     */
    public function execute(int $year, int $month): ?MonthlyStockOpnameEntity
    {
        return $this->monthlyStockRepository->findByYearAndMonth(
            year: $year,
            month: $month,
        );
    }
}
