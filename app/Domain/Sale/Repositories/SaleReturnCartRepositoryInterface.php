<?php

declare(strict_types=1);

namespace App\Domain\Sale\Repositories;

use App\Application\Sale\DTOs\SaleReturnCartDTO;
use App\Domain\Sale\Entities\SaleReturnCartEntity;

interface SaleReturnCartRepositoryInterface
{
    /**
     * Mengambil data berdasarkan ID.
     */
    public function findById(string $id): ?SaleReturnCartEntity;

    /**
     * Mengambil data berdasarkan kolom dan value.
     */
    public function find(string $column, string|int|float|bool|null $value): ?SaleReturnCartEntity;

    /**
     * Membuat data sale return cart baru.
     */
    public function create(SaleReturnCartDTO $value): SaleReturnCartEntity;

    /**
     * Menghapus data sale return cart berdasarkan user_id.
     */
    public function deleteByUserId(string $userId): int;

    /**
     * Menghapus permanen data sale return cart berdasarkan ID.
     */
    public function deleteById(string $id): int;

    /**
     * Update data sale return cart berdasarkan ID.
     */
    public function update(string $id, SaleReturnCartDTO $value): int;
}
