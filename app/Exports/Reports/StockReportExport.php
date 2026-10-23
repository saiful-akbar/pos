<?php

declare(strict_types=1);

namespace App\Exports\Reports;

use App\Application\Report\DTOs\StockReportFilterDTO;
use App\Domain\Report\Entities\StockReportSummaryEntity;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Override;

class StockReportExport implements Export, WithMultipleSheets
{
    /**
     * Create a new class instance.
     *
     * @param array<string, string> $filterLabels Keterangan filter (label => nilai).
     */
    public function __construct(
        private Builder $summaryQuery,
        private Builder $mutationQuery,
        private StockReportFilterDTO $filter,
        private StockReportSummaryEntity $summary,
        private Carbon $generatedAt,
        private array $filterLabels = [],
    ) {}

    /**
     * Sheet ringkasan rekonsiliasi dan sheet detail mutasi.
     */
    #[Override]
    public function sheets(): array
    {
        return [
            new StockReportSummarySheet($this->summaryQuery, $this->filter, $this->summary, $this->generatedAt, $this->filterLabels),
            new StockReportMutationSheet($this->mutationQuery),
        ];
    }
}
