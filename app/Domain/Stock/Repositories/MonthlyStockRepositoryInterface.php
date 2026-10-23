<?php

declare(strict_types=1);

namespace App\Domain\Stock\Repositories;

use App\Application\Stock\DTOs\MonthlyStockDTO;
use App\Domain\Stock\Entities\MonthlyStockEntity;
use Illuminate\Database\Eloquent\Builder;

interface MonthlyStockRepositoryInterface
{
    /**
     * Memeriksa apakah sudah ada stok bulanan pada tahun dan bulan tertentu.
     */
    public function existsByYearAndMonth(int $year, int $month): bool;

    /**
     * Mengambil stok bulanan berdasarkan id.
     */
    public function findById(string $id): ?MonthlyStockEntity;

    /**
     * Hapus permanen stok bulanan (detail ikut terhapus oleh relasi cascade).
     */
    public function delete(string $id): int;

    /**
     * Mengambil stok bulanan beserta user dan stok harian dalam format query builder (bulan opsional).
     */
    public function getQueryByYearAndMonth(int $year, ?int $month = null): Builder;

    /**
     * Memeriksa apakah stok harian sudah dijadikan stok bulanan (termasuk yang sudah di-soft delete, karena FK restrict).
     */
    public function existsByDailyStockId(string $dailyStockId): bool;

    /**
     * Membuat (insert) data stok bulanan baru.
     */
    public function create(MonthlyStockDTO $value): MonthlyStockEntity;
}
