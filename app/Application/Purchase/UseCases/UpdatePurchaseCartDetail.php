<?php

declare(strict_types=1);

namespace App\Application\Purchase\UseCases;

use App\Application\Purchase\DTOs\UpdatePurchaseCartDetailDTO;
use App\Application\Purchase\DTOs\UpdateQuantityAndPricePurchaseCartDTO;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Purchase\Repositories\PurchaseCartDetailRepositoryInterface;
use App\Domain\Purchase\Repositories\PurchaseCartRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdatePurchaseCartDetail
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private PurchaseCartRepositoryInterface $cartRepository,
        private PurchaseCartDetailRepositoryInterface $cartDetailRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Exceute method
     */
    public function execute(UpdatePurchaseCartDetailDTO $dto): void
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            // ambil data purchase cart berdasarkan id user yang sedang login.
            $cart = $this->cartRepository->findByUserId(
                userId: user()->id,
            );

            // validasi purcahse cart
            if ($cart === null) {
                throw ValidationException::withMessages(messages: [
                    'product_code' => 'Transaksi tidak ditemukan.',
                ]);
            }

            // Update data purchase cart detail
            $this->cartDetailRepository->update(dto: $dto);

            // ambil total quantity dari purchase cart detail.
            $totalQuantity = $this->cartDetailRepository->sumQuantityByCartId(
                cartId: $cart->id,
            );

            // ambil total dari subtotal price dari purchase cart detail.
            $totalPrice = $this->cartDetailRepository->sumSubtotalByCartId(
                cartId: $cart->id,
            );

            // Update total quantity dan total price pada purchase cart
            $this->cartRepository->updateQuantityAndPriceById(dto: new UpdateQuantityAndPricePurchaseCartDTO(
                id: $cart->id,
                totalQuantity: $totalQuantity,
                totalPrice: $totalPrice,
            ));

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
