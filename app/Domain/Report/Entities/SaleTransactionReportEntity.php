<?php

declare(strict_types=1);

namespace App\Domain\Report\Entities;

use App\Base\Entity;
use App\Domain\Sale\Entities\SaleDetailEntity;
use App\Domain\Sale\Entities\SaleEntity;
use Override;

class SaleTransactionReportEntity extends Entity
{
    /**
     * Create a new class instance.
     *
     * @param SaleDetailEntity[] $details
     */
    public function __construct(
        public readonly SaleEntity $sale,
        public readonly array $details,
    ) {}

    /**
     * Total harga sebelum diskon (jumlah subtotal detail).
     */
    public function itemsSubtotal(): float
    {
        return array_sum(array_map(fn (SaleDetailEntity $detail): float => $detail->subtotal, $this->details));
    }

    /**
     * Total diskon item (subtotal - grand total detail).
     */
    public function itemDiscount(): float
    {
        return array_sum(array_map(fn (SaleDetailEntity $detail): float => $detail->subtotal - $detail->grandTotal, $this->details));
    }

    /**
     * Nominal diskon pelanggan (total_amount - grand_total); kolom sales.discount sendiri berupa persen.
     */
    public function customerDiscount(): float
    {
        return $this->sale->totalAmount - $this->sale->grandTotal;
    }

    /**
     * Total quantity dalam satuan dasar (quantity x kelipatan).
     */
    public function baseQuantity(): float
    {
        return array_sum(array_map(fn (SaleDetailEntity $detail): float => $detail->quantity * $detail->multiplesQuantity, $this->details));
    }

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'sale' => $this->sale->toArray(),
            'details' => array_map(fn (SaleDetailEntity $detail): array => $detail->toArray(), $this->details),
        ];
    }
}
