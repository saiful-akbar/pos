<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Domain\Sale\Repositories\SaleDetailRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class GetSaleDetailsTable
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleDetailRepositoryInterface $saleDetailRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(string $saleId): JsonResponse
    {
        $dataTable = DataTables::eloquent(
            builder: $this->saleDetailRepository->getQueryBySaleId(
                saleId: $saleId,
            ),
        );

        return $dataTable->toJson();
    }
}
