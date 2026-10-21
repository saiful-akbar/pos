<?php

declare(strict_types=1);

namespace App\Domain\Sale\Repositories;

use App\Application\Sale\DTOs\SaleCartDetailDTO;
use App\Domain\Sale\Entities\SaleCartDetailEntity;
use Illuminate\Database\Eloquent\Builder;

interface SaleCartDetailRepositoryInterface
{
    /**
     * Mengambil data berdasarkan sale_cart_id dan product_id.
     */
    public function findBySaleCartIdAndProductId(string $saleCartId, string $productId): ?SaleCartDetailEntity;

    /**
     * Mengambil data berdasarkan kolom dan value.
     */
    public function find(string $column, string|float|int|bool $value): ?SaleCartDetailEntity;

    /**
     * Create (insert)
     */
    public function create(SaleCartDetailDTO $value): SaleCartDetailEntity;

    /**
     * Update.
     */
    public function update(string $id, SaleCartDetailDTO $value): int;

    /**
     * Hapus permanen,
     */
    public function delete(string $id): int;

    /**
     * Menghitung total produk.
     */
    public function countProductBySaleCartId(string $saleCartId): float;

    /**
     * Menghitung total quantity.
     */
    public function sumQuantityBySaleCartId(string $saleCartId): float;

    /**
     * Menghitung total amount (grand_total)
     */
    public function sumAmountBySaleCartId(string $saleCartId): float;

    /**
     * Mengambil data berdasarkan sale_cart_id dalam format query builder.
     */
    public function getQueryBySaleCartId(string $saleCartId): Builder;

    /**
     * Mengambil data berdasarkan sale_cart_id dalam.
     *
     * @return SaleCartDetailEntity[]
     */
    public function getBySaleCartId(string $saleCartId): array;
}
