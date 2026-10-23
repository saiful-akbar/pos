<?php

declare(strict_types=1);

namespace App\Application\Stock\UseCases;

use App\Domain\Stock\Repositories\DailyStockDetailRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class GetDailyStockDetailsTable
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private DailyStockDetailRepositoryInterface $dailyStockDetailRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(string $dailyStockId): JsonResponse
    {
        $dataTable = DataTables::eloquent(
            builder: $this->dailyStockDetailRepository->getQueryByDailyStockId(
                dailyStockId: $dailyStockId,
            ),
        );

        return $dataTable->toJson();
    }
}
