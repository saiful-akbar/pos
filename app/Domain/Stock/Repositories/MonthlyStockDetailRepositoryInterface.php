<?php

declare(strict_types=1);

namespace App\Domain\Stock\Repositories;

use App\Application\Stock\DTOs\MonthlyStockDetailDTO;
use Illuminate\Database\Eloquent\Builder;

interface MonthlyStockDetailRepositoryInterface
{
    /**
     * Membuat (insert) banyak data detail stok bulanan sekaligus. Mengembalikan jumlah baris yang dibuat.
     *
     * @param array<MonthlyStockDetailDTO> $values
     */
    public function createMany(array $values): int;

    /**
     * Mengambil detail stok bulanan beserta produk dan unit dalam format query builder.
     */
    public function getQueryByMonthlyStockId(string $monthlyStockId): Builder;

    /**
     * Mengambil ringkasan detail stok bulanan (id produk, quantity, kelipatan) berdasarkan id stok bulanan.
     *
     * @return array<int, array{product_id: string, quantity: float, multiples_quantity: float}>
     */
    public function getByMonthlyStockId(string $monthlyStockId): array;
}
