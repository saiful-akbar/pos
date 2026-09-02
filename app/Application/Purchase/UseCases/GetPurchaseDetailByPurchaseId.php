<?php

declare(strict_types=1);

namespace App\Application\Purchase\UseCases;

use App\Domain\Purchase\Entities\PurchaseDetailEntity;
use App\Domain\Purchase\Repositories\PurchaseDetailRepositoryInterface;

class GetPurchaseDetailByPurchaseId
{
    /**
     * Create a new class instance.
     */
    public function __construct(private PurchaseDetailRepositoryInterface $purchaseDetailRepository) {}

    /**
     * Execute method.
     *
     * @return PurchaseDetailEntity[]
     */
    public function execute(string $purchaseId): array
    {
        return $this->purchaseDetailRepository->getByPurchaseId(purchaseId: $purchaseId);
    }
}
