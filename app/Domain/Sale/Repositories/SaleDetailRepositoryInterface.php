<?php

declare(strict_types=1);

namespace App\Domain\Sale\Repositories;

use App\Application\Sale\DTOs\SaleDetailDTO;
use App\Domain\Sale\Entities\SaleDetailEntity;

interface SaleDetailRepositoryInterface
{
    /**
     * Mencari data sale detail berdasarkan ID.
     */
    public function findById(string $id): ?SaleDetailEntity;

    /**
     * Membuat data sale detail baru.
     */
    public function create(SaleDetailDTO $value): SaleDetailEntity;

    /**
     * Menghapus data sale detail berdasarkan ID.
     */
    public function deleteById(string $id): int;
}
