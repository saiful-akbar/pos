<?php

declare(strict_types=1);

namespace App\Domain\Sale\Repositories;

use App\Application\Sale\DTOs\CreateSaleDTO;
use App\Domain\Sale\Entities\SaleEntity;

interface SaleRepositoryInterface
{
    /**
     * Membuat invoice number.
     */
    public function generateInvoiceNumber(): string;

    /**
     * Menyimpan (insert) data penjualan.
     */
    public function create(CreateSaleDTO $dto): SaleEntity;
}
