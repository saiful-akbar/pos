<?php

declare(strict_types=1);

namespace App\Domain\Purchase\Repositories;

use App\Application\Purchase\DTOs\CreatePurchaseDetailDTO;
use App\Application\Purchase\DTOs\GetPurchasesDTO;
use App\Domain\Purchase\Entities\PurchaseDetailEntity;
use Illuminate\Database\Eloquent\Builder;

interface PurchaseDetailRepositoryInterface
{
    /**
     * Membuat (insert) data purchase detail.
     */
    public function create(CreatePurchaseDetailDTO $dto): PurchaseDetailEntity;

    /**
     * Mengambil daftar purchase detail
     * berdasarkan id purchase dalam format query builder.
     */
    public function getByPurchaseIdQuery(string $purchaseId): Builder;

    /**
     * Mengambil daftar purchase detail berdasarkan id purchase.
     *
     * @return PurchaseDetailEntity[]
     */
    public function getByPurchaseId(string $purchaseId): array;

    /**
     * Mengambil daftar detail pembelian berdasarkan rentang tanggal.
     */
    public function getByDate(GetPurchasesDTO $params): array;
}
