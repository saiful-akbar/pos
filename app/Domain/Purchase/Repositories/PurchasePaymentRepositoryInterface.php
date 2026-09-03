<?php

declare(strict_types=1);

namespace App\Domain\Purchase\Repositories;

use App\Application\Purchase\DTOs\CreatePurchasePaymentDTO;
use App\Domain\Purchase\Entities\PurchasePaymentEntity;
use Illuminate\Database\Eloquent\Builder;

interface PurchasePaymentRepositoryInterface
{
    /**
     * Membuat (insert) data pembayaran pembelian.
     */
    public function create(CreatePurchasePaymentDTO $dto): PurchasePaymentEntity;

    /**
     * Mengambil daftar pembayaran pembelian berdasarkan purchase_id.
     *
     * @return PurchasePaymentEntity[]
     */
    public function getByPurchaseId(string $purchaseId): array;

    /**
     * Mengambil daftar pembayaran berdasarkan id pembelian
     * dalam format query builder.
     */
    public function getByPurchaseIdQuery(string $purchaseId): Builder;

    /**
     * Menghapus permanen data pembayaran.
     */
    public function deleteById(string $id): void;

    /**
     * Mengambil data pembayaran pembelian berdasarkan id.
     */
    public function findById(string $id): ?PurchasePaymentEntity;

    /**
     * Menghitung total amount berdasarkan pirchase_id.
     */
    public function sumAmountByPurchaseId(string $purchaseId): int;
}
