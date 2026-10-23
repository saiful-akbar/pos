<?php

declare(strict_types=1);

namespace App\Domain\Report\Entities;

use App\Base\Entity;
use Override;

class PurchaseReportSummaryEntity extends Entity
{
    /**
     * Create a new class instance.
     *
     * @param int   $totalTransactions Jumlah transaksi aktif yang memenuhi filter.
     * @param float $totalPurchase     SUM(total_price).
     * @param float $totalPaid         SUM(already_paid).
     * @param float $totalOutstanding  jumlah sisa utang per transaksi; transaksi yang dibayar melebihi total dihitung 0.
     * @param int   $overpaidCount     Jumlah transaksi dibayar melebihi total (sisa utangnya dihitung 0).
     */
    public function __construct(
        public readonly int $totalTransactions,
        public readonly float $totalPurchase,
        public readonly float $totalPaid,
        public readonly float $totalOutstanding,
        public readonly int $overpaidCount,
    ) {}

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'total_transactions' => $this->totalTransactions,
            'total_purchase' => $this->totalPurchase,
            'total_paid' => $this->totalPaid,
            'total_outstanding' => $this->totalOutstanding,
            'overpaid_count' => $this->overpaidCount,
        ];
    }
}
