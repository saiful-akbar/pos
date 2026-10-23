<?php

declare(strict_types=1);

namespace App\Application\Report\UseCases;

use App\Application\Report\DTOs\StockReportFilterDTO;
use App\Domain\Report\Repositories\StockReportRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class GetStockReportTable
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private StockReportRepositoryInterface $stockReportRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(StockReportFilterDTO $filter): JsonResponse
    {
        return DataTables::query($this->stockReportRepository->getQuery(filter: $filter))->toJson();
    }
}
