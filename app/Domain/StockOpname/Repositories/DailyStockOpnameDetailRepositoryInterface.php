<?php

declare(strict_types=1);

namespace App\Domain\StockOpname\Repositories;

use App\Application\StockOpname\DTOs\DailyStockOpnameDetailDTO;
use App\Domain\StockOpname\Entities\DailyStockOpnameDetailEntity;

interface DailyStockOpnameDetailRepositoryInterface
{
    /**
     * Mengambil daftar data berdasarkan header id.
     *
     * @return DailyStockOpnameDetailEntity[]
     */
    public function getByHeaderId(string $headerId): array;

    /**
     * Create (insert).
     */
    public function create(DailyStockOpnameDetailDTO $value): DailyStockOpnameDetailEntity;

    /**
     * Update data.
     */
    public function update(string $id, DailyStockOpnameDetailDTO $value): int;

    /**
     * Hapus data berdasarkan id.
     */
    public function deleteById(string $id): int;

    /**
     * Mengambil data berdasarkan id.
     */
    public function findById(string $id): ?DailyStockOpnameDetailEntity;

    /**
     * Mengambil data berdasarkan id header dan id product.
     */
    public function findByDailyStockOpnameIdAndProductId(string $dailyStockOpnameId, string $productId): ?DailyStockOpnameDetailEntity;

    /**
     * Memeriksa apakah data berdasarkan header id dan product id sudah ada atau belum.
     */
    public function existsByDailyStockOpnameIdAndProductId(string $dailyStockOpnameId, string $productId): bool;

    /**
     * Menghitung total produk.
     */
    public function countProductByDailyStockOpnameId(string $dailyStockOpnameId): int|float;

    /**
     * Menghitung total quantity berdasarkan id header (daily_stock_opnames)
     */
    public function sumQuantityByDailyStockOpnameId(string $dailyStockOpnameId): int|float;
}
