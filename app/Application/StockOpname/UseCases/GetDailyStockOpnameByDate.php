<?php

declare(strict_types=1);

namespace App\Application\StockOpname\UseCases;

use App\Domain\StockOpname\Entities\DailyStockOpnameEntity;
use App\Domain\StockOpname\Repositories\DailyStockOpnameRepositoryInterface;
use DateTimeInterface;

class GetDailyStockOpnameByDate
{
    /**
     * Create a new class instance.
     */
    public function __construct(private DailyStockOpnameRepositoryInterface $dailyStockOpnameRepository) {}

    /**
     * Mengambil daftar stock opname harian berdasarkan rentang tanggal.
     *
     * @return DailyStockOpnameEntity[]
     */
    public function execute(DateTimeInterface $fromDate, DateTimeInterface $toDate): array
    {
        return $this->dailyStockOpnameRepository->getByDate(
            fromDate: $fromDate,
            toDate: $toDate,
        );
    }
}
