<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Sale\Entities\SaleDetailEntity;
use App\Domain\Sale\Entities\SaleEntity;
use App\Domain\Sale\Repositories\SaleDetailRepositoryInterface;
use App\Domain\Sale\Repositories\SaleRepositoryInterface;
use App\Repositories\ActivityLogRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class RemoveSale
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleRepositoryInterface $saleRepository,
        private SaleDetailRepositoryInterface $saleDetailRepository,
        private ProductRepositoryInterface $productRepository,
        private ActivityLogRepository $activityRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(string $id): void
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $sale = $this->saleRepository->findById(
                id: $id,
                responseType: SaleEntity::class,
            );

            if ($sale === null) {
                throw ValidationException::withMessages([
                    'message' => 'Transaksi penjualan tidak ditemukan.',
                ]);
            }

            $this->updateCurrentStockProduct(
                saleDetails: $this->saleDetailRepository->getBySaleId(
                    saleId: $sale->id,
                ),
            );

            $this->saleRepository->removeById(
                id: $sale->id,
            );

            $this->saleDetailRepository->removeBySaleId(
                saleId: $sale->id,
            );

            $this->activityRepository->create(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: 'Menghapus penjualan',
                event: EventActivityEnum::REMOVED,
                attributeChanges: new AttributeActivityDTO(
                    old: $sale->toArray(),
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
     * Memperbarui data stok produk.
     */
    private function updateCurrentStockProduct(array $saleDetails): void
    {
        foreach ($saleDetails as $saleDetail) {
            if ($saleDetail instanceof SaleDetailEntity) {
                $this->productRepository->updateCurrentStockById(
                    id: $saleDetail->product->id,
                    stock: $saleDetail->product->currentStock + $saleDetail->quantity,
                );

                continue;
            }

            throw new InvalidArgumentException(
                sprintf('Argumen saleDetails harus merupakan instansiasi dari %s', SaleDetailEntity::class),
            );
        }
    }
}
