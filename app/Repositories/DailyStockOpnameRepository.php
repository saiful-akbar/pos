<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\StockOpname\DTOs\DailyStockOpnameDTO;
use App\Base\Repository;
use App\Domain\StockOpname\Entities\DailyStockOpnameEntity;
use App\Domain\StockOpname\Repositories\DailyStockOpnameRepositoryInterface;
use App\Domain\StockOpname\ValueObjects\UserStockOpnameValue;
use App\Models\DailyStockOpname;
use App\Models\User;
use DateTimeInterface;
use Override;

class DailyStockOpnameRepository extends Repository implements DailyStockOpnameRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new DailyStockOpname());
    }

    /**
     * Mengambil daftar stock opname berdasarkan tentang tanggal.
     *
     * @return DailyStockOpnameEntity[]
     */
    #[Override]
    public function getByDate(DateTimeInterface $fromDate, DateTimeInterface $toDate): array
    {
        try {
            return $this->model->query()
                ->with('user')
                ->whereBetween('date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')])
                ->orderBy('date', 'desc')
                ->get()
                ->map(fn ($data) => $this->mapModelToDailyStockOpnameEntity($data))
                ->toArray();
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengmabil data berdasarkan id.
     */
    #[Override]
    public function findById(string $id): ?DailyStockOpnameEntity
    {
        try {
            $data = $this->model->query()
                ->with('user')
                ->find($id);

            if ($data === null) {
                return null;
            }

            return $this->mapModelToDailyStockOpnameEntity($data);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Memeriksa apakah data ada atau tidak berdasarkan ind.
     */
    #[Override]
    public function existsById(string $id): bool
    {
        try {
            return $this->model->query()
                ->where('id', '=', $id)
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
     * Update data.
     */
    #[Override]
    public function update(string $id, DailyStockOpnameDTO $value): int
    {
        try {
            return $this->model->query()
                ->where('id', '=', $id)
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
     * mengambil data berdasarkan tanggal.
     */
    #[Override]
    public function findByDate(DateTimeInterface $date): ?DailyStockOpnameEntity
    {
        try {
            $result = $this->model->query()
                ->with('user')
                ->where('date', $date->format('Y-m-d'))
                ->first();

            return $result === null ? null : $this->mapModelToDailyStockOpnameEntity($result);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Soft delete by id.
     */
    #[Override]
    public function removeById(string $id): int
    {
        try {
            return $this->model->query()
                ->where('id', $id)
                ->delete();
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Hapus permanen berdasarkan id.
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
}
