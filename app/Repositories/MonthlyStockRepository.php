<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Stock\DTOs\MonthlyStockDTO;
use App\Base\Repository;
use App\Domain\Stock\Entities\MonthlyStockEntity;
use App\Domain\Stock\Repositories\MonthlyStockRepositoryInterface;
use App\Models\MonthlyStock;
use Illuminate\Database\Eloquent\Builder;
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
     * Mengambil stok bulanan beserta user dan stok harian dalam format query builder (bulan opsional).
     */
    #[Override]
    public function getQueryByYearAndMonth(int $year, ?int $month = null): Builder
    {
        try {
            $columns = [
                'monthly_stocks.id as id',
                'monthly_stocks.year as year',
                'monthly_stocks.month as month',
                'monthly_stocks.total_product as total_product',
                'monthly_stocks.total_quantity as total_quantity',
                'monthly_stocks.notes as notes',
                'monthly_stocks.created_at as created_at',
                'users.full_name as user_name',
                'daily_stocks.id as daily_stock_id',
                'daily_stocks.date as daily_stock_date',
            ];

            return $this->model
                ->newQuery()
                ->select($columns)
                ->leftJoin('users', 'monthly_stocks.user_id', '=', 'users.id')
                ->leftJoin('daily_stocks', 'monthly_stocks.daily_stock_id', '=', 'daily_stocks.id')
                ->where('monthly_stocks.year', $year)
                // Filter bulan hanya diterapkan jika dipilih.
                ->when($month !== null, fn (Builder $query) => $query->where('monthly_stocks.month', $month));
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil stok bulanan berdasarkan id.
     */
    #[Override]
    public function findById(string $id): ?MonthlyStockEntity
    {
        try {
            $model = $this->model
                ->newQuery()
                ->with('user')
                ->find($id);

            return $model !== null
                ? MonthlyStockEntity::fromModel($model)
                : null;
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
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

    /**
     * Hapus permanen stok bulanan (detail ikut terhapus oleh relasi cascade).
     */
    #[Override]
    public function delete(string $id): int
    {
        try {
            return $this->model
                ->newQuery()
                ->whereKey($id)
                ->forceDelete();
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }
}
