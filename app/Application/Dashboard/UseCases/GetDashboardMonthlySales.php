<?php

declare(strict_types=1);

namespace App\Application\Dashboard\UseCases;

use App\Domain\Dashboard\Entities\MonthlySummaryEntity;
use App\Domain\Dashboard\Repositories\SaleDashboardRepositoryInterface;

class GetDashboardMonthlySales
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleDashboardRepositoryInterface $saleDashboardRepository,
    ) {}

    /**
     * Ringkasan penjualan (kuantitas dan nilai) untuk 12 bulan pada tahun terpilih.
     *
     * @return MonthlySummaryEntity[]
     */
    public function execute(int $year): array
    {
        return $this->saleDashboardRepository->getMonthlySummary(year: $year);
    }
}
