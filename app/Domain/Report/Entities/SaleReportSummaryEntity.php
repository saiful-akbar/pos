<?php

declare(strict_types=1);

namespace App\Domain\Report\Entities;

use App\Base\Entity;
use Override;

class SaleReportSummaryEntity extends Entity
{
    /**
     * Create a new class instance.
     *
     * @param int   $totalTransactions Jumlah transaksi penjualan unik yang memenuhi filter.
     * @param float $totalSales        SUM(sales.grand_total).
     * @param float $totalProductsSold SUM(quantity x multiples_quantity) detail penjualan (satuan dasar).
     * @param float $totalDiscount     Nominal diskon: diskon item (subtotal - grand_total detail) + diskon pelanggan (total_amount - grand_total).
     */
    public function __construct(
        public readonly int $totalTransactions,
        public readonly float $totalSales,
        public readonly float $totalProductsSold,
        public readonly float $totalDiscount,
    ) {}

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'total_transactions' => $this->totalTransactions,
            'total_sales' => $this->totalSales,
            'total_products_sold' => $this->totalProductsSold,
            'total_discount' => $this->totalDiscount,
        ];
    }
}
