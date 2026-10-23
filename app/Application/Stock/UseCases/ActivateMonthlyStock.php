<?php

declare(strict_types=1);

namespace App\Application\Stock\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Stock\Repositories\MonthlyStockDetailRepositoryInterface;
use App\Domain\Stock\Repositories\MonthlyStockRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ActivateMonthlyStock
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private MonthlyStockRepositoryInterface $monthlyStockRepository,
        private MonthlyStockDetailRepositoryInterface $monthlyStockDetailRepository,
        private ProductRepositoryInterface $productRepository,
        private ActivityLogRepositoryInterface $activityRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Menjadikan stok bulanan sebagai stok aktif (current_stock) produk.
     */
    public function execute(string $id): void
    {
        DB::beginTransaction();

        try {
            // 1. Ambil stok bulanan; error jika tidak ditemukan.
            $monthlyStock = $this->monthlyStockRepository->findById(
                id: $id,
            );

            if ($monthlyStock === null) {
                abort(404, 'Data stok bulanan tidak ditemukan.');
            }

            // 2. Ambil detail stok bulanan.
            $details = $this->monthlyStockDetailRepository->getByMonthlyStockId(
                monthlyStockId: $monthlyStock->id,
            );

            // 3. Jumlahkan stok (quantity x multiples_quantity) per produk karena satu produk bisa memiliki beberapa unit.
            $totals = [];

            foreach ($details as $detail) {
                $totals[$detail['product_id']] = ($totals[$detail['product_id']] ?? 0.0)
                    + $detail['quantity'] * $detail['multiples_quantity'];
            }

            // 4. Ganti current_stock tiap produk dengan total hasil penjumlahan (dibulatkan sesuai skala kolom decimal).
            foreach ($totals as $productId => $total) {
                $this->productRepository->updateCurrentStock(
                    id: (string) $productId,
                    value: round($total, 2),
                );
            }

            // 5. Buat catatan aktifitas.
            $this->activityRepository->create(
                dto: new CreateActivityDTO(
                    userId: user()->id,
                    logName: 'Menjadikan stok bulanan sebagai stok aktif produk',
                    event: EventActivityEnum::UPDATED,
                    attributeChanges: new AttributeActivityDTO(
                        attributes: [
                            'id' => $monthlyStock->id,
                            'year' => $monthlyStock->year,
                            'month' => $monthlyStock->month,
                            'total_product' => $monthlyStock->totalProduct,
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
