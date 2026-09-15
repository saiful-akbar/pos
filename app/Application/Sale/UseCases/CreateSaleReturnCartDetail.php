<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Application\Sale\DTOs\SaleReturnCartDetailDTO;
use App\Application\Sale\DTOs\SaleReturnCartDTO;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Product\Entities\ProductEntity;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Sale\Entities\SaleDetailEntity;
use App\Domain\Sale\Entities\SaleReturnCartEntity;
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
        private SaleReturnCartDetailRepositoryInterface $returnCartDetailRepository,
        private SaleReturnCartRepositoryInterface $returnCartRepository,
        private SaleDetailRepositoryInterface $saleDetailRepository,
        private ProductRepositoryInterface $productRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method
     */
    public function execute(string $productCode): void
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $returnCart = $this->findSaleReturnCart();
            $product = $this->findProduct(value: $productCode);
            $saleDetail = $this->findSaleDetail(saleId: $returnCart->saleId, productId: $product->id);
            $returnCartDetail = $this->returnCartDetailRepository->findByCartIdAndProductId(cartId: $returnCart->id, productId: $product->id);

            if ($returnCartDetail) {
                throw ValidationException::withMessages(messages: [
                    'product_code' => 'Produk sudah ada dalam daftar.'
                ]);
            }

            $this->returnCartDetailRepository->create(dto: new SaleReturnCartDetailDTO(
                saleReturnCartId: $returnCart->id,
                productId: $product->id,
                quantity: $saleDetail->quantity,
                purchasePrice: $saleDetail->purchasePrice,
                sellingPrice: $saleDetail->sellingPrice,
                discount: $saleDetail->discount,
                subtotal: $saleDetail->subtotal,
            ));

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
     *
     * @return SaleReturnCartEntity
     */
    private function findSaleReturnCart(): SaleReturnCartEntity
    {
        $result = $this->returnCartRepository->findByUserId(
            userId: user()->id,
        );

        if (is_null($result)) {
            throw ValidationException::withMessages(messages: [
                'message' => 'Transaksi tidak ditemukan.'
            ]);
        }

        return $result;
    }

    /**
     * Mengambil data produk berdasarkan code atau barcode.
     *
     * @param string $value
     * @return ProductEntity
     */
    private function findProduct(string $value): ProductEntity
    {
        $result = $this->productRepository->findByCodeOrBarcode(
            value: $value,
        );

        if (is_null($result)) {
            throw ValidationException::withMessages(messages: [
                'message' => 'Produk tidak ditemukan.'
            ]);
        }

        return $result;
    }

    /**
     * Mengambil data detail sales berdasarkan id sale dsn id product.
     *
     * @param string $saleId
     * @param string $productId
     * @return SaleDetailEntity
     */
    private function findSaleDetail(string $saleId, string $productId): SaleDetailEntity
    {
        $result = $this->saleDetailRepository->findBySaleIdAndProductId(
            saleId: $saleId,
            productId: $productId,
        );

        if (is_null($result)) {
            throw ValidationException::withMessages(messages: [
                'message' => 'Produk pada transaksi ini tidak ditemukan.'
            ]);
        }

        return $result;
    }
}
