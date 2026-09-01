<?php

declare(strict_types=1);

namespace App\Application\Purchase\UseCases;

use App\Domain\Purchase\Entities\PurchaseDetailEntity;
use App\Domain\Purchase\Repositories\PurchaseCartRepositoryInterface;

class FindPurchaseCartWithDetails
{
    /**
     * Create a new class instance.
     */
    public function __construct(private PurchaseCartRepositoryInterface $cartRepository) {}

    /**
     * Execute method.
     */
    public function execute(): ?PurchaseDetailEntity
    {
        return $this->cartRepository->findWithDetails();
    }
}
