<?php

declare(strict_types=1);

namespace App\Domain\Purchase\Repositories;

use App\Application\Purchase\DTOs\CreatePurchaseCartDetailDTO;
use App\Application\Purchase\DTOs\CreatePurchaseDTO;
use App\Application\Purchase\DTOs\UpdatePurchaseCartDetailDTO;
use App\Domain\Purchase\Entities\PurchaseEntity;

interface PurchaseCartRepositoryInterface
{
    /**
     * Mengambil data cart berdasarkan user id.
     */
    public function findByUser(string $userId): ?PurchaseEntity;

    /**
     * Membuat data cart baru.
     */
    public function create(CreatePurchaseDTO $dto): PurchaseEntity;

    /**
     * Membuat (insert) data cart detail
     */
    public function createDetail(string $userId, CreatePurchaseCartDetailDTO $dto): void;

    /**
     * Update data cart detail.
     */
    public function updateDetail(UpdatePurchaseCartDetailDTO $dto): void;

    /**
     * Mengahpus cart detail
     */
    public function deleteDetailById(string $id): void;

    /**
     * Menghitung total quantity cart detail berdasarkan id cart.
     *
     * @param string $id
     * @return integer
     */
    public function sumDetailQuantityByCartId(string $id): int;

    /**
     * Menghitung total subtotal berdasarkan id cart.
     *
     * @param string $id
     * @return integer
     */
    public function sumDetailSubtotalByCartId(string $id): int;
}
