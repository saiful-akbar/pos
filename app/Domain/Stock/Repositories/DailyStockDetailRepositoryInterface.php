<?php

declare(strict_types=1);

namespace App\Domain\Stock\Repositories;

use App\Application\Stock\DTOs\DailyStockDetailDTO;
use App\Domain\Stock\Entities\DailyStockDetailEntity;
use Illuminate\Database\Eloquent\Builder;

interface DailyStockDetailRepositoryInterface
{
    /**
     * Mengambil detail stok harian beserta produk dan unit dalam format query builder.
     */
    public function getQueryByDailyStockId(string $dailyStockId): Builder;

    /**
     * Membuat (insert) data detail stok harian baru.
     */
    public function create(DailyStockDetailDTO $value): DailyStockDetailEntity;

    /**
     * Mengambil detail stok harian berdasarkan id stok harian, id produk, dan id unit.
     */
    public function findByDailyStockIdProductIdAndUnitId(string $dailyStockId, string $productId, string $unitId): ?DailyStockDetailEntity;

    /**
     * Memperbarui quantity detail stok harian.
     */
    public function updateQuantity(string $id, float $quantity): int;
}
