<?php

declare(strict_types=1);

namespace App\Application\Report\UseCases;

use App\Domain\Report\Entities\SaleTransactionReportEntity;
use App\Domain\Sale\Repositories\SaleDetailRepositoryInterface;
use App\Domain\Sale\Repositories\SaleRepositoryInterface;

class GetSaleTransactionReport
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleRepositoryInterface $saleRepository,
        private SaleDetailRepositoryInterface $saleDetailRepository,
    ) {}

    /**
     * Mengambil satu transaksi penjualan beserta detail produknya (untuk cetak pdf).
     */
    public function execute(string $saleId): ?SaleTransactionReportEntity
    {
        $sale = $this->saleRepository->findById(id: $saleId);

        if ($sale === null) {
            return null;
        }

        return new SaleTransactionReportEntity(
            sale: $sale,
            details: $this->saleDetailRepository->getBySaleId(saleId: $sale->id),
        );
    }
}
