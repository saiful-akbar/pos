<?php

declare(strict_types=1);

namespace App\Application\Purchase\UseCases;

use App\Domain\Purchase\Repositories\PurchaseCartRepositoryInterface;

class DeletePurchaseCartDetail
{
    /**
     * Create a new class instance.
     */
    public function __construct(private PurchaseCartRepositoryInterface $purchaseCartRepository) {}

    /**
     * Execute method.
     */
    public function execute(string $id): void
    {
        $this->purchaseCartRepository->deleteDetailById(id: $id);
    }
}
