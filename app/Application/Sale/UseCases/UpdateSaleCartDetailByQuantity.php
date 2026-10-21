<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Application\Sale\DTOs\SaleCartDetailDTO;
use App\Application\Sale\DTOs\SaleCartDTO;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Price\Repositories\PriceRepositoryInterface;
use App\Domain\Sale\Repositories\SaleCartDetailRepositoryInterface;
use App\Domain\Sale\Repositories\SaleCartRepositoryInterface;
use App\Http\Requests\Sales\UpdateSaleCartDetailByQuantityRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateSaleCartDetailByQuantity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleCartRepositoryInterface $cartRepository,
        private SaleCartDetailRepositoryInterface $cartDetailRepository,
        private PriceRepositoryInterface $priceRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(UpdateSaleCartDetailByQuantityRequest $request, string $cartId, string $cartDetailId): void
    {
        DB::beginTransaction();

        try {
            $unitId = $request->validated('unit_id');
            $quantity = $request->float('quantity');

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

            $productPrice = $this->priceRepository->findByProductIdAndUnitId(
                productId: $cartDetail->product->id,
                unitId: $unitId,
            );

            if ($productPrice === null) {
                throw ValidationException::withMessages(messages: [
                    'unit_id' => 'unit (satuan) tidak ditemukan pada produk yang anda dipilih.',
                ]);
            }

            $subtotal = $productPrice->sellingPrice * $quantity;
            $grandTotal = $subtotal - (($subtotal * $productPrice->product->discount) / 100);

            if ($productPrice->product->currentStock < ($quantity * $productPrice->multiplesQuantity)) {
                throw ValidationException::withMessages(messages: [
                    'quantity' => 'Stok tidak cukup.',
                ]);
            }

            $this->cartDetailRepository->update(
                id: $cartDetail->id,
                value: new SaleCartDetailDTO(
                    saleCartId: $cartDetail->saleCartId,
                    productId: $cartDetail->product->id,
                    unitId: $productPrice->unit->id,
                    multiplesQuantity: $productPrice->multiplesQuantity,
                    purchasePrice: $productPrice->purchasePrice,
                    sellingPrice: $productPrice->sellingPrice,
                    quantity: $quantity,
                    subtotal: $subtotal,
                    discount: $productPrice->product->discount,
                    grandTotal: $grandTotal,
                ),
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
