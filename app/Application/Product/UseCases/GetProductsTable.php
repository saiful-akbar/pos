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
    public function __construct(private ProductRepositoryInterface $repository) {}

    /**
     * Execute method.
     */
    public function execute(): JsonResponse
    {
        return DataTables::eloquent($this->repository->getQuery())->toJson();
    }
}
