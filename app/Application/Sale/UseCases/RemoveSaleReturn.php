<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Sale\Repositories\SaleReturnDetailRepositoryInterface;
use App\Domain\Sale\Repositories\SaleReturnRepositoryInterface;
use Illuminate\Support\Facades\DB;

class RemoveSaleReturn
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleReturnRepositoryInterface $saleReturnRepository,
        private SaleReturnDetailRepositoryInterface $saleReturnDetailRepository,
        private ActivityLogRepositoryInterface $activityRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Menghapus (soft delete) pengembalian beserta detailnya. Mengembalikan jumlah baris sale_returns yang dihapus.
     */
    public function execute(string $id): int
    {
        DB::beginTransaction();

        try {
            // Soft delete seluruh detail pengembalian.
            $this->saleReturnDetailRepository->removeBySaleReturnId(
                saleReturnId: $id,
            );

            // Soft delete data utama pengembalian.
            $removed = $this->saleReturnRepository->removeById(
                id: $id,
            );

            // Data tidak ditemukan: batalkan, tidak ada yang perlu dicatat.
            if ($removed === 0) {
                DB::rollBack();

                return 0;
            }

            // Buat catatan aktifitas.
            $this->activityRepository->create(
                dto: new CreateActivityDTO(
                    userId: user()->id,
                    logName: 'Menghapus transaksi pengembalian penjualan',
                    event: EventActivityEnum::REMOVED,
                    attributeChanges: new AttributeActivityDTO(
                        old: ['id' => $id],
                    ),
                ),
            );

            DB::commit();

            return $removed;
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
