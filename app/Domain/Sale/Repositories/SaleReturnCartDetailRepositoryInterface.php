<?php

declare(strict_types=1);

namespace App\Domain\Sale\Repositories;

use App\Application\Sale\DTOs\SaleReturnCartDetailDTO;
use App\Domain\Sale\Entities\SaleReturnCartDetailEntity;
use Illuminate\Database\Eloquent\Builder;

interface SaleReturnCartDetailRepositoryInterface
{
    /**
     * Mengambil data berdasarkan sale_return_cart_id beserta data produk dan unit dalam format query builder.
     */
    public function getQueryBySaleReturnCartId(string $saleReturnCartId): Builder;

    /**
     * Mengambil daftar (koleksi) data berdasarkan sale_return_cart_id.
     *
     * @return array<SaleReturnCartDetailEntity>
     */
    public function getBySaleReturnCartId(string $saleReturnCartId): array;

    /**
     * Mengambil data berdasarkan ID.
     */
    public function findById(string $id): ?SaleReturnCartDetailEntity;

    /**
     * Mengambil data berdasarkan sale_return_cart_id dan product_id.
     */
    public function findBySaleReturnCartIdAndProductId(string $saleReturnCartId, string $productId): ?SaleReturnCartDetailEntity;

    /**
     * Membuat data sale return cart detail baru.
     */
    public function create(SaleReturnCartDetailDTO $value): SaleReturnCartDetailEntity;

    /**
     * Update data sale return cart detail.
     */
    public function update(string $id, SaleReturnCartDetailDTO $value): int;

    /**
     * Menghapus permanen data sale return cart detail berdasarkan ID.
     */
    public function deleteById(string $id): int;

    /**
     * Menghapus permanen seluruh data berdasarkan sale_return_cart_id.
     */
    public function deleteBySaleReturnCartId(string $saleReturnCartId): int;

    /**
     * Menghitung total produk.
     */
    public function countProductBySaleReturnCartId(string $saleReturnCartId): float;

    /**
     * Menghitung total quantity.
     */
    public function sumQuantityBySaleReturnCartId(string $saleReturnCartId): float;

    /**
     * Menghitung total amount (grand_total).
     */
    public function sumAmountBySaleReturnCartId(string $saleReturnCartId): float;
}
