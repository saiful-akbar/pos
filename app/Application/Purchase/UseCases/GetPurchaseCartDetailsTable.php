<?php

declare(strict_types=1);

namespace App\Application\Purchase\UseCases;

use App\Domain\Purchase\Repositories\PurchaseCartDetailRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class GetPurchaseCartDetailsTable
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private PurchaseCartDetailRepositoryInterface $purchaseCartDetailRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(string $purchaseCartId): JsonResponse
    {
        $dataTable = DataTables::eloquent(
            builder: $this->purchaseCartDetailRepository->getQueryByPurchaseCartId(
                purchaseCartId: $purchaseCartId,
            ),
        );

        return $dataTable->toJson();
    }
}
