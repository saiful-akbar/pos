<?php

declare(strict_types=1);

namespace App\Domain\Sale\Repositories;

use App\Application\Sale\DTOs\SaleCartDetailDTO;
use App\Domain\Sale\Entities\SaleCartDetailEntity;
use Illuminate\Database\Eloquent\Builder;

interface SaleCartDetailRepositoryInterface
{
    /**
     * Menyimpan (insert) data cart detail baru.
     */
    public function create(SaleCartDetailDTO $dto): ?SaleCartDetailEntity;

    /**
     * Memperbarui data cart detail
     */
    public function update(string $id, SaleCartDetailDTO $dto): int;

    /**
     * Membuat atau memperbarui data cart detail.
     */
    public function createOrUpdate(SaleCartDetailDTO $dto): void;

    /**
     * Menghitung total quantity
     */
    public function sumQuantityByCartId(string $cartId): int;

    /**
     * Menghitung total subtotal.
     */
    public function sumSubtotalByCartId(string $cartId): int;

    /**
     * Mengambil data cart detail berdasarkan id.
     */
    public function findById(string $id): ?SaleCartDetailEntity;

    /**
     * Mengambil daftar cart detail berdasarkan
     * cart id dalam format dalam format query builder.
     */
    public function getByCartIdQuery(string $cartId): Builder;

    /**
     * Menghapus data cart details.
     */
    public function deleteById(string $id): int;

    /**
     * Mengambil daftar cart detail berdasarkan cart id.
     *
     * @return SaleCartDetailEntity[]
     */
    public function getByCartId(string $cartId): array;
}
