<?php

declare(strict_types=1);

namespace App\Application\Dashboard\UseCases;

use App\Domain\Dashboard\Entities\SalesTotalsEntity;
use App\Domain\Dashboard\Repositories\SaleDashboardRepositoryInterface;
use DateTimeInterface;

class GetDashboardSalesTotals
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleDashboardRepositoryInterface $saleDashboardRepository,
    ) {}

    /**
     * Total nilai penjualan hari ini, bulan ini, dan tahun ini.
     */
    public function execute(DateTimeInterface $today): SalesTotalsEntity
    {
        return $this->saleDashboardRepository->getSalesTotals(today: $today);
    }
}
