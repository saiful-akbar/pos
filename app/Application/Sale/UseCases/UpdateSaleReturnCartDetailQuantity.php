<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Application\Sale\DTOs\SaleReturnCartDetailDTO;
use App\Application\Sale\DTOs\SaleReturnCartDTO;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Sale\Repositories\SaleDetailRepositoryInterface;
use App\Domain\Sale\Repositories\SaleReturnCartDetailRepositoryInterface;
use App\Domain\Sale\Repositories\SaleReturnCartRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateSaleReturnCartDetailQuantity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleReturnCartRepositoryInterface $cartRepository,
        private SaleReturnCartDetailRepositoryInterface $cartDetailRepository,
        private SaleDetailRepositoryInterface $saleDetailRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(string $cartId, string $cartDetailId, float $quantity): void
    {
        DB::beginTransaction();

        try {
            // Ambil data keranjang pengembalian penjualan berdasarkan ID.
            $cart = $this->cartRepository->findById(
                id: $cartId,
            );

            if ($cart === null) {
                abort(404, 'Keranjang pengembalian penjualan tidak ditemukan.');
            }

            // Ambil data detail keranjang pengembalian berdasarkan ID.
            $cartDetail = $this->cartDetailRepository->findById(
                id: $cartDetailId,
            );

            // Detail harus ada dan harus milik keranjang yang sedang diproses.
            if ($cartDetail === null || $cartDetail->saleReturnCartId !== $cart->id) {
                abort(404, 'Produk pada keranjang pengembalian tidak ditemukan.');
            }

            // Kuantitas tidak boleh 0 atau minus.
            if ($quantity <= 0) {
                throw ValidationException::withMessages(messages: [
                    'quantity' => 'Quantity harus lebih dari 0.',
                ]);
            }

            // Ambil detail penjualan asli untuk mengetahui jumlah yang terjual.
            $saleDetail = $this->saleDetailRepository->findBySaleIdAndProductId(
                saleId: $cart->saleId,
                productId: $cartDetail->product->id,
            );

            if ($saleDetail === null) {
                abort(404, 'Produk tidak terdapat pada transaksi penjualan ini.');
            }

            // Kuantitas pengembalian tidak boleh melebihi kuantitas yang terjual.
            if ($quantity > $saleDetail->quantity) {
                throw ValidationException::withMessages(messages: [
                    'quantity' => "Quantity tidak boleh melebihi jumlah yang terjual ({$saleDetail->quantity}).",
                ]);
            }

            // Hitung ulang subtotal dan grand total berdasarkan quantity baru.
            $subtotal = $cartDetail->sellingPrice * $quantity;
            $grandTotal = $subtotal - (($subtotal * $cartDetail->discount) / 100);

            // Update quantity, subtotal, dan grand total pada detail keranjang.
            $this->cartDetailRepository->update(
                id: $cartDetail->id,
                value: new SaleReturnCartDetailDTO(
                    saleReturnCartId: $cartDetail->saleReturnCartId,
                    productId: $cartDetail->product->id,
                    unitId: $cartDetail->unit->id,
                    multiplesQuantity: $cartDetail->multiplesQuantity,
                    purchasePrice: $cartDetail->purchasePrice,
                    sellingPrice: $cartDetail->sellingPrice,
                    quantity: $quantity,
                    subtotal: $subtotal,
                    discount: $cartDetail->discount,
                    grandTotal: $grandTotal,
                ),
            );

            // Hitung ulang total keranjang berdasarkan seluruh detail yang tersimpan.
            $totalProduct = $this->cartDetailRepository->countProductBySaleReturnCartId(
                saleReturnCartId: $cart->id,
            );

            $totalQuantity = $this->cartDetailRepository->sumQuantityBySaleReturnCartId(
                saleReturnCartId: $cart->id,
            );

            $totalAmount = $this->cartDetailRepository->sumAmountBySaleReturnCartId(
                saleReturnCartId: $cart->id,
            );

            // Simpan ulang total pada keranjang; diskon tetap mengikuti diskon penjualan asli.
            $this->cartRepository->update(
                id: $cart->id,
                value: new SaleReturnCartDTO(
                    userId: $cart->user->id,
                    saleId: $cart->saleId,
                    transactionDate: $cart->transactionDate,
                    totalProduct: $totalProduct,
                    totalQuantity: $totalQuantity,
                    totalAmount: $totalAmount,
                    discount: $cart->discount,
                    grandTotal: $totalAmount - (($totalAmount * $cart->discount) / 100),
                    notes: $cart->notes,
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
