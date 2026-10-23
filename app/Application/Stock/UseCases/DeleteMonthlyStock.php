<?php

declare(strict_types=1);

namespace App\Application\Stock\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Stock\Repositories\MonthlyStockRepositoryInterface;
use Illuminate\Support\Facades\DB;

class DeleteMonthlyStock
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private MonthlyStockRepositoryInterface $monthlyStockRepository,
        private ActivityLogRepositoryInterface $activityRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Menghapus permanen stok bulanan beserta detailnya.
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

            // 2. Hapus permanen; detail stok bulanan ikut terhapus oleh relasi cascade.
            $this->monthlyStockRepository->delete(
                id: $monthlyStock->id,
            );

            // 3. Buat catatan aktifitas.
            $this->activityRepository->create(
                dto: new CreateActivityDTO(
                    userId: user()->id,
                    logName: 'Menghapus permanen data stok bulanan',
                    event: EventActivityEnum::DELETED,
                    attributeChanges: new AttributeActivityDTO(
                        old: $monthlyStock->toArray(),
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
