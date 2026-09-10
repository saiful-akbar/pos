<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Domain\Sale\Repositories\SaleCartDetailRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class GetSaleCartDetailsByCartIdTable
{
    /**
     * Create a new class instance.
     */
    public function __construct(private SaleCartDetailRepositoryInterface $cartDetailRepository) {}

    /**
     * Execute method.
     */
    public function execute(string $cartId): JsonResponse
    {
        $dataTable = DataTables::eloquent(
            builder: $this->cartDetailRepository->getByCartIdQuery(
                cartId: $cartId
            )
        );

        return $dataTable->toJson();
    }
}
