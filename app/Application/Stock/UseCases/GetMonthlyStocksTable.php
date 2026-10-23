<?php

declare(strict_types=1);

namespace App\Application\Stock\UseCases;

use App\Domain\Stock\Repositories\MonthlyStockRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class GetMonthlyStocksTable
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private MonthlyStockRepositoryInterface $monthlyStockRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(int $year, ?int $month = null): JsonResponse
    {
        $dataTable = DataTables::eloquent(
            builder: $this->monthlyStockRepository->getQueryByYearAndMonth(
                year: $year,
                month: $month,
            ),
        );

        return $dataTable->toJson();
    }
}
