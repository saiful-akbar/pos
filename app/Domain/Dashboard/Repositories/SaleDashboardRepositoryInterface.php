<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Repositories;

use App\Domain\Dashboard\Entities\MonthlySummaryEntity;
use DateTimeInterface;
use Illuminate\Support\Collection;

interface SaleDashboardRepositoryInterface
{
    /**
     * Mengambil summary data perbulan
     *
     * @return MonthlySummaryEntity[]
     */
    public function getMonthlySummary(int $year): array;

    /**
     * Mengambil produk terlaris
     */
    public function getBestSellingProduct(DateTimeInterface $fromDate, DateTimeInterface $toDate): Collection;
}
