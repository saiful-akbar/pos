<?php

declare(strict_types=1);

namespace App\Application\Purchase\UseCases;

use App\Domain\Purchase\Entities\PurchaseCartDetailEntity;
use App\Domain\Purchase\Repositories\PurchaseCartDetailRepositoryInterface;

class FindPurchaseCartDetailById
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
    public function execute(string $id): ?PurchaseCartDetailEntity
    {
        return $this->purchaseCartDetailRepository->findById(
            id: $id,
        );
    }
}
