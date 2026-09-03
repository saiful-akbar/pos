<?php

declare(strict_types=1);

namespace App\Application\Purchase\UseCases;

use App\Domain\Purchase\Repositories\PurchasePaymentRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class GetPurchasePaymentByPurchaseIdTable
{
    /**
     * Create a new class instance.
     */
    public function __construct(private PurchasePaymentRepositoryInterface $paymentRepository) {}

    /**
     * Execute method.
     */
    public function execute(string $purchaseId): JsonResponse
    {
        return DataTables::eloquent($this->paymentRepository->getByPurchaseIdQuery($purchaseId))->toJson();
    }
}
