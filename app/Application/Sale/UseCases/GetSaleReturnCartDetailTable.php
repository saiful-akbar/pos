<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Domain\Sale\Repositories\SaleReturnCartDetailRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class GetSaleReturnCartDetailTable
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleReturnCartDetailRepositoryInterface $cartDetailRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(string $cartId): JsonResponse
    {
        $dataTable = DataTables::eloquent(
            builder: $this->cartDetailRepository->getQueryBySaleReturnCartId(
                saleReturnCartId: $cartId,
            ),
        );

        return $dataTable->toJson();
    }
}
