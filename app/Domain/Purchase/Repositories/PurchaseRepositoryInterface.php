<?php

declare(strict_types=1);

namespace App\Domain\Purchase\Repositories;

use App\Application\Purchase\DTOs\CreatePurchaseDTO;
use App\Application\Purchase\DTOs\UpdatePurchaseDTO;
use App\Domain\Purchase\Entities\PurchaseEntity;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;

interface PurchaseRepositoryInterface
{
    /**
     * Membuat (insert) data pembalian.
     */
    public function create(CreatePurchaseDTO $dto): PurchaseEntity;

    /**
     * Menmabil data penjualan berdasarkan rentang waktu dalam format query builder.
     */
    public function getByDateRangeQuery(DateTimeInterface $fromDate, DateTimeInterface $toDate): Builder;

    /**
     * Mengambil data pembelian berdasarkan id.
     */
    public function findById(string $id): ?PurchaseEntity;

    /**
     * Memperbarui data pembelian.
     *
     * @param UpdatePurchaseDTO $dto
     * @return void
     */
    public function update(UpdatePurchaseDTO $dto): void;
}
