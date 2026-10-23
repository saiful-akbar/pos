<?php

declare(strict_types=1);

namespace App\Application\Cash\UseCases;

use App\Domain\Cash\Entities\CashMonthSummaryEntity;
use App\Domain\Cash\Repositories\CashReportRepositoryInterface;

class GetCashMonthSummary
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private CashReportRepositoryInterface $cashReportRepository,
    ) {}

    /**
     * Ringkasan kas satu bulan (saldo awal, mutasi, saldo teoritis, saldo aktual bulan berikutnya, selisih, status).
     */
    public function execute(int $year, int $month): CashMonthSummaryEntity
    {
        // Rentang satu bulan selalu menghasilkan tepat satu baris.
        return $this->cashReportRepository->getMonthlySummaries(
            fromYear: $year,
            fromMonth: $month,
            toYear: $year,
            toMonth: $month,
        )[0];
    }
}
