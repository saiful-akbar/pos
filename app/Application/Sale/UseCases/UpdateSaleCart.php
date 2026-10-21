<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Application\Sale\DTOs\SaleCartDTO;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Sale\Repositories\SaleCartRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UpdateSaleCart
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleCartRepositoryInterface $cartRepository,
        private CustomerRepositoryInterface $customerRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(string $cartId, ?string $customerId): void
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

            $customer = null;

            if ($customerId !== null) {
                $customer = $this->customerRepository->findById(
                    id: $customerId,
                );
            }

            $discount = $customer?->discount ?? 0;

            $this->cartRepository->update(
                id: $cart->id,
                value: new SaleCartDTO(
                    userId: $cart->user->id,
                    customerId: $customer?->id,
                    transactionDate: $cart->transactionDate,
                    totalProduct: $cart->totalProduct,
                    totalQuantity: $cart->totalQuantity,
                    totalAmount: $cart->totalAmount,
                    discount: $discount,
                    grandTotal: $cart->totalAmount - (($cart->totalAmount * $discount) / 100),
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
