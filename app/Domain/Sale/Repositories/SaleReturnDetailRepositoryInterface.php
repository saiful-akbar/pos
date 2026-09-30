<?php

declare(strict_types=1);

namespace App\Domain\Sale\Repositories;

use App\Application\Sale\DTOs\GetSaleReturnDTO;
use App\Application\Sale\DTOs\SaleReturnDetailDTO;
use App\Domain\Sale\Entities\SaleReturnDetailEntity;

interface SaleReturnDetailRepositoryInterface
{
    /**
     * Menyimpan (insert) data detail (produk) pengembalian penjualan.
     */
    public function create(SaleReturnDetailDTO $dto): SaleReturnDetailEntity;

    /**
     * Mengambil daftar data detail pengembalian pembelian.
     *
     * @return SaleReturnDetailEntity[]
     */
    public function get(GetSaleReturnDTO $params): array;

    /**
     * Soft delete by sale return id
     */
    public function removeBySaleReturnId(string $saleReturnId): int;

    /**
     * Mengambil daftar data detail pengembalian pembelian
     * berdasarkan sale reutrn id.
     *
     * @return SaleReturnDetailEntity[]
     */
    public function getBySaleReturnId(string $saleReturnId): array;
}
