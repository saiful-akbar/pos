<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Application\Sale\DTOs\SaleReturnCartDetailDTO;
use App\Application\Sale\DTOs\SaleReturnCartDTO;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Sale\Entities\SaleDetailEntity;
use App\Domain\Sale\Entities\SaleEntity;
use App\Domain\Sale\Entities\SaleReturnCartDetailEntity;
use App\Domain\Sale\Entities\SaleReturnCartEntity;
use App\Domain\Sale\Repositories\SaleDetailRepositoryInterface;
use App\Domain\Sale\Repositories\SaleRepositoryInterface;
use App\Domain\Sale\Repositories\SaleReturnCartDetailRepositoryInterface;
use App\Domain\Sale\Repositories\SaleReturnCartRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateQuantityReturnCartDetail
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleReturnCartRepositoryInterface $returnCartRepository,
        private SaleReturnCartDetailRepositoryInterface $returnCartDetailRepository,
        private SaleRepositoryInterface $saleRepository,
        private SaleDetailRepositoryInterface $saleDetailRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(string $id, int|float $quantity): void
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $returnCart = $this->findSaleReturnCartByCurrentUser();
            $returnCartDetail = $this->findSaleReturnCartDetail(id: $id);
            $sale = $this->findSale(id: $returnCart->saleId);
            $saleDetail = $this->findSaleDetail(saleId: $sale->id, productId: $returnCartDetail->product->id);

            // validasi quantity
            if ($quantity > $saleDetail->quantity) {
                throw ValidationException::withMessages(messages: [
                    'quantity' => 'Jumlah quantity tidak boleh melebihi penjualan.',
                ]);
            }

            $this->returnCartDetailRepository->update(
                id: $returnCartDetail->id,
                dto: new SaleReturnCartDetailDTO(
                    saleReturnCartId: $returnCartDetail->saleReturnCartId,
                    productId: $returnCartDetail->product->id,
                    quantity: $quantity,
                    purchasePrice: $returnCartDetail->purchasePrice,
                    sellingPrice: $returnCartDetail->sellingPrice,
                    discount: $returnCartDetail->discount,
                    subtotal: $quantity * $returnCartDetail->sellingPrice,
                ),
            );

            $totalQuantity = $this->returnCartDetailRepository->sumQuantityByCartId(cartId: $returnCart->id);
            $totalAmount = $this->returnCartDetailRepository->sumSubtotalByCartId(cartId: $returnCart->id);

            $this->returnCartRepository->update(
                id: $returnCart->id,
                dto: new SaleReturnCartDTO(
                    saleId: $returnCart->saleId,
                    userId: $returnCart->user->id,
                    transactionDate: $returnCart->transactionDate,
                    totalQuantity: $totalQuantity,
                    totalAmount: $totalAmount,
                    notes: $returnCart->notes
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

    /**
     * Mengambil data keranjang pengembalian penjualan
     * berdasarkan user yang sedang login saat ini.
     */
    private function findSaleReturnCartByCurrentUser(): SaleReturnCartEntity
    {
        $result = $this->returnCartRepository->findByUserId(
            userId: user()->id,
        );

        if ($result === null) {
            throw ValidationException::withMessages(messages: [
                'quantity' => 'Transaksi pengembalian tidak ditemukan.',
            ]);
        }

        return $result;
    }

    /**
     * Mengambil data detail (produk) keranjang pengembalian
     * penjualan berdasarkan id.
     */
    private function findSaleReturnCartDetail(string $id): SaleReturnCartDetailEntity
    {
        $result = $this->returnCartDetailRepository->findById(
            id: $id,
        );

        if ($result === null) {
            throw ValidationException::withMessages(messages: [
                'quantity' => 'Produk tidak ditemukan dalam daftar.',
            ]);
        }

        return $result;
    }

    /**
     * Mengambil data penjualan berdasarkan id.
     */
    private function findSale(string $id): SaleEntity
    {
        $result = $this->saleRepository->findById(
            id: $id,
            responseType: SaleEntity::class,
        );

        if ($result === null) {
            throw ValidationException::withMessages(messages: [
                'quantity' => 'Transaksi tidak ditemukan.',
            ]);
        }

        return $result;
    }

    /**
     * Mengambil data detail (product) penjualan.
     */
    private function findSaleDetail(string $saleId, string $productId): SaleDetailEntity
    {
        $result = $this->saleDetailRepository->findBySaleIdAndProductId(
            saleId: $saleId,
            productId: $productId,
        );

        if ($result === null) {
            throw ValidationException::withMessages(messages: [
                'quantity' => 'Produk tidak ditemukan pada transaksi ini.',
            ]);
        }

        return $result;
    }
}
