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

class UpdateQuantityDailyStockOpnameDetail
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
    public function execute(string $headerId, string $detailId, int|float $quantity): void
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            // ambil dan validasi data daily stock
            $dailyStock = $this->dailyStockRepository->findById($headerId);

            if ($dailyStock === null) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stok opname tidak ditemukan.',
                ]);
            }

            // Ambil dan validasai data daily stock detail
            $dailyStockDetail = $this->dailyStockDetailRepository->findById($detailId);

            if ($dailyStockDetail === null) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stok pada produk ini tidak ditemukan.',
                ]);
            }

            // Update quantity daily stock detail
            $this->dailyStockDetailRepository->updateQuantity(
                id: $detailId,
                quantity: $quantity,
            );

            // Hitung total produk dan quantity setelah diupdate.
            $totalProduct = $this->dailyStockDetailRepository->countProductByHeader($headerId);
            $totalQuantity = $this->dailyStockDetailRepository->sumQuantityByHeader($headerId);

            // Update total produk dan quantity pada daily stock.
            $this->dailyStockRepository->update(
                id: $headerId,
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
                logName: "Memperbarui quantity daily stock opname detail pada id: {$detailId}",
                event: EventActivityEnum::UPDATED,
                attributeChanges: new AttributeActivityDTO(
                    old: [
                        'id' => $dailyStockDetail->id,
                        'quantity' => $dailyStockDetail->quantity,
                    ],
                    attributes: [
                        'id' => $dailyStockDetail->id,
                        'quantity' => $quantity,
                    ],
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
