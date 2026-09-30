<?php

declare(strict_types=1);

namespace App\Domain\Sale\Repositories;

use App\Application\Sale\DTOs\CreateSaleDetailDTO;
use App\Application\Sale\DTOs\GetSalesDTO;
use App\Domain\Sale\Entities\SaleDetailEntity;
use Illuminate\Database\Eloquent\Builder;

interface SaleDetailRepositoryInterface
{
    /**
     * Menyimpan (insert) data detail penjualan.
     */
    public function create(CreateSaleDetailDTO $dto): SaleDetailEntity;

    /**
     * Mengambil daftar detail sales berdasarkan sales id
     * dalam format query builder.
     */
    public function getBySaleIdQuery(string $saleId): Builder;

    /**
     * Mengambil data berdasarkan id sales dan id product.
     */
    public function findBySaleIdAndProductId(string $saleId, string $productId): ?SaleDetailEntity;

    /**
     * Mengambil daftar detail penjualan.
     *
     * @return SaleDetailEntity[]
     */
    public function get(GetSalesDTO $params): array;

    /**
     * Soft delete by sale id
     */
    public function removeBySaleId(string $saleId): int;

    /**
     * Mengambil daftar berdasarkan sale id
     *
     * @return SaleDetailEntity[]
     */
    public function getBySaleId(string $saleId): array;
}
