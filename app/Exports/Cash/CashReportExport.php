<?php

declare(strict_types=1);

namespace App\Exports\Cash;

use App\Application\Cash\DTOs\CashReportFilterDTO;
use App\Domain\Cash\Entities\CashMonthSummaryEntity;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Override;

class CashReportExport implements Export, WithMultipleSheets
{
    /**
     * Create a new class instance.
     *
     * @param CashMonthSummaryEntity[]                              $summaries
     * @param array{incoming: string, outgoing: string, count: int} $totals
     */
    public function __construct(
        private Builder $transactionsQuery,
        private array $summaries,
        private array $totals,
        private CashReportFilterDTO $filter,
        private Carbon $generatedAt,
    ) {}

    /**
     * Sheet transaksi, rekap mutasi bulanan, dan rekonsiliasi bulanan.
     */
    #[Override]
    public function sheets(): array
    {
        return array_map(
            fn (string $type): CashReportSheet => new CashReportSheet(
                $type,
                $this->transactionsQuery,
                $this->summaries,
                $this->totals,
                $this->filter,
                $this->generatedAt,
            ),
            ['transactions', 'monthly', 'reconciliation'],
        );
    }
}
