<?php

declare(strict_types=1);

namespace App\Application\StockOpname\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\StockOpname\Entities\DailyStockOpnameEntity;
use App\Domain\StockOpname\Repositories\DailyStockOpnameDetailRepositoryInterface;
use App\Domain\StockOpname\Repositories\DailyStockOpnameRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SetActiveStock
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private DailyStockOpnameRepositoryInterface $dailyStockRepository,
        private DailyStockOpnameDetailRepositoryInterface $dailyStockDetailRepository,
        private ProductRepositoryInterface $productRepository,
        private ActivityLogRepositoryInterface $activityRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method
     */
    public function execute(string $dailyStockId): void
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $stock = $this->findStock($dailyStockId);
            $details = $this->dailyStockDetailRepository->getByHeaderId($stock->id);

            foreach ($details as $detail) {
                $this->productRepository->updateCurrentStockById(
                    id: $detail->product->id,
                    stock: $detail->physicalStock,
                );
            }

            $this->activityRepository->create(new CreateActivityDTO(
                userId: user()->id,
                logName: 'Menjadikan daily stock opname sebagai stok aktif',
                event: EventActivityEnum::UPDATED,
                attributeChanges: new AttributeActivityDTO(
                    attributes: $stock->toArray(),
                )
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
     * Mengambil data daily_stock_opnames berdasarkan id.
     */
    private function findStock(string $id): DailyStockOpnameEntity
    {
        $result = $this->dailyStockRepository->findById($id);

        if ($result === null) {
            throw ValidationException::withMessages([
                'message' => 'Stock opname tidak ditemukan.',
            ]);
        }

        return $result;
    }
}
