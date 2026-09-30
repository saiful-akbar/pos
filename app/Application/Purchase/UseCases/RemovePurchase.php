<?php

declare(strict_types=1);

namespace App\Application\Purchase\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Purchase\Entities\PurchaseDetailEntity;
use App\Domain\Purchase\Repositories\PurchaseDetailRepositoryInterface;
use App\Domain\Purchase\Repositories\PurchaseRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class RemovePurchase
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private PurchaseRepositoryInterface $purchaseRepository,
        private PurchaseDetailRepositoryInterface $purchaseDetailRepository,
        private ProductRepositoryInterface $productRepository,
        private ActivityLogRepositoryInterface $activityRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method
     */
    public function execute(string $id): void
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $purchase = $this->purchaseRepository->findById(
                id: $id
            );

            if ($purchase === null) {
                throw ValidationException::withMessages([
                    'message' => 'Transaksi pembelian tidak ditemukan',
                ]);
            }

            $this->updateCurrentStockProduct(
                purchaseDetails: $this->purchaseDetailRepository->getByPurchaseId(
                    purchaseId: $purchase->id
                ),
            );

            $this->purchaseRepository->removeById(
                id: $purchase->id,
            );

            $this->purchaseDetailRepository->removeByPurchaseId(
                purchaseId: $purchase->id,
            );

            $this->activityRepository->create(new CreateActivityDTO(
                userId: user()->id,
                logName: 'Menghapus pembelian',
                event: EventActivityEnum::REMOVED,
                attributeChanges: new AttributeActivityDTO(
                    old: $purchase->toArray(),
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
     * Memperbarui data stok produk
     */
    private function updateCurrentStockProduct(array $purchaseDetails): void
    {
        foreach ($purchaseDetails as $purchaseDetail) {
            if ($purchaseDetail instanceof PurchaseDetailEntity) {
                $this->productRepository->updateCurrentStockById(
                    id: $purchaseDetail->product->id,
                    stock: $purchaseDetail->product->currentStock - $purchaseDetail->quantity,
                );

                continue;
            }

            throw new InvalidArgumentException(
                sprintf('Argumen purchaseDetails harus merupakan instansiasi dari %s', PurchaseDetailEntity::class),
            );
        }
    }
}
