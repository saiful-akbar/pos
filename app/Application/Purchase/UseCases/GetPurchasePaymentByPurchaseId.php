<?php

declare(strict_types=1);

namespace App\Application\Purchase\UseCases;

use App\Domain\Purchase\Entities\PurchasePaymentEntity;
use App\Domain\Purchase\Repositories\PurchasePaymentRepositoryInterface;

class GetPurchasePaymentByPurchaseId
{
    /**
     * Create a new class instance.
     */
    public function __construct(private PurchasePaymentRepositoryInterface $paymentRepository) {}

    /**
     * Execute method.
     *
     * @return PurchasePaymentEntity[]
     */
    public function execute(string $purchaseId): array
    {
        return $this->paymentRepository->getByPurchaseId(
            purchaseId: $purchaseId,
        );
    }
}
