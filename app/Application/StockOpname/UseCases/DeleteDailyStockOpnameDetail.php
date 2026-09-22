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

            // Hitung total produk dan quantity setelah dihapus.
            $totalProduk = $this->dailyStockDetailRepository->countProductByHeader($dailyStockDetail->dailyStockOpnameId);
            $totalQuantity = $this->dailyStockDetailRepository->sumQuantityByHeader($dailyStockDetail->dailyStockOpnameId);

            // Update total produk dan quantity pada daily stock opname.
            $this->dailyStockRepository->update(
                id: $dailyStockDetail->dailyStockOpnameId,
                value: new DailyStockOpnameDTO(
                    userId: $dailyStock->user->id,
                    date: $dailyStock->date,
                    totalProduct: $totalProduk,
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
