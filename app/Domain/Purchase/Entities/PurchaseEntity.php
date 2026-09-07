<?php

declare(strict_types=1);

namespace App\Domain\Purchase\Entities;

use App\Base\Entity;
use App\Domain\Purchase\Enums\PaymentStatusEnum;
use App\Domain\Purchase\ValueObjects\SupplierPurchaseValue;
use App\Domain\Purchase\ValueObjects\UserPurchaseValue;
use DateTimeInterface;
use Override;

class PurchaseEntity extends Entity
{
    /**
     * Constructor.
     */
    public function __construct(
        public readonly string $id,
        public readonly UserPurchaseValue $user,
        public readonly SupplierPurchaseValue $supplier,
        public readonly DateTimeInterface $purchaseDate,
        public readonly ?string $invoiceNumber,
        public readonly int $totalQuantity,
        public readonly int $totalPrice,
        public readonly PaymentStatusEnum $paymentStatus,
        public readonly int $amountPaid = 0,
        public readonly ?string $notes = null,
        public readonly ?DateTimeInterface $createdAt = null,
        public readonly ?DateTimeInterface $updatedAt = null,
        public readonly ?DateTimeInterface $deletedAt = null,
    ) {}

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user' => $this->user->toArray(),
            'supplier' => $this->supplier->toArray(),
            'invoice_number' => $this->invoiceNumber,
            'purchase_date' => $this->purchaseDate->format('Y-m-d'),
            'total_quantity' => $this->totalQuantity,
            'total_price' => $this->totalPrice,
            'payment_status' => $this->paymentStatus->value,
            'amount_paid' => $this->amountPaid,
            'notes' => $this->notes,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
