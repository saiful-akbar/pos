<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Application\Sale\DTOs\SaleReturnCartDTO;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Sale\Entities\SaleReturnCartDetailEntity;
use App\Domain\Sale\Entities\SaleReturnCartEntity;
use App\Domain\Sale\Repositories\SaleReturnCartDetailRepositoryInterface;
use App\Domain\Sale\Repositories\SaleReturnCartRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeleteSaleReturnCartDetailById
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleReturnCartDetailRepositoryInterface $returnCartDetailRepository,
        private SaleReturnCartRepositoryInterface $returnCartRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method
     */
    public function execute(string $id): void
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $returnCart = $this->findReturnCart();
            $returnCartDetail = $this->findReturnCartDetail(id: $id);

            $this->returnCartDetailRepository->deleteById(
                id: $returnCartDetail->id,
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
     * Mengambil data detail (produk) kerangjang pengembalian penjualan
     * berdasarkan id.
     */
    private function findReturnCartDetail(string $id): SaleReturnCartDetailEntity
    {
        $result = $this->returnCartDetailRepository->findById(
            id: $id,
        );

        if ($result === null) {
            throw ValidationException::withMessages(messages: [
                'message' => 'Produk tidak ditemukan.',
            ]);
        }

        return $result;
    }

    /**
     * Mengambil data keranjang pengembalian penjualan
     * berdasarkan user yang sedang login saat ini.
     */
    private function findReturnCart(): SaleReturnCartEntity
    {
        $result = $this->returnCartRepository->findByUserId(
            userId: user()->id,
        );

        if ($result === null) {
            throw ValidationException::withMessages(messages: [
                'message' => 'Transaksi tidak ditemukan.',
            ]);
        }

        return $result;
    }
}
