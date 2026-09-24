<?php

declare(strict_types=1);

namespace App\Application\StockOpname\UseCases;

use App\Domain\StockOpname\Entities\DailyStockOpnameEntity;
use App\Domain\StockOpname\Repositories\DailyStockOpnameRepositoryInterface;
use DateTimeInterface;

class FindDailyStockOpnameByDate
{
    /**
     * Create a new class instance.
     */
    public function __construct(private DailyStockOpnameRepositoryInterface $dailyStockOpnameRepository) {}

    /**
     * Execute method.
     */
    public function execute(DateTimeInterface $date): ?DailyStockOpnameEntity
    {
        return $this->dailyStockOpnameRepository->findByDate($date);
    }
}
