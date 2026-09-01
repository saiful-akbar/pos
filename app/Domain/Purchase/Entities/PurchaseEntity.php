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
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly UserPurchaseValue $user,
        public readonly ?SupplierPurchaseValue $supplier = null,
        public readonly ?string $invoiceNumber = null,
        public readonly int $totalAmount = 0,
        public readonly ?PaymentStatusEnum $paymentStatus = null,
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
            'supplier' => $this->supplier?->toArray(),
            'user' => $this->user?->toArray(),
            'invoice_number' => $this->invoiceNumber,
            'total_amount' => $this->totalAmount,
            'payment_status' => $this->paymentStatus,
            'notes' => $this->notes,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
