<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Stock\DTOs\MonthlyStockDTO;
use App\Base\Repository;
use App\Domain\Stock\Entities\MonthlyStockEntity;
use App\Domain\Stock\Repositories\MonthlyStockRepositoryInterface;
use App\Models\MonthlyStock;
use Override;

class MonthlyStockRepository extends Repository implements MonthlyStockRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new MonthlyStock());
    }

    /**
     * Memeriksa apakah stok harian sudah dijadikan stok bulanan (termasuk yang sudah di-soft delete, karena FK restrict).
     */
    #[Override]
    public function existsByDailyStockId(string $dailyStockId): bool
    {
        try {
            return $this->model
                ->newQuery()
                ->withTrashed()
                ->where('daily_stock_id', $dailyStockId)
                ->exists();
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Memeriksa apakah sudah ada stok bulanan pada tahun dan bulan tertentu.
     */
    #[Override]
    public function existsByYearAndMonth(int $year, int $month): bool
    {
        try {
            return $this->model
                ->newQuery()
                ->where('year', $year)
                ->where('month', $month)
                ->exists();
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Membuat (insert) data stok bulanan baru.
     */
    #[Override]
    public function create(MonthlyStockDTO $value): MonthlyStockEntity
    {
        try {
            $model = $this->model
                ->newQuery()
                ->create($value->toArray());

            $model->load(['user']);

            return MonthlyStockEntity::fromModel($model);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }
}
