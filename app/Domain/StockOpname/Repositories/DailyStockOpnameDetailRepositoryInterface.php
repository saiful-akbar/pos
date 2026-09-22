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
    public function findByHeaderAndProduct(string $headerId, string $productId): ?DailyStockOpnameDetailEntity;

    /**
     * Memeriksa apakah data berdasarkan header id dan product id sudah ada atau belum.
     */
    public function existsByHeaderAndProduct(string $headerId, string $productId): bool;

    /**
     * Menghitung total produk.
     */
    public function countProductByHeader(string $headerId): int|float;

    /**
     * Menghitung total stok system berdasarkan id header (daily_stock_opnames)
     */
    public function sumSystemStockByHeader(string $headerId): int|float;

    /**
     * Menghitung total stok fisik berdasarkan id header (daily_stock_opnames)
     */
    public function sumPhysicalStockByHeader(string $headerId): int|float;
}
