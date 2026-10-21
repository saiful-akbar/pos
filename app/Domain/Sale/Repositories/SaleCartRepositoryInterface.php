<?php

declare(strict_types=1);

namespace App\Domain\Sale\Repositories;

use App\Application\Sale\DTOs\SaleCartDTO;
use App\Domain\Sale\Entities\SaleCartEntity;

interface SaleCartRepositoryInterface
{
    /**
     * Mengambil data berdasarkan user_id.
     */
    public function findByUserId(string $userId): ?SaleCartEntity;

    /**
     * Mengambil data berdasarkan kolom.
     */
    public function find(string $column, string $value): ?SaleCartEntity;

    /**
     * Create (insert).
     */
    public function create(SaleCartDTO $value): SaleCartEntity;

    /**
     * Update.
     */
    public function update(string $id, SaleCartDTO $value): int;

    /**
     * Hapus permanen.
     */
    public function delete(string $id): int;
}
