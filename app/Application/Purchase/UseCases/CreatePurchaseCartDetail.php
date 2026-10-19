<?php

declare(strict_types=1);

namespace App\Application\Purchase\UseCases;

use App\Application\Purchase\DTOs\PurchaseCartDetailDTO;
use App\Application\Purchase\DTOs\PurchaseCartDTO;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Price\Repositories\PriceRepositoryInterface;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Purchase\Entities\PurchaseCartEntity;
use App\Domain\Purchase\Repositories\PurchaseCartDetailRepositoryInterface;
use App\Domain\Purchase\Repositories\PurchaseCartRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreatePurchaseCartDetail
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private PurchaseCartDetailRepositoryInterface $purchaseCartDetailRepository,
        private PurchaseCartRepositoryInterface $purchaseCartRepository,
        private ProductRepositoryInterface $productRepository,
        private PriceRepositoryInterface $productPriceRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(PurchaseCartEntity $purchaseCart, string $productCode): void
    {
        DB::beginTransaction();

        try {
            $product = $this->productRepository->findByCode(
                code: $productCode,
            );

            if ($product === null) {
                throw ValidationException::withMessages(messages: [
                    'product_code' => 'Produk tidak ditemukan.',
                ]);
            }

            $productPrice = $this->productPriceRepository->findByProductIdAndUnitId(
                productId: $product->id,
            );

            if ($productPrice === null) {
                throw ValidationException::withMessages(messages: [
                    'product_code' => 'Produk tidak memiliki harga.',
                ]);
            }

            $purchaseCartDetail = $this->purchaseCartDetailRepository->findByPurchaseCartIdAndProductId(
                purchaseCartId: $purchaseCart->id,
                productId: $productPrice->product->id,
            );

            if ($purchaseCartDetail === null) {
                $this->purchaseCartDetailRepository->create(
                    values: new PurchaseCartDetailDTO(
                        purchaseCartId: $purchaseCart->id,
                        productId: $productPrice->product->id,
                        unitId: $productPrice->unit->id,
                        multiplesQuantity: $productPrice->multiplesQuantity,
                        quantity: 1,
                        price: $productPrice->purchasePrice,
                        subtotal: $productPrice->purchasePrice * 1,
                    ),
                );
            } else {
                $this->purchaseCartDetailRepository->update(
                    id: $purchaseCartDetail->id,
                    values: new PurchaseCartDetailDTO(
                        purchaseCartId: $purchaseCart->id,
                        productId: $productPrice->product->id,
                        unitId: $productPrice->unit->id,
                        multiplesQuantity: $productPrice->multiplesQuantity,
                        quantity: $purchaseCartDetail->quantity + 1,
                        price: $productPrice->purchasePrice,
                        subtotal: $productPrice->purchasePrice * ($purchaseCartDetail->quantity + 1),
                    ),
                );
            }

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

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            $this->errorRepository->create(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }
}
