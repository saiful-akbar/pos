<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\Sale\DTOs\SaleReturnDetailDTO;
use App\Application\Sale\DTOs\SaleReturnDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Sale\Entities\SaleReturnEntity;
use App\Domain\Sale\Repositories\SaleReturnCartDetailRepositoryInterface;
use App\Domain\Sale\Repositories\SaleReturnCartRepositoryInterface;
use App\Domain\Sale\Repositories\SaleReturnDetailRepositoryInterface;
use App\Domain\Sale\Repositories\SaleReturnRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateSaleReturn
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleReturnRepositoryInterface $saleReturnRepository,
        private SaleReturnDetailRepositoryInterface $saleReturnDetailRepository,
        private SaleReturnCartRepositoryInterface $cartRepository,
        private SaleReturnCartDetailRepositoryInterface $cartDetailRepository,
        private ProductRepositoryInterface $productRepository,
        private ActivityLogRepositoryInterface $activityRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(string $cartId, ?string $notes): SaleReturnEntity
    {
        DB::beginTransaction();

        try {
            // Ambil data keranjang pengembalian berdasarkan ID.
            $cart = $this->cartRepository->findById(
                id: $cartId,
            );

            // Keranjang harus ada dan milik user yang sedang login.
            if ($cart === null || $cart->user->id !== user()->id) {
                abort(404, 'Keranjang pengembalian penjualan tidak ditemukan.');
            }

            // Ambil daftar detail keranjang pengembalian.
            $cartDetails = $this->cartDetailRepository->getBySaleReturnCartId(
                saleReturnCartId: $cart->id,
            );

            // Transaksi tidak boleh diselesaikan tanpa produk.
            if ($cartDetails === []) {
                throw ValidationException::withMessages(messages: [
                    'notes' => 'Belum ada produk pada transaksi pengembalian.',
                ]);
            }

            // Insert data pengembalian dari data keranjang.
            $saleReturn = $this->saleReturnRepository->create(
                value: new SaleReturnDTO(
                    userId: $cart->user->id,
                    saleId: $cart->saleId,
                    returnNumber: $this->saleReturnRepository->generateReturnNumber(
                        transactionDate: $cart->transactionDate,
                    ),
                    transactionDate: $cart->transactionDate,
                    totalProduct: $cart->totalProduct,
                    totalQuantity: $cart->totalQuantity,
                    totalAmount: $cart->totalAmount,
                    discount: $cart->discount,
                    grandTotal: $cart->grandTotal,
                    notes: $notes,
                ),
            );

            foreach ($cartDetails as $cartDetail) {
                // Insert detail pengembalian dari detail keranjang.
                $this->saleReturnDetailRepository->create(
                    value: new SaleReturnDetailDTO(
                        saleReturnId: $saleReturn->id,
                        productId: $cartDetail->product->id,
                        unitId: $cartDetail->unit->id,
                        multiplesQuantity: $cartDetail->multiplesQuantity,
                        purchasePrice: $cartDetail->purchasePrice,
                        sellingPrice: $cartDetail->sellingPrice,
                        quantity: $cartDetail->quantity,
                        subtotal: $cartDetail->subtotal,
                        discount: $cartDetail->discount,
                        grandTotal: $cartDetail->grandTotal,
                    ),
                );

                // Ambil stok produk terbaru (bukan snapshot dari keranjang).
                $product = $this->productRepository->findById(
                    id: $cartDetail->product->id,
                );

                if ($product === null) {
                    abort(404, "Produk {$cartDetail->product->name} tidak ditemukan.");
                }

                // Update current_stock: barang yang dikembalikan masuk kembali ke stok
                // sebesar quantity pengembalian x multiples_quantity unit.
                $this->productRepository->updateCurrentStock(
                    id: $product->id,
                    value: $product->currentStock + ($cartDetail->quantity * $cartDetail->multiplesQuantity),
                );
            }

            // Hapus permanen detail keranjang lalu keranjangnya.
            $this->cartDetailRepository->deleteBySaleReturnCartId(
                saleReturnCartId: $cart->id,
            );

            $this->cartRepository->deleteById(
                id: $cart->id,
            );

            // Buat catatan aktifitas.
            $this->activityRepository->create(
                dto: new CreateActivityDTO(
                    userId: user()->id,
                    logName: 'Menyimpan transaksi pengembalian penjualan',
                    event: EventActivityEnum::CREATED,
                    attributeChanges: new AttributeActivityDTO(
                        attributes: $saleReturn->toArray(),
                    ),
                ),
            );

            DB::commit();

            return $saleReturn;
        } catch (\Throwable $e) {
            DB::rollBack();

            // Simpan pesan error.
            $this->errorRepository->create(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }
}
