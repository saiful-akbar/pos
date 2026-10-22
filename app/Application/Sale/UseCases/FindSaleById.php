<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Domain\Sale\Entities\SaleEntity;
use App\Domain\Sale\Repositories\SaleRepositoryInterface;

class FindSaleById
{
    public function __construct(
        private SaleRepositoryInterface $saleRepository,
    ) {}

    /**
     * Mengambil data penjualan berdasarkan ID.
     */
    public function execute(string $id): ?SaleEntity
    {
        return $this->saleRepository->findById(
            id: $id,
        );
    }
}
