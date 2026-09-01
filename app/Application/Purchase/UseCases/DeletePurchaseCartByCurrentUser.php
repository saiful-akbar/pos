<?php

declare(strict_types=1);

namespace App\Application\Purchase\UseCases;

use App\Domain\Purchase\Repositories\PurchaseCartRepositoryInterface;

class DeletePurchaseCartByCurrentUser
{
    /**
     * Create a new class instance.
     */
    public function __construct(private PurchaseCartRepositoryInterface $cartRepository) {}

    /**
     * Execute method.
     */
    public function execute(): void
    {
        $this->cartRepository->deleteByUserId(
            userId: user()->id,
        );
    }
}
