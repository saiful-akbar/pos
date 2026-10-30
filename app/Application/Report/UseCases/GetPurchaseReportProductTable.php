<?php

declare(strict_types=1);

namespace App\Application\Report\UseCases;

use App\Application\Report\DTOs\PurchaseReportFilterDTO;
use App\Domain\Report\Repositories\PurchaseReportRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class GetPurchaseReportProductTable
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private PurchaseReportRepositoryInterface $reportRepository,
    ) {}

    /**
     * Execute method (DataTables server side).
     */
    public function execute(PurchaseReportFilterDTO $filter): JsonResponse
    {
        $dataTable = DataTables::eloquent(
            builder: $this->reportRepository->getProductQuery(filter: $filter),
        );

        // Urutkan kolom hasil perhitungan dengan ekspresi SQL-nya.
        $dataTable->orderColumn('base_quantity', '(purchase_details.quantity * purchase_details.multiples_quantity) $1');

        return $dataTable->toJson();
    }
}
