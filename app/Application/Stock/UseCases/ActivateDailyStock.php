<?php

declare(strict_types=1);

namespace App\Application\Stock\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Stock\Repositories\DailyStockRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ActivateDailyStock
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private DailyStockRepositoryInterface $dailyStockRepository,
        private ProductRepositoryInterface $productRepository,
        private ActivityLogRepositoryInterface $activityRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Menjadikan stok harian sebagai stok aktif (current_stock) produk.
     */
    public function execute(string $id): void
    {
        DB::beginTransaction();

        try {
            // Ambil stok harian beserta daftar detailnya.
            $dailyStock = $this->dailyStockRepository->findById(
                id: $id,
            );

            if ($dailyStock === null) {
                abort(404, 'Data stok harian tidak ditemukan.');
            }

            // Ganti current_stock tiap produk dengan quantity x multiples_quantity dari detail.
            foreach ($dailyStock->details as $detail) {
                $this->productRepository->updateCurrentStock(
                    id: $detail->product->id,
                    value: $detail->quantity * $detail->multiplesQuantity,
                );
            }

            // Buat catatan aktifitas.
            $this->activityRepository->create(
                dto: new CreateActivityDTO(
                    userId: user()->id,
                    logName: 'Menjadikan stok harian sebagai stok aktif produk',
                    event: EventActivityEnum::UPDATED,
                    attributeChanges: new AttributeActivityDTO(
                        attributes: [
                            'id' => $dailyStock->id,
                            'date' => $dailyStock->date->format('Y-m-d'),
                            'total_product' => $dailyStock->totalProduct,
                        ],
                    ),
                ),
            );

            DB::commit();
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
