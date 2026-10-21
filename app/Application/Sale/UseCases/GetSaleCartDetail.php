<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Domain\Sale\Repositories\SaleCartDetailRepositoryInterface;

class GetSaleCartDetail
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleCartDetailRepositoryInterface $cartDetailRepository
    ) {}

    /**
     * Execute method.
     */
    public function execute(string $cartId): array
    {
        return $this->cartDetailRepository->getBySaleCartId(
            saleCartId: $cartId,
        );
    }
}
