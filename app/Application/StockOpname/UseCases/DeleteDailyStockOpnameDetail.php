<?php

declare(strict_types=1);

namespace App\Application\StockOpname\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\StockOpname\DTOs\DailyStockOpnameDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\StockOpname\Repositories\DailyStockOpnameDetailRepositoryInterface;
use App\Domain\StockOpname\Repositories\DailyStockOpnameRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeleteDailyStockOpnameDetail
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private DailyStockOpnameRepositoryInterface $dailyStockRepository,
        private DailyStockOpnameDetailRepositoryInterface $dailyStockDetailRepository,
        private ActivityLogRepositoryInterface $activityRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(string $headerId, string $detailId): void
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $dailyStockDetail = $this->dailyStockDetailRepository->findById($detailId);

            if ($dailyStockDetail === null) {
                throw ValidationException::withMessages(messages: [
                    'message' => 'Stok todak ditemukan.',
                ]);
            }

            // Ambil data stock opname.
            $dailyStock = $this->dailyStockRepository->findById($headerId);

            if ($dailyStock === null) {
                throw ValidationException::withMessages([
                    'message' => 'Stok opname tidak ditemukan.',
                ]);
            }

            // Hapus stock opname detail.
            $this->dailyStockDetailRepository->deleteById($detailId);

            // Hitung total produk, stok system dan stock fisik setelah quantity dirubah.
            $totalProduct = $this->dailyStockDetailRepository->countProductByHeader($headerId);
            $totalSystemStock = $this->dailyStockDetailRepository->sumSystemStockByHeader($headerId);
            $totalPhysicalStock = $this->dailyStockDetailRepository->sumPhysicalStockByHeader($headerId);

            // Update total produk, total stok system, total stok fisik dan total difference
            $this->dailyStockRepository->update(
                id: $headerId,
                value: new DailyStockOpnameDTO(
                    userId: user()->id,
                    date: $dailyStock->date,
                    totalProduct: $totalProduct,
                    totalSystemStock: $totalSystemStock,
                    totalPhysicalStock: $totalPhysicalStock,
                    totalDifference: $totalPhysicalStock - $totalSystemStock,
                ),
            );

            // Buat catatan aktifitas.
            $this->activityRepository->create(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: 'Menghapus daily stock opname detail',
                event: EventActivityEnum::DELETED,
                attributeChanges: new AttributeActivityDTO(
                    old: $dailyStockDetail->toArray(),
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
}
