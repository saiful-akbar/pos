<?php

declare(strict_types=1);

namespace App\Application\Purcahse\UseCases;

use App\Domain\Purchase\Entities\PurchaseCartDetailEntity;
use App\Domain\Purchase\Repositories\PurchaseCartDetailRepositoryInterface;

class GetPurchaseCartDetailByCartId
{
    /**
     * Create a new class instance.
     */
    public function __construct(private PurchaseCartDetailRepositoryInterface $cartDetailRepository) {}

    /**
     * Mengambil daftar cart detail berdasarkan cart id.
     *
     * @return PurchaseCartDetailEntity[]
     */
    public function execute(string $cartId): array
    {
        return $this->cartDetailRepository->getByCartId(
            cartId: $cartId,
        );
    }
}
