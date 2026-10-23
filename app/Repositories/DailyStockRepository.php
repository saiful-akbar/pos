<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Stock\DTOs\DailyStockDTO;
use App\Base\Repository;
use App\Domain\Stock\Entities\DailyStockEntity;
use App\Domain\Stock\Repositories\DailyStockRepositoryInterface;
use App\Models\DailyStock;
use DateTimeInterface;
use Override;

class DailyStockRepository extends Repository implements DailyStockRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new DailyStock());
    }

    /**
     * Mengambil daftar stok harian berdasarkan rentang tanggal.
     *
     * @return array<DailyStockEntity>
     */
    #[Override]
    public function getByDateRange(DateTimeInterface $startDate, DateTimeInterface $endDate): array
    {
        try {
            // Ambil stok harian beserta user, detail, produk (+kategori), dan unit dalam rentang tanggal.
            $models = $this->model
                ->newQuery()
                ->with(['user', 'details.product.category', 'details.unit'])
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->orderBy('date', 'asc')
                ->get();

            return $models
                ->map(fn (DailyStock $model): DailyStockEntity => DailyStockEntity::fromModel($model))
                ->all();
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil data stok harian beserta detailnya berdasarkan ID.
     */
    #[Override]
    public function findById(string $id): ?DailyStockEntity
    {
        try {
            $model = $this->model
                ->newQuery()
                ->with(['user', 'details.product.category', 'details.unit'])
                ->find($id);

            return $model !== null
                ? DailyStockEntity::fromModel($model)
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
     * Memeriksa apakah sudah ada stok harian pada tanggal tertentu.
     */
    #[Override]
    public function existsByDate(DateTimeInterface $date): bool
    {
        try {
            return $this->model
                ->newQuery()
                ->whereDate('date', $date->format('Y-m-d'))
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
     * Membuat (insert) data stok harian baru.
     */
    #[Override]
    public function create(DailyStockDTO $value): DailyStockEntity
    {
        try {
            // Simpan stok harian lalu muat relasi untuk entity.
            $model = $this->model
                ->newQuery()
                ->create($value->toArray());

            $model->load(['user', 'details.product.category', 'details.unit']);

            return DailyStockEntity::fromModel($model);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Update data stok harian berdasarkan ID.
     */
    #[Override]
    public function update(string $id, DailyStockDTO $value): int
    {
        try {
            return $this->model
                ->newQuery()
                ->whereKey($id)
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
     * Hapus permanen stok harian (detail ikut terhapus oleh relasi cascade).
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
