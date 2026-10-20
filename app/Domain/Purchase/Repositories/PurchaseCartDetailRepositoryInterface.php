<?php

declare(strict_types=1);

namespace App\Domain\Purchase\Repositories;

use App\Application\Purchase\DTOs\PurchaseCartDetailDTO;
use App\Domain\Purchase\Entities\PurchaseCartDetailEntity;
use Illuminate\Database\Eloquent\Builder;

interface PurchaseCartDetailRepositoryInterface
{
    /**
     * Mengambil data berdasarkan product_id.
     */
    public function findByPurchaseCartIdAndProductId(string $purchaseCartId, string $productId): ?PurchaseCartDetailEntity;

    /**
     * Mengambil data berdasarkan id.
     */
    public function findById(string $id): ?PurchaseCartDetailEntity;

    /**
     * Create (insert).
     */
    public function create(PurchaseCartDetailDTO $values): PurchaseCartDetailEntity;

    /**
     * Update
     */
    public function update(string $id, PurchaseCartDetailDTO $values): int;

    /**
     * Delete
     */
    public function delete(string $id): int;

    /**
     * Menghitung total produk berdasarkan purchase_cart_id.
     */
    public function countProductByPurchaseCartId(string $purchaseCartId): float;

    /**
     * Menghitung total quantity berdasarkan purchase_cart_id.
     */
    public function sumQuantityByPurchaseCartId(string $purchaseCartId): float;

    /**
     * Menghitung total price (subtotal) berdasarkan purchase_cart_id.
     */
    public function sumSubtotalByPurchaseCartId(string $purchaseCartId): float;

    /**
     * Mengambil daftar berdasarkan purchase_cart_id.
     *
     * @return PurchaseCartDetailEntity[]
     */
    public function getByPurchaseCartId(string $purchaseCartId): array;

    /**
     * Mengambil daftar berdasarkan purchase_cart_id dalam format query builder.
     */
    public function getQueryByPurchaseCartId(string $purchaseCartId): Builder;
}
