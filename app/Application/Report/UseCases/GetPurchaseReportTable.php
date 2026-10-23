<?php

declare(strict_types=1);

namespace App\Application\Report\UseCases;

use App\Application\Report\DTOs\PurchaseReportFilterDTO;
use App\Domain\Report\Repositories\PurchaseReportRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class GetPurchaseReportTable
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private PurchaseReportRepositoryInterface $purchaseReportRepository,
    ) {}

    /**
     * Execute method (DataTables server side).
     */
    public function execute(PurchaseReportFilterDTO $filter): JsonResponse
    {
        $dataTable = DataTables::eloquent(
            builder: $this->purchaseReportRepository->getQuery(filter: $filter),
        );

        // Urutkan kolom sisa utang (kolom hasil perhitungan) dengan ekspresi SQL-nya.
        $dataTable->orderColumn('outstanding', 'GREATEST(purchases.total_price - purchases.already_paid, 0) $1');

        return $dataTable->toJson();
    }
}
