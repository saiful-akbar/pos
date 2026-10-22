<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Domain\Sale\Entities\SaleDetailEntity;
use App\Domain\Sale\Repositories\SaleDetailRepositoryInterface;

class GetSaleDetailsBySaleId
{
    public function __construct(
        private SaleDetailRepositoryInterface $saleDetailRepository,
    ) {}

    /**
     * Mengambil daftar detail penjualan berdasarkan ID penjualan.
     *
     * @return array<SaleDetailEntity>
     */
    public function execute(string $saleId): array
    {
        return $this->saleDetailRepository->getBySaleId(
            saleId: $saleId,
        );
    }
}
