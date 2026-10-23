<?php

declare(strict_types=1);

namespace App\Application\Report\UseCases;

use App\Domain\Report\Repositories\SaleReturnReportRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class GetSaleReturnReportDetailsTable
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
    public function execute(string $saleReturnId): JsonResponse
    {
        return DataTables::eloquent(
            builder: $this->saleReturnReportRepository->getDetailQuery(saleReturnId: $saleReturnId),
        )->toJson();
    }
}
