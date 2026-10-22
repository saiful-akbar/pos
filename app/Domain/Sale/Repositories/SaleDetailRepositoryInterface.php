<?php

declare(strict_types=1);

namespace App\Domain\Sale\Repositories;

use App\Application\Sale\DTOs\SaleDetailDTO;
use App\Domain\Sale\Entities\SaleDetailEntity;
use Illuminate\Database\Eloquent\Builder;

interface SaleDetailRepositoryInterface
{
    /**
     * Mencari data sale detail berdasarkan ID.
     */
    public function findById(string $id): ?SaleDetailEntity;

    /**
     * Mencari data sale detail berdasarkan sale_id dan product_id.
     */
    public function findBySaleIdAndProductId(string $saleId, string $productId): ?SaleDetailEntity;

    /**
     * Membuat data sale detail baru.
     */
    public function create(SaleDetailDTO $value): SaleDetailEntity;

    /**
     * Menghapus data sale detail berdasarkan ID.
     */
    public function deleteById(string $id): int;

    /**
     * Menghapus seluruh detail berdasarkan ID penjualan.
     */
    public function deleteBySaleId(string $saleId): int;

    /**
     * Mengambil daftar detail penjualan berdasarkan ID penjualan.
     *
     * @return array<SaleDetailEntity>
     */
    public function getBySaleId(string $saleId): array;

    /**
     * Mengambil data berdasarkan sale_id beserta data produk dan unit dalam format query builder.
     */
    public function getQueryBySaleId(string $saleId): Builder;
}
