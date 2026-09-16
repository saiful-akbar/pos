<?php

namespace App\Application\Sale\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\Sale\DTOs\SaleReturnDetailDTO;
use App\Application\Sale\DTOs\SaleReturnDTO;
use App\Application\StockOpname\DTOs\CreateStockMovementDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Sale\Entities\SaleReturnCartEntity;
use App\Domain\Sale\Repositories\SaleReturnCartDetailRepositoryInterface;
use App\Domain\Sale\Repositories\SaleReturnCartRepositoryInterface;
use App\Domain\Sale\Repositories\SaleReturnDetailRepositoryInterface;
use App\Domain\Sale\Repositories\SaleReturnRepositoryInterface;
use App\Domain\StockOpname\Enums\TypeStockMovementEnum;
use App\Domain\StockOpname\Repositories\StockMovementRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateSaleReturn
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleReturnCartRepositoryInterface $returnCartRepository,
        private SaleReturnCartDetailRepositoryInterface $returnCartDetailRepository,
        private SaleReturnRepositoryInterface $returnRepository,
        private SaleReturnDetailRepositoryInterface $returnDetailRepository,
        private ProductRepositoryInterface $productRepository,
        private StockMovementRepositoryInterface $stockMovementRepository,
        private ActivityLogRepositoryInterface $activityRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method.
     *
     * @param string $notes
     * @return void
     */
    public function execute(?string $notes): void
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $returnCart = $this->findSaleReturnCart();
            $returnCartDetails = $this->returnCartDetailRepository->getByCartId(cartId: $returnCart->id);

            // Insert data sale return
            $return = $this->returnRepository->create(dto: new SaleReturnDTO(
                saleId: $returnCart->saleId,
                userId: user()->id,
                transactionDate: $returnCart->transactionDate,
                totalQuantity: $returnCart->totalQuantity,
                totalAmount: $returnCart->totalAmount,
                notes: $notes,
            ));

            // Insert data sale return detail
            foreach ($returnCartDetails as $returnCartDetail) {
                $returnDetail = $this->returnDetailRepository->create(dto: new SaleReturnDetailDTO(
                    saleReturnId: $return->id,
                    productId: $returnCartDetail->product->id,
                    quantity: $returnCartDetail->quantity,
                    purchasePrice: $returnCartDetail->purchasePrice,
                    sellingPrice: $returnCartDetail->sellingPrice,
                    discount: $returnCartDetail->discount,
                    subtotal: $returnCartDetail->subtotal,
                ));

                $currentStock = $returnDetail->product->currentStock + $returnDetail->quantity;

                // Buat stock movement
                $this->stockMovementRepository->create(dto: new CreateStockMovementDTO(
                    userId: user()->id,
                    productId: $returnDetail->product->id,
                    type: TypeStockMovementEnum::SALE_RETURN,
                    quantity: $returnDetail->quantity,
                    stockBefore: $returnDetail->product->currentStock,
                    stockAfter: $currentStock,
                    referenceId: $returnDetail->id,
                    notes: 'Penambahan stok dari hasil pengembalian penjualan',
                ));

                // Update quantity product
                $this->productRepository->updateCurrentStockById(
                    id: $returnDetail->product->id,
                    stock: $currentStock,
                );
            }

            // Hapus return cart
            $this->returnCartRepository->deleteById(
                id: $returnCart->id,
            );

            // Buat catatan aktifitas.
            $this->activityRepository->create(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: 'Menyimpan transaksi pengembalian penjualan',
                event: EventActivityEnum::CREATED,
                attributeChanges: new AttributeActivityDTO(
                    attributes: $return->toArray(),
                ),
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

    /**
     * Mengambil data pengembalian penjualan berdasarkan
     * user yang sedang login (aktif) saat ini.
     *
     * @return SaleReturnCartEntity
     */
    private function findSaleReturnCart(): SaleReturnCartEntity
    {
        $result = $this->returnCartRepository->findByUserId(
            userId: user()->id,
        );

        if ($result === null) {
            throw ValidationException::withMessages(messages: [
                'message' => 'Transaksi pengembalian tidak ditemukan.'
            ]);
        }

        return $result;
    }
}
