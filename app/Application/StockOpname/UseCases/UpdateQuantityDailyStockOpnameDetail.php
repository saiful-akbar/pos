<?php

declare(strict_types=1);

namespace App\Application\StockOpname\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\StockOpname\DTOs\DailyStockOpnameDetailDTO;
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
    public function execute(string $dailyStockOpnameId, string $dailyStockOpnameDetailId, int|float $quantity): void
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            // Ambil dan validasi data daily_stock_opnames.
            $dailyStock = $this->dailyStockRepository->findById($dailyStockOpnameId);

            if ($dailyStock === null) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stok opname tidak ditemukan.',
                ]);
            }

            // Ambil dan validasai data daily_stock_opname_details.
            $dailyStockDetail = $this->dailyStockDetailRepository->findById($dailyStockOpnameDetailId);

            if ($dailyStockDetail === null) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stok pada produk ini tidak ditemukan.',
                ]);
            }

            // Update quantity daily stock detail
            $this->dailyStockDetailRepository->update(
                id: $dailyStockDetail->id,
                value: new DailyStockOpnameDetailDTO(
                    dailyStockOpnameId: $dailyStockDetail->dailyStockOpnameId,
                    productId: $dailyStockDetail->product->id,
                    quantity: $quantity,
                )
            );

            // Hitung total produk, stok system dan stock fisik setelah quantity dirubah.
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
                logName: "Memperbarui quantity daily stock opname detail pada id: {$dailyStock->id}",
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
