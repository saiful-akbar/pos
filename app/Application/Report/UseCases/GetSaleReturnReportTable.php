<?php

declare(strict_types=1);

namespace App\Application\Report\UseCases;

use App\Application\Report\DTOs\SaleReturnReportFilterDTO;
use App\Domain\Report\Repositories\SaleReturnReportRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class GetSaleReturnReportTable
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleReturnReportRepositoryInterface $saleReturnReportRepository,
    ) {}

    /**
     * Execute method (DataTables server side).
     */
    public function execute(SaleReturnReportFilterDTO $filter): JsonResponse
    {
        $dataTable = DataTables::eloquent(
            builder: $this->saleReturnReportRepository->getQuery(filter: $filter),
        );

        // Kolom hasil perhitungan diurutkan memakai alias select-nya.
        $dataTable->orderColumn('items_subtotal', 'items_subtotal $1');
        $dataTable->orderColumn('base_quantity', 'base_quantity $1');
        $dataTable->orderColumn('discount_amount', 'discount_amount $1');

        return $dataTable->toJson();
    }
}
