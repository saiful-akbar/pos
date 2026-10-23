<?php

declare(strict_types=1);

namespace App\Domain\Report\Entities;

use App\Base\Entity;
use App\Domain\Purchase\Entities\PurchaseDetailEntity;
use App\Domain\Purchase\Entities\PurchaseEntity;
use App\Domain\Purchase\Entities\PurchasePaymentEntity;
use Override;

class PurchaseTransactionReportEntity extends Entity
{
    /**
     * Create a new class instance.
     *
     * @param PurchaseDetailEntity[]  $details
     * @param PurchasePaymentEntity[] $payments
     */
    public function __construct(
        public readonly PurchaseEntity $purchase,
        public readonly array $details,
        public readonly array $payments,
    ) {}

    /**
     * Sisa utang transaksi; pembayaran yang melebihi total dihitung 0.
     */
    public function outstanding(): float
    {
        return max($this->purchase->totalPrice - $this->purchase->alreadyPaid, 0.0);
    }

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'purchase' => $this->purchase->toArray(),
            'details' => array_map(fn (PurchaseDetailEntity $detail): array => $detail->toArray(), $this->details),
            'payments' => array_map(fn (PurchasePaymentEntity $payment): array => $payment->toArray(), $this->payments),
        ];
    }
}
