<?php

declare(strict_types=1);

namespace App\Application\StockOpname\UseCases;

use App\Domain\StockOpname\Entities\MonthlyStockOpnameDetailEntity;
use App\Domain\StockOpname\Repositories\MonthlyStockOpnameDetailRepositoryInterface;

class GetMonthlyStockDetails
{
    /**
     * Create a new class instance.
     */
    public function __construct(private MonthlyStockOpnameDetailRepositoryInterface $monthlyStockDetailRepository) {}

    /**
     * Execute method.
     *
     * @return MonthlyStockOpnameDetailEntity[]
     */
    public function execute(string $monthlyStockId): array
    {
        return $this->monthlyStockDetailRepository->getByMonthlyStockOpnameId($monthlyStockId);
    }
}
