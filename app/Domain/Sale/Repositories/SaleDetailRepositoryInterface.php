<?php

declare(strict_types=1);

namespace App\Domain\Sale\Repositories;

use App\Application\Sale\DTOs\CreateSaleDetailDTO;
use App\Domain\Sale\Entities\SaleDetailEntity;

interface SaleDetailRepositoryInterface
{
    /**
     * Menyimpan (insert) data detail penjualan.
     */
    public function create(CreateSaleDetailDTO $dto): SaleDetailEntity;
}
