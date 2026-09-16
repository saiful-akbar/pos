<?php

declare(strict_types=1);

namespace App\Domain\Sale\Repositories;

use App\Application\Sale\DTOs\SaleReturnCartDetailDTO;
use App\Domain\Sale\Entities\SaleReturnCartDetailEntity;
use Illuminate\Database\Eloquent\Builder;

interface SaleReturnCartDetailRepositoryInterface
{
    /**
     * Menambah (insert) data.
     */
    public function create(SaleReturnCartDetailDTO $dto): SaleReturnCartDetailEntity;

    /**
     * Memperbarui data.
     */
    public function update(string $id, SaleReturnCartDetailDTO $dto): int;

    /**
     * Mengahpus berdasarkan id.
     */
    public function deleteById(string $id): int;

    /**
     * Mengambil data berdasarkan id cart dan id product.
     */
    public function findByCartIdAndProductId(string $cartId, string $productId): ?SaleReturnCartDetailEntity;

    /**
     * Mengambil data berdasarkan id.
     */
    public function findById(string $id): ?SaleReturnCartDetailEntity;

    /**
     * Mengambil daftar data berdasarkan cart dalam fomat query builder.
     */
    public function getByCartIdQuery(string $cartId): Builder;

    /**
     * Menhitung total quantity berdasarkan id cart.
     */
    public function sumQuantityByCartId(string $cartId): int|float;

    /**
     * Menghitung total amoun (subtotal) berdasarkan id cart.
     */
    public function sumSubtotalByCartId(string $cartId): int|float;

    /**
     * Mengambil daftar berdasarkan return cart id.
     *
     * @param string $cartId
     * @return SaleReturnCartDetailEntity[]
     */
    public function getByCartId(string $cartId): array;
}
