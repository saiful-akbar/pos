<?php

declare(strict_types=1);

namespace App\Domain\Stock\Repositories;

use App\Application\Stock\DTOs\MonthlyStockDetailDTO;

interface MonthlyStockDetailRepositoryInterface
{
    /**
     * Membuat (insert) banyak data detail stok bulanan sekaligus. Mengembalikan jumlah baris yang dibuat.
     *
     * @param  array<MonthlyStockDetailDTO>  $values
     */
    public function createMany(array $values): int;
}
