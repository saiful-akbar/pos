<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Application\Sale\DTOs\SaleCartDetailDTO;
use App\Application\Sale\DTOs\SaleCartDTO;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
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
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(string $cartId, string $productCode): void
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $cart = $this->cartRepository->findById(id: $cartId);

            // validasi data cart
            if ($cart === null) {
                throw ValidationException::withMessages([
                    'product' => 'Tidak ada transaksi yang ditemukan.',
                ]);
            }

            // Ambil data produk
            $product = $this->productRepository->findByCodeOrBarcode(value: $productCode);

            // validasi product
            if ($product === null) {
                throw ValidationException::withMessages([
                    'product' => 'Produk tidak ditemukan.',
                ]);
            }

            // Validasi quantity
            if ($product->currentStock < 1) {
                throw ValidationException::withMessages([
                    'product' => 'Quantity produk tidak cukup.',
                ]);
            }

            // tambah data cart detail.
            $this->cartDetailRepository->createOrUpdate(
                dto: new SaleCartDetailDTO(
                    cart: $cart,
                    product: $product,
                    quantity: 1,
                ),
            );

            // ambil total quantity dan amount dari cart detail.
            $totalQuantity = $this->cartDetailRepository->sumQuantityByCartId(cartId: $cart->id);
            $totalAmount = $this->cartDetailRepository->sumSubtotalByCartId(cartId: $cart->id);

            // update total quantity dan amount pada cart.
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
