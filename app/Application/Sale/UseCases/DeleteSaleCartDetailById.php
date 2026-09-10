<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Application\Sale\DTOs\SaleCartDTO;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Sale\Repositories\SaleCartDetailRepositoryInterface;
use App\Domain\Sale\Repositories\SaleCartRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeleteSaleCartDetailById
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleCartDetailRepositoryInterface $cartDetailRepository,
        private SaleCartRepositoryInterface $cartRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(string $cartId, string $cartDetailId): int
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $cart = $this->cartRepository->findById(
                id: $cartId
            );

            if ($cart === null) {
                throw ValidationException::withMessages(messages: [
                    'message' => 'Transaksi tidak ditemukan.',
                ]);
            }

            // ambil data sale_cart_details
            $cartDetail = $this->cartDetailRepository->findById(
                id: $cartDetailId
            );

            if ($cartDetail === null) {
                throw ValidationException::withMessages(messages: [
                    'message' => 'Produk tidak ditemukan.',
                ]);
            }

            // hapus data sale_cart_details.
            $deleted = $this->cartDetailRepository->deleteById(
                id: $cartDetailId
            );

            // hitung dan ambil total quantity setelah data sale_carts detail dihapus.
            $totalQuantity = $this->cartDetailRepository->sumQuantityByCartId(
                cartId: $cart->id
            );

            // hitung dan ambil subtotal (total harga) setelah sale_carts detail dihapus.
            $totalAmount = $this->cartDetailRepository->sumSubtotalByCartId(
                cartId: $cart->id
            );

            // update total quanatity dan total amount pada sale_carts.
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

            return $deleted;
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
