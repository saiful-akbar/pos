<?php

declare(strict_types=1);

namespace App\Application\Purchase\UseCases;

use App\Domain\Purchase\Entities\PurchaseEntity;
use App\Domain\Purchase\Repositories\PurchaseCartRepositoryInterface;

class FindPurchaseCartByCurrentUser
{
    /**
     * Create a new class instance.
     */
    public function __construct(private PurchaseCartRepositoryInterface $purchaseCartRepository) {}

    /**
     * Execute method
     */
    public function execute(): ?PurchaseEntity
    {
        return $this->purchaseCartRepository->findByUser(userId: user()->id);
    }
}
