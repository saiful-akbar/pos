<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Application\Sale\DTOs\SaleCartDTO;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Sale\Repositories\SaleCartDetailRepositoryInterface;
use App\Domain\Sale\Repositories\SaleCartRepositoryInterface;
use Illuminate\Support\Facades\DB;

class DeleteSaleCartDetail
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleCartRepositoryInterface $cartRepository,
        private SaleCartDetailRepositoryInterface $cartDetailRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(string $cartId, string $cartDetailId): void
    {
        DB::beginTransaction();

        try {
            $cart = $this->cartRepository->find(
                column: 'id',
                value: $cartId,
            );

            if ($cart === null) {
                abort(404, 'Transaksi penjualan tidak ditemukan.');
            }

            $cartDetail = $this->cartDetailRepository->find(
                column: 'id',
                value: $cartDetailId,
            );

            if ($cart === null) {
                abort(404, 'Produk tidak ditemukan.');
            }

            $this->cartDetailRepository->delete(
                id: $cartDetail->id,
            );

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
                    customerId: $cart->customer?->id,
                    transactionDate: $cart->transactionDate,
                    totalProduct: $totalProduct,
                    totalQuantity: $totalQuantity,
                    totalAmount: $totalAmount,
                    discount: $cart->discount,
                    grandTotal: $totalAmount - (($totalAmount * $cart->discount) / 100),
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
