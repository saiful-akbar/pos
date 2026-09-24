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
use App\Domain\StockOpname\Entities\DailyStockOpnameDetailEntity;
use App\Domain\StockOpname\Entities\DailyStockOpnameEntity;
use App\Domain\StockOpname\Repositories\DailyStockOpnameDetailRepositoryInterface;
use App\Domain\StockOpname\Repositories\DailyStockOpnameRepositoryInterface;
use App\Domain\StockOpname\Repositories\MonthlyStockOpnameDetailRepositoryInterface;
use App\Domain\StockOpname\Repositories\MonthlyStockOpnameRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateMonthlyStockOpname
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private DailyStockOpnameRepositoryInterface $dailyStockRepository,
        private DailyStockOpnameDetailRepositoryInterface $dailyStockDetailRepository,
        private MonthlyStockOpnameRepositoryInterface $monthlyStockRepository,
        private MonthlyStockOpnameDetailRepositoryInterface $monthlyStockDetailRepository,
        private ActivityLogRepositoryInterface $activityRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(string $dailyStockId, int $year, int $month, ?string $notes): void
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $dailyStock = $this->findDailyStock($dailyStockId);
            $dailyStockDetails = $this->getDailyStockDetails($dailyStock->id);

            // Periksa apakah stok bulanan sudah ada atu belum.
            if ($this->existsMonthlyStock($year, $month)) {
                throw ValidationException::withMessages([
                    'month' => 'Stok opname sudah ada pada bulan yang anda pilih.',
                ]);
            }

            // Insert data stok bulanan.
            $monthlyStock = $this->monthlyStockRepository->create(new MonthlyStockOpnameDTO(
                dailyStockOpnameId: $dailyStock->id,
                userId: user()->id,
                year: $year,
                month: $month,
                totalProduct: $dailyStock->totalProduct,
                totalSystemStock: $dailyStock->totalSystemStock,
                totalPhysicalStock: $dailyStock->totalPhysicalStock,
                totalDifference: $dailyStock->totalDifference,
                notes: $notes,
            ));

            // Insert data detail (produk) untuk stok bulanan.
            foreach ($dailyStockDetails as $dailyStockDetail) {
                $this->monthlyStockDetailRepository->create(new MonthlyStockOpnameDetailDTO(
                    monthlyStockOpnameId: $monthlyStock->id,
                    productId: $dailyStockDetail->product->id,
                    systemStock: $dailyStockDetail->systemStock,
                    physicalStock: $dailyStockDetail->physicalStock,
                    difference: $dailyStockDetail->difference,
                ));
            }

            // Buat catatan aktifitas.
            $this->activityRepository->create(new CreateActivityDTO(
                userId: user()->id,
                logName: 'Menjadikan stok harian sebagai stok bulanan',
                event: EventActivityEnum::CREATED,
                attributeChanges: new AttributeActivityDTO(
                    attributes: $monthlyStock->toArray(),
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

    /**
     * Mengambil stok opname harian berdasarkan id.
     */
    private function findDailyStock(string $id): DailyStockOpnameEntity
    {
        $result = $this->dailyStockRepository->findById($id);

        if ($result === null) {
            throw ValidationException::withMessages([
                'year' => 'Stok opname harian tidak ditemukan.',
            ]);
        }

        return $result;
    }

    /**
     * Mengambil daftar daily_stock_opname_details berdasarkan id dari daily_stock_opanmes
     *
     * @return DailyStockOpnameDetailEntity[]
     */
    private function getDailyStockDetails(string $dailyStockId): array
    {
        return $this->dailyStockDetailRepository->getByHeaderId($dailyStockId);
    }

    /**
     * Memeriksa paakah stok opname bulanan ada atu tidak.
     */
    private function existsMonthlyStock(int $year, int $month): bool
    {
        return $this->monthlyStockRepository->existsByYearAndMonth(
            year: $year,
            month: $month,
        );
    }
}
