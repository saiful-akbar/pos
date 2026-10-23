<?php

declare(strict_types=1);

namespace App\Application\Cash\UseCases;

use App\Domain\Cash\Entities\CashMonthSummaryEntity;
use App\Domain\Cash\Repositories\CashReportRepositoryInterface;

class GetCashMonthlySummaries
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private CashReportRepositoryInterface $cashReportRepository,
    ) {}

    /**
     * Ringkasan kas per bulan pada rentang bulan (inklusif).
     *
     * @return CashMonthSummaryEntity[]
     */
    public function execute(int $fromYear, int $fromMonth, int $toYear, int $toMonth): array
    {
        return $this->cashReportRepository->getMonthlySummaries(
            fromYear: $fromYear,
            fromMonth: $fromMonth,
            toYear: $toYear,
            toMonth: $toMonth,
        );
    }
}
