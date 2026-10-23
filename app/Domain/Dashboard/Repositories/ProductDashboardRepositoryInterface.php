<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Repositories;

use App\Domain\Dashboard\Entities\LowStockProductEntity;

interface ProductDashboardRepositoryInterface
{
    /**
     * Mengambil produk aktif dengan stok saat ini pada atau di bawah batas minimum, paling mendesak lebih dahulu.
     *
     * @return LowStockProductEntity[]
     */
    public function getLowStockProducts(int $limit): array;

    /**
     * Menghitung seluruh produk aktif yang stoknya pada atau di bawah batas minimum.
     */
    public function countLowStockProducts(): int;
}
