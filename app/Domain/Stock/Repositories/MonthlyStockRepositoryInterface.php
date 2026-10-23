<?php

declare(strict_types=1);

namespace App\Domain\Stock\Repositories;

use App\Application\Stock\DTOs\MonthlyStockDTO;
use App\Domain\Stock\Entities\MonthlyStockEntity;

interface MonthlyStockRepositoryInterface
{
    /**
     * Memeriksa apakah sudah ada stok bulanan pada tahun dan bulan tertentu.
     */
    public function existsByYearAndMonth(int $year, int $month): bool;

    /**
     * Memeriksa apakah stok harian sudah dijadikan stok bulanan (termasuk yang sudah di-soft delete, karena FK restrict).
     */
    public function existsByDailyStockId(string $dailyStockId): bool;

    /**
     * Membuat (insert) data stok bulanan baru.
     */
    public function create(MonthlyStockDTO $value): MonthlyStockEntity;
}
