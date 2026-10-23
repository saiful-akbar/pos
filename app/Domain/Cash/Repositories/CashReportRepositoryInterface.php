<?php

declare(strict_types=1);

namespace App\Domain\Cash\Repositories;

use App\Domain\Cash\Entities\CashMonthSummaryEntity;

interface CashReportRepositoryInterface
{
    /**
     * Mengambil ringkasan kas per bulan pada rentang bulan (inklusif), terurut dari bulan awal.
     * Setiap bulan pada rentang selalu muncul, termasuk yang belum memiliki saldo atau transaksi.
     *
     * @return CashMonthSummaryEntity[]
     */
    public function getMonthlySummaries(int $fromYear, int $fromMonth, int $toYear, int $toMonth): array;
}
