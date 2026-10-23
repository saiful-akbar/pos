<?php

declare(strict_types=1);

namespace App\Application\Stock\UseCases;

use App\Domain\Stock\Repositories\MonthlyStockDetailRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class GetMonthlyStockDetailsTable
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private MonthlyStockDetailRepositoryInterface $monthlyStockDetailRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(string $monthlyStockId): JsonResponse
    {
        $dataTable = DataTables::eloquent(
            builder: $this->monthlyStockDetailRepository->getQueryByMonthlyStockId(
                monthlyStockId: $monthlyStockId,
            ),
        );

        return $dataTable->toJson();
    }
}
