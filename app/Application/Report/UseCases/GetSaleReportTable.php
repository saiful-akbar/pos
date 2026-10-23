<?php

declare(strict_types=1);

namespace App\Application\Report\UseCases;

use App\Application\Report\DTOs\SaleReportFilterDTO;
use App\Domain\Report\Repositories\SaleReportRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class GetSaleReportTable
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleReportRepositoryInterface $saleReportRepository,
    ) {}

    /**
     * Execute method (DataTables server side).
     */
    public function execute(SaleReportFilterDTO $filter): JsonResponse
    {
        $dataTable = DataTables::eloquent(
            builder: $this->saleReportRepository->getQuery(filter: $filter),
        );

        // Kolom hasil perhitungan diurutkan memakai alias select-nya.
        $dataTable->orderColumn('items_subtotal', 'items_subtotal $1');
        $dataTable->orderColumn('base_quantity', 'base_quantity $1');
        $dataTable->orderColumn('discount_amount', 'discount_amount $1');

        return $dataTable->toJson();
    }
}
