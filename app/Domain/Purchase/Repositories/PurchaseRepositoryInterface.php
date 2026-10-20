<?php

declare(strict_types=1);

namespace App\Domain\Purchase\Repositories;

use App\Application\Purchase\DTOs\PurchaseDTO;
use App\Domain\Purchase\Entities\PurchaseEntity;
use DateTimeInterface;

interface PurchaseRepositoryInterface
{
    /**
     * Create (insert)
     */
    public function create(PurchaseDTO $value): PurchaseEntity;

    /**
     * Mengambil daftar pembelian
     *
     * @return PurchaseEntity[]
     */
    public function getByDate(DateTimeInterface $fromDate, DateTimeInterface $toDate): array;

    /**
     * Mengambil data berdasarkan id.
     */
    public function findById(string $id): ?PurchaseEntity;

    /**
     * Remove (soft delete).
     */
    public function remove(string $id): int;

    /**
     * update.
     */
    public function update(string $id, PurchaseDTO $value): int;
}
