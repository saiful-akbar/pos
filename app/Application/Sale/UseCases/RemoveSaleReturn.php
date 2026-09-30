<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Sale\Entities\SaleReturnDetailEntity;
use App\Domain\Sale\Repositories\SaleReturnDetailRepositoryInterface;
use App\Domain\Sale\Repositories\SaleReturnRepositoryInterface;
use App\Repositories\ActivityLogRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class RemoveSaleReturn
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleReturnRepositoryInterface $returnRepository,
        private SaleReturnDetailRepositoryInterface $returnDetailRepository,
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
            $return = $this->returnRepository->findById(
                id: $id,
            );

            if ($return === null) {
                throw ValidationException::withMessages([
                    'message' => 'Transaksi pengembalian penjualan tidak ditemukan.',
                ]);
            }

            $this->updateCurrentStockProduct(
                details: $this->returnDetailRepository->getBySaleReturnId(
                    saleReturnId: $return->id,
                ),
            );

            $this->returnRepository->removeById(
                id: $return->id,
            );

            $this->returnDetailRepository->removeBySaleReturnId(
                saleReturnId: $return->id,
            );

            $this->activityRepository->create(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: 'Menghapus pengembalian penjualan',
                event: EventActivityEnum::REMOVED,
                attributeChanges: new AttributeActivityDTO(
                    old: $return->toArray(),
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
    private function updateCurrentStockProduct(array $details): void
    {
        foreach ($details as $detail) {
            if ($detail instanceof SaleReturnDetailEntity) {
                $this->productRepository->updateCurrentStockById(
                    id: $detail->product->id,
                    stock: $detail->product->currentStock - $detail->quantity,
                );

                continue;
            }

            throw new InvalidArgumentException(
                sprintf('Argumen details harus merupakan instansiasi dari %s', SaleReturnDetailEntity::class),
            );
        }
    }
}
