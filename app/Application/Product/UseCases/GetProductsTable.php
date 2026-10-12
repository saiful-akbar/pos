<?php

declare(strict_types=1);

namespace App\Application\Product\UseCases;

use App\Domain\Product\Repositories\ProductRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class GetProductsTable
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(): JsonResponse
    {
        $dataTable = DataTables::eloquent(
            builder: $this->productRepository->getQuery(),
        );

        return $dataTable->toJson();
    }
}
