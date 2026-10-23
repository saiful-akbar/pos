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

            // Jumlahkan stok (quantity x multiples_quantity) per produk karena satu produk bisa memiliki beberapa unit.
            $totals = [];

            foreach ($dailyStock->details as $detail) {
                $totals[$detail->product->id] = ($totals[$detail->product->id] ?? 0.0)
                    + $detail->quantity * $detail->multiplesQuantity;
            }

            // Ganti current_stock tiap produk dengan total hasil penjumlahan (dibulatkan sesuai skala kolom decimal).
            foreach ($totals as $productId => $total) {
                $this->productRepository->updateCurrentStock(
                    id: (string) $productId,
                    value: round($total, 2),
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
