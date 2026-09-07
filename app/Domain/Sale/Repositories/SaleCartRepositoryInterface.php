<?php

declare(strict_types=1);

namespace App\Domain\Sale\Repositories;

use App\Application\Sale\DTOs\CreateSaleCartDTO;
use App\Domain\Sale\Entities\SaleCartEntity;

interface SaleCartRepositoryInterface
{
    /**
     * Mengambil data keranjang penjualan berdasarkan id user.
     */
    public function findByUserId(string $userId): ?SaleCartEntity;

    /**
     * Mengambil data keranjang penjualan berdasarkan id.
     */
    public function findById(string $id): ?SaleCartEntity;

    /**
     * Membuat kerangjang belanja baru
     */
    public function create(CreateSaleCartDTO $dto): SaleCartEntity;

    /**
     * Menghapus keranjang belanja.
     */
    public function deleteById(string $id): void;
}
