<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Application\Sale\DTOs\SaleReturnCartDetailDTO;
use App\Application\Sale\DTOs\SaleReturnCartDTO;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Sale\Repositories\SaleDetailRepositoryInterface;
use App\Domain\Sale\Repositories\SaleReturnCartDetailRepositoryInterface;
use App\Domain\Sale\Repositories\SaleReturnCartRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateSaleReturnCartDetail
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleReturnCartRepositoryInterface $cartRepository,
        private SaleReturnCartDetailRepositoryInterface $cartDetailRepository,
        private SaleDetailRepositoryInterface $saleDetailRepository,
        private ProductRepositoryInterface $productRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(string $cartId, string $productCode): void
    {
        DB::beginTransaction();

        try {
            // Ambil data keranjang pengembalian penjualan berdasarkan ID.
            $cart = $this->cartRepository->find(
                column: 'id',
                value: $cartId,
            );

            if ($cart === null) {
                abort(404, 'Keranjang pengembalian penjualan tidak ditemukan.');
            }

            // Ambil data produk berdasarkan kode produk yang dikirim.
            $product = $this->productRepository->findByCode(
                code: $productCode,
            );

            if ($product === null) {
                throw ValidationException::withMessages(messages: [
                    'product_code' => 'Produk tidak ditemukan.',
                ]);
            }

            // Ambil detail penjualan asli untuk produk ini, sebagai sumber harga/unit/qty terjual.
            $saleDetail = $this->saleDetailRepository->findBySaleIdAndProductId(
                saleId: $cart->saleId,
                productId: $product->id,
            );

            if ($saleDetail === null) {
                throw ValidationException::withMessages(messages: [
                    'product_code' => "Produk {$product->name} ({$product->code}) tidak terdapat pada transaksi penjualan ini.",
                ]);
            }

            // Periksa apakah produk yang sama sudah ada pada keranjang pengembalian ini.
            $cartDetail = $this->cartDetailRepository->findBySaleReturnCartIdAndProductId(
                saleReturnCartId: $cart->id,
                productId: $product->id,
            );

            // Jika belum ada, kuantitas default mengikuti jumlah yang terjual.
            // Jika sudah ada, tambahkan 1 ke kuantitas yang sudah tersimpan.
            $quantity = $cartDetail !== null
                ? $cartDetail->quantity + 1
                : $saleDetail->quantity;

            // Kuantitas pengembalian tidak boleh melebihi kuantitas yang terjual.
            if ($quantity > $saleDetail->quantity) {
                throw ValidationException::withMessages(messages: [
                    'product_code' => "Jumlah pengembalian produk {$product->name} ({$product->code}) tidak boleh melebihi jumlah yang terjual ({$saleDetail->quantity}).",
                ]);
            }

            if ($cartDetail === null) {
                // Belum ada detail untuk produk ini, buat baris baru.
                $this->cartDetailRepository->create(
                    value: new SaleReturnCartDetailDTO(
                        saleReturnCartId: $cart->id,
                        productId: $saleDetail->product->id,
                        unitId: $saleDetail->unit->id,
                        multiplesQuantity: $saleDetail->multiplesQuantity,
                        purchasePrice: $saleDetail->purchasePrice,
                        sellingPrice: $saleDetail->sellingPrice,
                        quantity: $quantity,
                        subtotal: $saleDetail->sellingPrice * $quantity,
                        discount: $saleDetail->discount,
                        grandTotal: ($saleDetail->sellingPrice * $quantity) - ((($saleDetail->sellingPrice * $quantity) * $saleDetail->discount) / 100),
                    ),
                );
            } else {
                // Sudah ada detail untuk produk ini, update kuantitas dan total barisnya.
                $subtotal = $cartDetail->sellingPrice * $quantity;
                $grandTotal = $subtotal - (($subtotal * $cartDetail->discount) / 100);

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
            }

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

            // Simpan ulang total pada data keranjang pengembalian penjualan.
            // Diskon tetap mengikuti nilai diskon penjualan asli yang sudah tersimpan di keranjang.
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
