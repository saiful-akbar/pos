<?php

declare(strict_types=1);

namespace App\Application\Dashboard\UseCases;

use App\Domain\Dashboard\Entities\ProductSalesEntity;
use App\Domain\Dashboard\Enums\DashboardPeriodEnum;
use App\Domain\Dashboard\Repositories\SaleDashboardRepositoryInterface;
use DateTimeImmutable;

class GetDashboardFastMoving
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleDashboardRepositoryInterface $saleDashboardRepository,
    ) {}

    /**
     * Top produk terlaris (berdasarkan kuantitas terjual) pada periode.
     *
     * @return ProductSalesEntity[]
     */
    public function execute(DashboardPeriodEnum $period, DateTimeImmutable $today, int $limit = 10): array
    {
        [$from, $to] = $period->range($today);

        return $this->saleDashboardRepository->getFastMovingProducts(fromDate: $from, toDate: $to, limit: $limit);
    }
}
