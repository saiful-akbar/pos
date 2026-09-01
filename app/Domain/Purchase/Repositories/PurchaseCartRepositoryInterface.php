<?php

declare(strict_types=1);

namespace App\Domain\Purchase\Repositories;

use App\Application\Purchase\DTOs\CreatePurchaseCartDTO;
use App\Application\Purchase\DTOs\UpdateQuantityAndPricePurchaseCartDTO;
use App\Domain\Purchase\Entities\PurchaseCartEntity;

interface PurchaseCartRepositoryInterface
{
    /**
     * Mengambil data cart berdasarkan user id.
     */
    public function findByUserId(string $userId): ?PurchaseCartEntity;

    /**
     * Membuat data cart baru.
     */
    public function create(CreatePurchaseCartDTO $dto): PurchaseCartEntity;

    /**
     * Update total quantity dan total price purchase cart.
     */
    public function updateQuantityAndPriceById(UpdateQuantityAndPricePurchaseCartDTO $dto): void;

    /**
     * Hapus purchase cart.
     */
    public function deleteByUserId(string $userId): void;
}
