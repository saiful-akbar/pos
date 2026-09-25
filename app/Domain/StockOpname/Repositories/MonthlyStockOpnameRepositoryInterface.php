<?php

declare(strict_types=1);

namespace App\Domain\StockOpname\Repositories;

use App\Application\StockOpname\DTOs\MonthlyStockOpnameDTO;
use App\Domain\StockOpname\Entities\MonthlyStockOpnameEntity;

interface MonthlyStockOpnameRepositoryInterface
{
    /**
     * Menyimpan (insert) data.
     */
    public function create(MonthlyStockOpnameDTO $value): MonthlyStockOpnameEntity;

    /**
     * Memeriksa apakah data ada atau tidak berdasarkan tahun dan bulan.
     */
    public function existsByYearAndMonth(int $year, int $month): bool;

    /**
     * Memeriksa apakah data ada atau tidak berdasarkan id.
     */
    public function existsById(string $id): bool;

    /**
     * Mengambil data berdasarkan tahun dan bulan.
     */
    public function findByYearAndMonth(int $year, int $month): ?MonthlyStockOpnameEntity;

    /**
     * Mengambil data berdasarkan id.
     */
    public function findById(string $id): ?MonthlyStockOpnameEntity;

    /**
     * Menghapus berdasarkan id.
     *
     * @param string $id
     * @return integer
     */
    public function deleteById(string $id): int;

    /**
     * Update stock
     *
     * @param string $id
     * @param MonthlyStockOpnameDTO $value
     * @return integer
     */
    public function update(string $id, MonthlyStockOpnameDTO $value): int;
}
