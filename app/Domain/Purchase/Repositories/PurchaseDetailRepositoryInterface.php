<?php

declare(strict_types=1);

namespace App\Domain\Purchase\Repositories;

use App\Application\Purchase\DTOs\PurchaseDetailDTO;
use App\Domain\Purchase\Entities\PurchaseDetailEntity;
use Illuminate\Database\Eloquent\Builder;

interface PurchaseDetailRepositoryInterface
{
    /**
     * Mengambil daftar berdasarkan purchase_id.
     *
     * @return PurchaseDetailEntity[]
     */
    public function getByPurchaseId(string $purchaseId): array;

    /**
     * Mengambil daftar berdasarkan purchase_id dalam format query builder.
     */
    public function getQueryByPurchaseId(string $purchaseId): Builder;

    /**
     * Mengambil data berdasarkan id.
     */
    public function findById(string $id): ?PurchaseDetailEntity;

    /**
     * Create (insert).
     */
    public function create(PurchaseDetailDTO $value): PurchaseDetailEntity;

    /**
     * Remove (soft delete).
     */
    public function remove(string $id): int;

    /**
     * Remove (soft delete) berdasarkan purchase_id.
     */
    public function removeByPurchaseId(string $purchaseId): int;
}
