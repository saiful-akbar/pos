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
    public function execute(string $dailyStockOpnameId, string $dailyStockOpnameDetailId): void
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $dailyStockDetail = $this->dailyStockDetailRepository->findById($dailyStockOpnameDetailId);

            if ($dailyStockDetail === null) {
                throw ValidationException::withMessages(messages: [
                    'message' => 'Stok todak ditemukan.',
                ]);
            }

            // Ambil data stock opname.
            $dailyStock = $this->dailyStockRepository->findById($dailyStockOpnameId);

            if ($dailyStock === null) {
                throw ValidationException::withMessages([
                    'message' => 'Stok opname tidak ditemukan.',
                ]);
            }

            // Hapus stock opname detail.
            $this->dailyStockDetailRepository->deleteById($dailyStockDetail->id);

            // Hitung total produk dan quantity setelah dirubah.
            $totalProduct = $this->dailyStockDetailRepository->countProductByDailyStockOpnameId($dailyStock->id);
            $totalQuantity = $this->dailyStockDetailRepository->sumQuantityByDailyStockOpnameId($dailyStock->id);

            // Update total produk, total stok system, total stok fisik dan total difference
            $this->dailyStockRepository->update(
                id: $dailyStock->id,
                value: new DailyStockOpnameDTO(
                    userId: user()->id,
                    date: $dailyStock->date,
                    totalProduct: $totalProduct,
                    totalQuantity: $totalQuantity,
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
