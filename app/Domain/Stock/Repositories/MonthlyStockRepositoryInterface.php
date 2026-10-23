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
     * Membuat (insert) data stok bulanan baru.
     */
    public function create(MonthlyStockDTO $value): MonthlyStockEntity;
}
