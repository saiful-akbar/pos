<?php

declare(strict_types=1);

namespace App\Application\Stock\UseCases;

use App\Domain\Stock\Repositories\MonthlyStockDetailRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class GetMonthlyStockDetailsQuery
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private MonthlyStockDetailRepositoryInterface $monthlyStockDetailRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(string $monthlyStockId): Builder
    {
        return $this->monthlyStockDetailRepository->getQueryByMonthlyStockId(
            monthlyStockId: $monthlyStockId,
        );
    }
}
