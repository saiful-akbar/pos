<?php

declare(strict_types=1);

namespace App\Application\Price\UseCases;

use App\Domain\Price\Repositories\PriceRepositoryInterface;
use App\Domain\Product\Entities\ProductEntity;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class GetPricesTableByProduct
{
    /**
     * Create a new class instance.
     */
    public function __construct(private PriceRepositoryInterface $priceRepository) {}

    /**
     * Execute method.
     */
    public function execute(ProductEntity $product): JsonResponse
    {
        $dataTable = DataTables::eloquent(
            builder: $this->priceRepository->getQueryByProductId(
                productId: $product->id,
            )
        );

        return $dataTable->toJson();
    }
}
