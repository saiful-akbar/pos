<?php

declare(strict_types=1);

namespace App\Domain\Sale\Repositories;

use App\Application\Sale\DTOs\SaleReturnDetailDTO;
use App\Domain\Sale\Entities\SaleReturnDetailEntity;

interface SaleReturnDetailRepositoryInterface
{
    /**
     * Membuat (insert) data sale return detail baru.
     */
    public function create(SaleReturnDetailDTO $value): SaleReturnDetailEntity;

    /**
     * Menghapus (soft delete) seluruh data sale return detail berdasarkan sale_return_id.
     */
    public function removeBySaleReturnId(string $saleReturnId): int;
}
