<?php

declare(strict_types=1);

namespace App\Application\Stock\UseCases;

use App\Domain\Stock\Entities\DailyStockEntity;
use App\Domain\Stock\Repositories\DailyStockRepositoryInterface;
use DateTimeInterface;

class GetDailyStockByDateRange
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private DailyStockRepositoryInterface $dailyStockRepository,
    ) {}

    /**
     * Execute method.
     *
     * @return array<DailyStockEntity>
     */
    public function execute(DateTimeInterface $startDate, DateTimeInterface $endDate): array
    {
        return $this->dailyStockRepository->getByDateRange(
            startDate: $startDate,
            endDate: $endDate,
        );
    }
}
