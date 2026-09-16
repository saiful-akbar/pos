<?php

declare(strict_types=1);

namespace App\Domain\Sale\Repositories;

use App\Application\Sale\DTOs\SaleReturnDTO;
use App\Domain\Sale\Entities\SaleReturnEntity;

interface SaleReturnRepositoryInterface
{
    /**
     * Menyimpan data pengembalian penjualan.
     */
    public function create(SaleReturnDTO $dto): SaleReturnEntity;
}
