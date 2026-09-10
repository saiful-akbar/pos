<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Application\Sale\DTOs\SaleCartDetailDTO;
use App\Application\Sale\DTOs\SaleCartDTO;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Product\Entities\ProductEntity;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Sale\Repositories\SaleCartDetailRepositoryInterface;
use App\Domain\Sale\Repositories\SaleCartRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateQuantitySalesCartDetailById
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleCartDetailRepositoryInterface $cartDetailRepository,
        private SaleCartRepositoryInterface $cartRepository,
        private ProductRepositoryInterface $productRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(string $cartId, string $cartDetailId, int $quantity): void
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $cart = $this->cartRepository->findById(
                id: $cartId,
            );

            // validasi data cart.
            if ($cart === null) {
                throw ValidationException::withMessages(messages: [
                    'message' => 'Transaksi tidak ditemukan.',
                ]);
            }

            // ambil data cart detail.
            $cartDetail = $this->cartDetailRepository->findById(
                id: $cartDetailId,
            );

            // validasi data cart detail.
            if ($cartDetail === null) {
                throw ValidationException::withMessages(messages: [
                    'message' => 'Produk tidak ditemukan.',
                ]);
            }

            // ambil data produk.
            $product = $this->productRepository->findById(
                id: $cartDetail->product->id,
                response: ProductEntity::class,
            );

            // validasi data product.
            if ($product === null) {
                throw ValidationException::withMessages(messages: [
                    'message' => 'Produk tidak ditemukan.',
                ]);
            }

            // update data product.
            $this->cartDetailRepository->update(
                id: $cartDetail->id,
                dto: new SaleCartDetailDTO(
                    cart: $cart,
                    product: $product,
                    quantity: $quantity,
                ),
            );

            // ambil total quantity dan total amount dari cart detail setelah diupdate.
            $totalQuantity = $this->cartDetailRepository->sumQuantityByCartId(cartId: $cart->id);
            $totalAmount = $this->cartDetailRepository->sumSubtotalByCartId(cartId: $cart->id);

            // update total quantity dan total amount pada cart
            $this->cartRepository->update(
                id: $cart->id,
                dto: new SaleCartDTO(
                    userId: $cart->user->id,
                    isPending: $cart->isPending,
                    transactionDate: $cart->transactionDate,
                    totalQuantity: $totalQuantity,
                    totalAmount: $totalAmount
                ),
            );

            DB::connection(pos_connection())->commit();
        } catch (\Throwable $e) {
            DB::connection(pos_connection())->rollBack();

            $this->errorRepository->create(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }
}
