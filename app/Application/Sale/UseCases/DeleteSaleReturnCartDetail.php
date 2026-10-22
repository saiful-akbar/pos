<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Application\Sale\DTOs\SaleReturnCartDTO;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Sale\Repositories\SaleReturnCartDetailRepositoryInterface;
use App\Domain\Sale\Repositories\SaleReturnCartRepositoryInterface;
use Illuminate\Support\Facades\DB;

class DeleteSaleReturnCartDetail
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleReturnCartRepositoryInterface $cartRepository,
        private SaleReturnCartDetailRepositoryInterface $cartDetailRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(string $cartId, string $cartDetailId): void
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

            // Ambil detail keranjang; harus ada dan milik keranjang yang sedang diproses.
            $cartDetail = $this->cartDetailRepository->findById(
                id: $cartDetailId,
            );

            if ($cartDetail === null || $cartDetail->saleReturnCartId !== $cart->id) {
                abort(404, 'Produk pada keranjang pengembalian tidak ditemukan.');
            }

            // Hapus permanen detail keranjang.
            $this->cartDetailRepository->deleteById(
                id: $cartDetail->id,
            );

            // Hitung ulang total keranjang berdasarkan detail yang tersisa.
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
