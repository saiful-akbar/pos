<?php

declare(strict_types=1);

namespace App\Domain\StockOpname\Repositories;

use App\Application\StockOpname\DTOs\MonthlyStockOpnameDetailDTO;
use App\Domain\StockOpname\Entities\MonthlyStockOpnameDetailEntity;

interface MonthlyStockOpnameDetailRepositoryInterface
{
    /**
     * Menyimpan (insert) data.
     */
    public function create(MonthlyStockOpnameDetailDTO $value): MonthlyStockOpnameDetailEntity;

    /**
     * Mengambil daftar data berdasarkan monthly_stock_opname_id.
     *
     * @return MonthlyStockOpnameDetailEntity[]
     */
    public function getByMonthlyStockOpnameId(string $id): array;
}
