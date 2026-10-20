<?php

declare(strict_types=1);

namespace App\Domain\Purchase\Repositories;

use App\Application\Purchase\DTOs\PurchasePaymentDTO;
use App\Domain\Purchase\Entities\PurchasePaymentEntity;
use Illuminate\Database\Eloquent\Builder;

interface PurchasePaymentRepositoryInterface
{
    /**
     * Mengambil daftar pembayaran berdasarkan purchase_id dalam format query builder.
     */
    public function getQueryByPurchaseId(string $purchaseId): Builder;

    /**
     * Create (insert).
     */
    public function create(PurchasePaymentDTO $value): PurchasePaymentEntity;

    /**
     * Remove (soft delete) berdasarkan purchase_id.
     */
    public function removeByPurchaseId(string $purchaseId): int;

    /**
     * Mengambil data berdasarkan id.
     */
    public function findById(string $id): ?PurchasePaymentEntity;

    /**
     * Hapus permanen berdasarkan id.
     */
    public function delete(string $id): int;

    /**
     * Menghitung jumlah nominal berdasarkan purchase_id.
     */
    public function sumAmountByPurchaseId(string $purchaseId): float;
}
