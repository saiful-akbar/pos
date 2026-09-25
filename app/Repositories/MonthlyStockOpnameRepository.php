<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\StockOpname\DTOs\MonthlyStockOpnameDTO;
use App\Base\Repository;
use App\Domain\StockOpname\Entities\DailyStockOpnameEntity;
use App\Domain\StockOpname\Entities\MonthlyStockOpnameEntity;
use App\Domain\StockOpname\Repositories\MonthlyStockOpnameRepositoryInterface;
use App\Domain\StockOpname\ValueObjects\UserStockOpnameValue;
use App\Models\DailyStockOpname;
use App\Models\MonthlyStockOpname;
use App\Models\User;
use Override;

class MonthlyStockOpnameRepository extends Repository implements MonthlyStockOpnameRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new MonthlyStockOpname());
    }

    /**
     * Menyimpan (insert) data.
     */
    #[Override]
    public function create(MonthlyStockOpnameDTO $value): MonthlyStockOpnameEntity
    {
        try {
            $result = $this->model->query()->create($value->toArray());

            return $this->mapModelToMonthlyStockOpnameEntity(
                model: $result->load(['user', 'daily.user']),
            );
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Memeriksa apakah data ada atau tidak berdasarkan tahun dan bulan.
     */
    #[Override]
    public function existsByYearAndMonth(int $year, int $month): bool
    {
        try {
            return $this->model->query()
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
     * Memeriksa apakah data ada atau tidak berdasarkan id.
     */
    #[Override]
    public function existsById(string $id): bool
    {
        try {
            return $this->model->query()
                ->where('id', $id)
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
     * Mengambil data berdasarkan tahun dan bulan.
     */
    #[Override]
    public function findByYearAndMonth(int $year, int $month): ?MonthlyStockOpnameEntity
    {
        try {
            $result = $this->model->query()
                ->with(['user', 'daily.user'])
                ->where('year', $year)
                ->where('month', $month)
                ->first();

            return $result === null ? null : $this->mapModelToMonthlyStockOpnameEntity($result);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil data berdasarkan id.
     */
    #[Override]
    public function findById(string $id): ?MonthlyStockOpnameEntity
    {
        try {
            $result = $this->model->query()
                ->with(['user', 'daily.user'])
                ->where('id', $id)
                ->first();

            return $result === null ? null : $this->mapModelToMonthlyStockOpnameEntity($result);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Menghapus berdasarkan id.
     */
    #[Override]
    public function deleteById(string $id): int
    {
        try {
            return $this->model->query()
                ->where('id', $id)
                ->forceDelete();
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Update stock
     */
    #[Override]
    public function update(string $id, MonthlyStockOpnameDTO $value): int
    {
        try {
            return $this->model->query()
                ->where('id', $id)
                ->update($value->toArray());
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Pemetaan class Model ke class UserStockOpnameValue
     */
    private function mapModelToUserStockOpnameValue(User $model): UserStockOpnameValue
    {
        return new UserStockOpnameValue(
            id: $model->id,
            username: $model->username,
            fullName: $model->full_name,
            photo: $model->photo,
            isActive: $model->is_active === 1,
        );
    }

    /**
     * Pemetaan class Model ke class DailyStockOpnameEntity.
     */
    private function mapModelToDailyStockOpnameEntity(DailyStockOpname $model): DailyStockOpnameEntity
    {
        return new DailyStockOpnameEntity(
            id: $model->id,
            user: $this->mapModelToUserStockOpnameValue($model->user),
            date: parse_date_time_immutable($model->date),
            totalProduct: parse_float($model->total_product),
            totalQuantity: parse_float($model->total_quantity),
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }

    /**
     * Pemetaan class Model ke class MonthlyStockOpnameEntity
     */
    private function mapModelToMonthlyStockOpnameEntity(MonthlyStockOpname $model): MonthlyStockOpnameEntity
    {
        return new MonthlyStockOpnameEntity(
            id: $model->id,
            dailyStockOpname: $this->mapModelToDailyStockOpnameEntity($model->daily),
            user: $this->mapModelToUserStockOpnameValue($model->user),
            year: parse_int($model->year),
            month: parse_int($model->month),
            totalProduct: parse_float($model->total_product),
            totalQuantity: parse_float($model->total_quantity),
            notes: $model->notes,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }
}
