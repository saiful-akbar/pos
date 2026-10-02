<?php

declare(strict_types=1);

namespace App\Application\StockOpname\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\StockOpname\Repositories\DailyStockOpnameDetailRepositoryInterface;
use App\Domain\StockOpname\Repositories\DailyStockOpnameRepositoryInterface;
use App\Domain\StockOpname\Repositories\MonthlyStockOpnameRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeleteDailyStockOpname
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private readonly MonthlyStockOpnameRepositoryInterface $monthlyStockRepository,
        private readonly DailyStockOpnameRepositoryInterface $dailyStockRepository,
        private readonly DailyStockOpnameDetailRepositoryInterface $dailyStockDetailRepository,
        private readonly ActivityLogRepositoryInterface $activityRepository,
        private readonly ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute
     */
    public function execute(string $id): void
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $dailyStock = $this->dailyStockRepository->findById($id);

            if ($dailyStock === null) {
                throw ValidationException::withMessages([
                    'message' => 'Stok opname tidak ditemukan.',
                ]);
            }

            $monthlyStock = $this->monthlyStockRepository->existsByDailyStockOpnameId($dailyStock->id);

            if ($monthlyStock) {
                throw ValidationException::withMessages([
                    'message' => 'Stok opname ini ditetapkan sebagai stok awal bulanan.',
                ]);
            }

            $this->dailyStockRepository->deleteById($dailyStock->id);
            $this->dailyStockDetailRepository->deleteByDailyStockOpnameId($dailyStock->id);

            $this->activityRepository->create(new CreateActivityDTO(
                userId: user()->id,
                logName: 'Menghapus stok opname harian',
                event: EventActivityEnum::REMOVED,
                attributeChanges: new AttributeActivityDTO(
                    old: $dailyStock->toArray(),
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
