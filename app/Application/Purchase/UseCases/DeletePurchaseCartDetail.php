<?php

declare(strict_types=1);

namespace App\Application\Purchase\UseCases;

use App\Application\Purchase\DTOs\PurchaseCartDTO;
use App\Domain\Purchase\Entities\PurchaseCartDetailEntity;
use App\Domain\Purchase\Entities\PurchaseCartEntity;
use App\Domain\Purchase\Repositories\PurchaseCartDetailRepositoryInterface;
use App\Domain\Purchase\Repositories\PurchaseCartRepositoryInterface;

class DeletePurchaseCartDetail
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private PurchaseCartDetailRepositoryInterface $purchaseCartDetailRepository,
        private PurchaseCartRepositoryInterface $purchaseCartRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(PurchaseCartEntity $purchaseCart, PurchaseCartDetailEntity $purchaseCartDetail): void
    {
        $this->purchaseCartDetailRepository->delete(
            id: $purchaseCartDetail->id,
        );

        $totalProduct = $this->purchaseCartDetailRepository->countProductByPurchaseCartId(
            purchaseCartId: $purchaseCart->id,
        );

        $totalQuantity = $this->purchaseCartDetailRepository->sumQuantityByPurchaseCartId(
            purchaseCartId: $purchaseCart->id,
        );

        $totalPrice = $this->purchaseCartDetailRepository->sumSubtotalByPurchaseCartId(
            purchaseCartId: $purchaseCart->id,
        );

        $this->purchaseCartRepository->update(
            id: $purchaseCart->id,
            values: new PurchaseCartDTO(
                userId: $purchaseCart->user->id,
                transactionDate: $purchaseCart->transactionDate,
                totalProduct: $totalProduct,
                totalQuantity: $totalQuantity,
                totalPrice: $totalPrice,
            ),
        );
    }
}
