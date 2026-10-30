<?php

declare(strict_types=1);

namespace App\Application\Report\UseCases;

use App\Application\Report\DTOs\SaleReturnReportFilterDTO;
use App\Domain\Report\Repositories\SaleReturnReportRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class GetSaleReturnReportProductTable
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleReturnReportRepositoryInterface $reportRepository,
    ) {}

    /**
     * Execute method (DataTables server side).
     */
    public function execute(SaleReturnReportFilterDTO $filter): JsonResponse
    {
        $dataTable = DataTables::eloquent(
            builder: $this->reportRepository->getProductQuery(filter: $filter),
        );

        // Urutkan kolom hasil perhitungan dengan ekspresi SQL-nya.
        $dataTable->orderColumn('base_quantity', '(sale_return_details.quantity * sale_return_details.multiples_quantity) $1');

        return $dataTable->toJson();
    }
}
