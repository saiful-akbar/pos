<?php

declare(strict_types=1);

namespace App\Application\Purchase\UseCases;

use App\Domain\Purchase\Entities\PurchaseCartEntity;
use App\Domain\Purchase\Repositories\PurchaseCartDetailRepositoryInterface;

class GetPurchaseCartDetailsByPurchaseCartId
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
    public function execute(PurchaseCartEntity $purchaseCart): array
    {
        return $this->purchaseCartDetailRepository->getByPurchaseCartId(
            purchaseCartId: $purchaseCart->id,
        );
    }
}
