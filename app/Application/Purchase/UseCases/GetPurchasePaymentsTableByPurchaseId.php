<?php

declare(strict_types=1);

namespace App\Application\Purchase\UseCases;

use App\Domain\Purchase\Repositories\PurchasePaymentRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class GetPurchasePaymentsTableByPurchaseId
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private PurchasePaymentRepositoryInterface $purchasePaymentRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(string $purchaseId): JsonResponse
    {
        $dataTable = DataTables::eloquent(
            builder: $this->purchasePaymentRepository->getQueryByPurchaseId(
                purchaseId: $purchaseId,
            ),
        );

        return $dataTable->toJson();
    }
}
