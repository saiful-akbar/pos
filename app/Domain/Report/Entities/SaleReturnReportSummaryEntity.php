<?php

declare(strict_types=1);

namespace App\Domain\Report\Entities;

use App\Base\Entity;
use Override;

class SaleReturnReportSummaryEntity extends Entity
{
    /**
     * Create a new class instance.
     *
     * @param int   $totalReturns  Jumlah dokumen pengembalian unik yang memenuhi filter.
     * @param float $totalValue    SUM(sale_returns.grand_total) (nilai pengembalian, bukan uang refund aktual).
     * @param float $totalQuantity SUM(quantity x multiples_quantity) detail (satuan dasar).
     * @param int   $totalProducts Jumlah produk unik yang dikembalikan pada dokumen yang memenuhi filter.
     * @param float $totalDiscount Nominal diskon: diskon item (subtotal - grand_total detail) + diskon penjualan (total_amount - grand_total).
     */
    public function __construct(
        public readonly int $totalReturns,
        public readonly float $totalValue,
        public readonly float $totalQuantity,
        public readonly int $totalProducts,
        public readonly float $totalDiscount,
    ) {}

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'total_returns' => $this->totalReturns,
            'total_value' => $this->totalValue,
            'total_quantity' => $this->totalQuantity,
            'total_products' => $this->totalProducts,
            'total_discount' => $this->totalDiscount,
        ];
    }
}
