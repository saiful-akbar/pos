<?php

declare(strict_types=1);

namespace App\Application\Report\UseCases;

use App\Application\Report\DTOs\SaleReportFilterDTO;
use App\Domain\Report\Repositories\SaleReportRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class GetSaleReportProductTable
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleReportRepositoryInterface $reportRepository,
    ) {}

    /**
     * Execute method (DataTables server side).
     */
    public function execute(SaleReportFilterDTO $filter): JsonResponse
    {
        $dataTable = DataTables::eloquent(
            builder: $this->reportRepository->getProductQuery(filter: $filter),
        );

        // Urutkan kolom hasil perhitungan dengan ekspresi SQL-nya.
        $dataTable->orderColumn('base_quantity', '(sale_details.quantity * sale_details.multiples_quantity) $1');

        return $dataTable->toJson();
    }
}
