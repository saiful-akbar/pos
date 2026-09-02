<?php

declare(strict_types=1);

namespace App\Application\Purchase\UseCases;

use App\Domain\Purchase\Repositories\PurchaseDetailRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class GetPurchaseDetailByPurchaseIdTable
{
    /**
     * Create a new class instance.
     */
    public function __construct(private PurchaseDetailRepositoryInterface $purchaseDetailRepository) {}

    /**
     * Execute method
     */
    public function execute(string $purchaseId): JsonResponse
    {
        $table = DataTables::eloquent(
            builder: $this->purchaseDetailRepository->getByPurchaseIdQuery(
                purchaseId: $purchaseId
            )
        );

        return $table->toJson();
    }
}
