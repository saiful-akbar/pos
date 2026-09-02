<?php

declare(strict_types=1);

namespace App\Application\Purchase\UseCases;

use App\Domain\Purchase\Entities\PurchaseEntity;
use App\Domain\Purchase\Repositories\PurchaseRepositoryInterface;

class FindPurchaseById
{
    /**
     * Create a new class instance.
     */
    public function __construct(private PurchaseRepositoryInterface $purchaseRepository) {}

    /**
     * Execute method.
     */
    public function execute(string $id): ?PurchaseEntity
    {
        return $this->purchaseRepository->findById(id: $id);
    }
}
