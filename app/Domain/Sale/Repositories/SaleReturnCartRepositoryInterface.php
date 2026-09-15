<?php

declare(strict_types=1);

namespace App\Domain\Sale\Repositories;

use App\Application\Sale\DTOs\SaleReturnCartDTO;
use App\Domain\Sale\Entities\SaleReturnCartEntity;

interface SaleReturnCartRepositoryInterface
{
    /**
     * Membuat data keranjang pengembalian penjualan.
     */
    public function create(SaleReturnCartDTO $dto): SaleReturnCartEntity;

    /**
     * Menghapus permanen data kerangjang pengembalian belanja.
     */
    public function deleteById(string $id): int;

    /**
     * Memperbarui data keranjang pengambalian belanja.
     */
    public function update(string $id, SaleReturnCartDTO $dto): int;

    /**
     * Mengambil data keranjang pengembalian penjualan berdasarkan id user.
     */
    public function findByUserId(string $userId): ?SaleReturnCartEntity;

    /**
     * Mengambil data keranjang pengembalian pembelian berdasarkan id.
     */
    public function findById(string $id): ?SaleReturnCartEntity;
}
