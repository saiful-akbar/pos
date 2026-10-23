<?php

declare(strict_types=1);

namespace App\Application\Stock\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\Stock\DTOs\MonthlyStockDetailDTO;
use App\Application\Stock\DTOs\MonthlyStockDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Stock\Entities\DailyStockDetailEntity;
use App\Domain\Stock\Entities\MonthlyStockEntity;
use App\Domain\Stock\Repositories\DailyStockRepositoryInterface;
use App\Domain\Stock\Repositories\MonthlyStockDetailRepositoryInterface;
use App\Domain\Stock\Repositories\MonthlyStockRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateMonthlyStock
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private DailyStockRepositoryInterface $dailyStockRepository,
        private MonthlyStockRepositoryInterface $monthlyStockRepository,
        private MonthlyStockDetailRepositoryInterface $monthlyStockDetailRepository,
        private ActivityLogRepositoryInterface $activityRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Menjadikan stok harian sebagai stok awal bulan pada tahun dan bulan yang dipilih.
     */
    public function execute(string $dailyStockId, int $year, int $month): MonthlyStockEntity
    {
        DB::beginTransaction();

        try {
            // Ambil stok harian beserta daftar detailnya.
            $dailyStock = $this->dailyStockRepository->findById(
                id: $dailyStockId,
            );

            if ($dailyStock === null) {
                abort(404, 'Data stok harian tidak ditemukan.');
            }

            // Satu tahun-bulan hanya boleh memiliki satu stok awal bulan.
            if ($this->monthlyStockRepository->existsByYearAndMonth(year: $year, month: $month)) {
                throw ValidationException::withMessages(messages: [
                    'month' => 'Sudah ada stok awal bulan pada tahun dan bulan yang dipilih.',
                ]);
            }

            // Insert stok bulanan berdasarkan stok harian.
            $monthlyStock = $this->monthlyStockRepository->create(
                value: new MonthlyStockDTO(
                    userId: user()->id,
                    dailyStockId: $dailyStock->id,
                    year: $year,
                    month: $month,
                    totalProduct: $dailyStock->totalProduct,
                    totalQuantity: $dailyStock->totalQuantity,
                    notes: $dailyStock->notes,
                ),
            );

            // Insert detail stok bulanan berdasarkan detail stok harian.
            $this->monthlyStockDetailRepository->createMany(
                values: array_map(
                    fn (DailyStockDetailEntity $detail): MonthlyStockDetailDTO => new MonthlyStockDetailDTO(
                        monthlyStockId: $monthlyStock->id,
                        productId: $detail->product->id,
                        unitId: $detail->unit->id,
                        quantity: $detail->quantity,
                        multiplesQuantity: $detail->multiplesQuantity,
                    ),
                    $dailyStock->details,
                ),
            );

            // Buat catatan aktifitas.
            $this->activityRepository->create(
                dto: new CreateActivityDTO(
                    userId: user()->id,
                    logName: 'Menjadikan stok harian sebagai stok awal bulan',
                    event: EventActivityEnum::CREATED,
                    attributeChanges: new AttributeActivityDTO(
                        attributes: $monthlyStock->toArray(),
                    ),
                ),
            );

            DB::commit();

            return $monthlyStock;
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
