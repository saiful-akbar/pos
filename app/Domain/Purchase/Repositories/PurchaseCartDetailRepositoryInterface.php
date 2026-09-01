<?php

declare(strict_types=1);

namespace App\Domain\Purchase\Repositories;

use App\Application\Purchase\DTOs\CreatePurchaseCartDetailDTO;
use App\Application\Purchase\DTOs\UpdatePurchaseCartDetailDTO;
use App\Domain\Purchase\Entities\PurchaseCartDetailEntity;

interface PurchaseCartDetailRepositoryInterface
{
    /**
     * Membuat (insert) atau memperbarui (update) data cart detail
     */
    public function createOrUpdate(CreatePurchaseCartDetailDTO $dto): PurchaseCartDetailEntity;

    /**
     * Update data purcahse cart detail.
     */
    public function update(UpdatePurchaseCartDetailDTO $dto): void;

    /**
     * Mengahpus purcahse cart detail
     */
    public function deleteById(string $id): void;

    /**
     * Menghitung total quantity cart detail berdasarkan id cart.
     */
    public function sumQuantityByCartId(string $cartId): int;

    /**
     * Menghitung total subtotal berdasarkan id cart.
     */
    public function sumSubtotalByCartId(string $cartId): int;

    /**
     * Mengambil daftar purchase cart detail berdasarkan id dari purchase cart.
     *
     * @return PurchaseCartDetailEntity[]
     */
    public function getByCartId(string $cartId): array;
}
