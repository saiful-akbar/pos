<?php

declare(strict_types=1);

namespace App\Application\StockOpname\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\StockOpname\DTOs\MonthlyStockOpnameDetailDTO;
use App\Application\StockOpname\DTOs\MonthlyStockOpnameDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\StockOpname\Entities\DailyStockOpnameEntity;
use App\Domain\StockOpname\Entities\MonthlyStockOpnameEntity;
use App\Domain\StockOpname\Repositories\DailyStockOpnameDetailRepositoryInterface;
use App\Domain\StockOpname\Repositories\DailyStockOpnameRepositoryInterface;
use App\Domain\StockOpname\Repositories\MonthlyStockOpnameDetailRepositoryInterface;
use App\Domain\StockOpname\Repositories\MonthlyStockOpnameRepositoryInterface;
use DateTimeInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateMonthlyStock
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private MonthlyStockOpnameRepositoryInterface $monthlyStockRepository,
        private MonthlyStockOpnameDetailRepositoryInterface $monthlyStockDetailRepository,
        private DailyStockOpnameRepositoryInterface $dailyStockRepository,
        private DailyStockOpnameDetailRepositoryInterface $dailyStockDetailRepository,
        private ActivityLogRepositoryInterface $activityRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method
     */
    public function execute(string $id, DateTimeInterface $dailyStockDate, ?string $notes): void
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $monthlyStock = $this->findMonthlyStock($id);
            $dailyStock = $this->findDailyStock($dailyStockDate);
            $dailyStockDetails = $this->dailyStockDetailRepository->getByHeaderId($dailyStock->id);

            // Update monthly stock
            $this->monthlyStockRepository->update(
                id: $monthlyStock->id,
                value: new MonthlyStockOpnameDTO(
                    dailyStockOpnameId: $dailyStock->id,
                    userId: user()->id,
                    year: $monthlyStock->year,
                    month: $monthlyStock->month,
                    totalProduct: $dailyStock->totalProduct,
                    totalQuantity: $dailyStock->totalQuantity,
                    notes: $notes ?? $monthlyStock->notes,
                ),
            );

            // Hapus monthly stock detail
            $this->monthlyStockDetailRepository->deleteByMonthlyStockOpnameId($monthlyStock->id);

            // Insert data baru monthly stock detail.
            foreach ($dailyStockDetails as $dailyStockDetail) {
                $this->monthlyStockDetailRepository->create(new MonthlyStockOpnameDetailDTO(
                    monthlyStockOpnameId: $monthlyStock->id,
                    productId: $dailyStockDetail->product->id,
                    quantity: $dailyStockDetail->quantity,
                ));
            }

            // Buat catatan aktifitas.
            $this->activityRepository->create(new CreateActivityDTO(
                userId: user()->id,
                logName: "Merubah stok bulanan dengan id: {$monthlyStock->id}",
                event: EventActivityEnum::UPDATED,
                attributeChanges: new AttributeActivityDTO(
                    old: $monthlyStock->toArray(),
                    attributes: [
                        'id' => $monthlyStock->id,
                        'daily_stock_opname_id' => $dailyStock->id,
                        'user_id' => user()->id,
                        'year' => $monthlyStock->year,
                        'month' => $monthlyStock->month,
                        'total_product' => $dailyStock->totalProduct,
                        'total_quantity' => $dailyStock->totalQuantity,
                        'notes' => $notes ?? $monthlyStock->notes,
                    ],
                )
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

    /**
     * Mengambil stok opname harian berdasarkan tanggal.
     */
    private function findDailyStock(DateTimeInterface $date): DailyStockOpnameEntity
    {
        $result = $this->dailyStockRepository->findByDate($date);

        if ($result === null) {
            throw ValidationException::withMessages([
                'daily_stock_opname_date' => 'Stok opname harian tidak ditemukan.',
            ]);
        }

        return $result;
    }

    /**
     * Mengambil stok opname bulanan berdasarkan id.
     */
    private function findMonthlyStock(string $id): MonthlyStockOpnameEntity
    {
        $result = $this->monthlyStockRepository->findById($id);

        if ($result === null) {
            throw ValidationException::withMessages([
                'daily_stock_opname_date' => 'Stok awal bulan tidak ditemukan.',
            ]);
        }

        return $result;
    }
}
