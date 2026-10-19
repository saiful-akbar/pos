<?php

declare(strict_types=1);

namespace App\Domain\Purchase\Repositories;

use App\Application\Purchase\DTOs\PurchaseCartDTO;
use App\Domain\Purchase\Entities\PurchaseCartEntity;

interface PurchaseCartRepositoryInterface
{
    /**
     * Mengambil data berdasarkan user_id.
     */
    public function findByUserId(string $userId): ?PurchaseCartEntity;

    /**
     * Mengambil data berdasarkan id.
     */
    public function findById(string $id): ?PurchaseCartEntity;

    /**
     * Create (insert).
     */
    public function create(PurchaseCartDTO $values): PurchaseCartEntity;

    /**
     * Update.
     */
    public function update(string $id, PurchaseCartDTO $values): int;

    /**
     * Delete.
     */
    public function delete(string $id): int;
}
