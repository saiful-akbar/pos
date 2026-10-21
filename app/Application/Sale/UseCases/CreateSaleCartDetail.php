<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Application\Sale\DTOs\SaleCartDetailDTO;
use App\Application\Sale\DTOs\SaleCartDTO;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Price\Repositories\PriceRepositoryInterface;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Sale\Repositories\SaleCartDetailRepositoryInterface;
use App\Domain\Sale\Repositories\SaleCartRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateSaleCartDetail
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleCartRepositoryInterface $cartRepository,
        private SaleCartDetailRepositoryInterface $cartDetailRepository,
        private ProductRepositoryInterface $productRepository,
        private PriceRepositoryInterface $priceRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(string $cartId, string $productCode): void
    {
        DB::beginTransaction();

        try {
            $cart = $this->cartRepository->find(
                column: 'id',
                value: $cartId,
            );

            if ($cart === null) {
                abort(404, 'Transaksi penjualan tidak ditemukan');
            }

            $product = $this->productRepository->findByCode(
                code: $productCode,
            );

            if ($product === null) {
                throw ValidationException::withMessages(messages: [
                    'product_code' => 'Produk tidak ditemukan.',
                ]);
            }

            $price = $this->priceRepository->findByProductIdAndUnitId(
                productId: $product->id,
            );

            if ($price === null) {
                throw ValidationException::withMessages(messages: [
                    'product_code' => 'Produk tidak memiliki harga.',
                ]);
            }

            if ($product->currentStock <= 0) {
                throw ValidationException::withMessages(messages: [
                    'product_code' => 'Stok produk tidak cukup.',
                ]);
            }

            $cartDetail = $this->cartDetailRepository->findBySaleCartIdAndProductId(
                saleCartId: $cart->id,
                productId: $product->id,
            );

            if ($cartDetail === null) {
                $this->cartDetailRepository->create(
                    value: new SaleCartDetailDTO(
                        saleCartId: $cart->id,
                        productId: $product->id,
                        unitId: $price->unit->id,
                        multiplesQuantity: $price->multiplesQuantity,
                        purchasePrice: $price->purchasePrice,
                        sellingPrice: $price->sellingPrice,
                        quantity: 1,
                        subtotal: $price->sellingPrice,
                        discount: $product->discount,
                        grandTotal: $price->sellingPrice - (($price->sellingPrice * $product->discount) / 100),
                    ),
                );
            } else {
                $totalQuantity = $cartDetail->quantity + 1;
                $totalAmount = $cartDetail->sellingPrice * $totalQuantity;
                $grandTotalAmount = $totalAmount - (($totalAmount * $cartDetail->discount) / 100);

                $this->cartDetailRepository->update(
                    id: $cartDetail->id,
                    value: new SaleCartDetailDTO(
                        saleCartId: $cartDetail->saleCartId,
                        productId: $cartDetail->product->id,
                        unitId: $cartDetail->unit->id,
                        multiplesQuantity: $cartDetail->multiplesQuantity,
                        purchasePrice: $cartDetail->purchasePrice,
                        sellingPrice: $totalAmount,
                        quantity: $totalQuantity,
                        subtotal: $totalAmount,
                        discount: $cartDetail->discount,
                        grandTotal: $grandTotalAmount,
                    ),
                );
            }

            $totalProduct = $this->cartDetailRepository->countProductBySaleCartId(
                saleCartId: $cart->id,
            );

            $totalQuantity = $this->cartDetailRepository->sumQuantityBySaleCartId(
                saleCartId: $cart->id,
            );

            $totalAmount = $this->cartDetailRepository->sumAmountBySaleCartId(
                saleCartId: $cart->id,
            );

            $this->cartRepository->update(
                id: $cart->id,
                value: new SaleCartDTO(
                    userId: $cart->user->id,
                    transactionDate: $cart->transactionDate,
                    totalProduct: $totalProduct,
                    totalQuantity: $totalQuantity,
                    totalAmount: $totalAmount,
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
