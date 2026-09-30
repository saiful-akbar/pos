<?php

declare(strict_types=1);

namespace App\Domain\Sale\Repositories;

use App\Application\Sale\DTOs\GetSaleReturnDTO;
use App\Application\Sale\DTOs\SaleReturnDTO;
use App\Domain\Sale\Entities\SaleReturnEntity;

interface SaleReturnRepositoryInterface
{
    /**
     * Menyimpan data pengembalian penjualan.
     */
    public function create(SaleReturnDTO $dto): SaleReturnEntity;

    /**
     * Mengmabil daftar pengembalian penjualan
     *
     * @return SaleReturnEntity[]
     */
    public function get(GetSaleReturnDTO $params): array;

    /**
     * Generate nomor return.
     */
    public function generateReturnNumber(): string;

    /**
     * Soft delete by id.
     */
    public function removeById(string $id): int;

    /**
     * Mengambil data berdasarkan id.
     */
    public function findById(string $id): ?SaleReturnEntity;
}
