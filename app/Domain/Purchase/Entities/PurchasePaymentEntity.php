<?php

declare(strict_types=1);

namespace App\Domain\Purchase\Entities;

use App\Base\Entity;
use App\Domain\Purchase\Enums\PaymentMethodEnum;
use App\Domain\Purchase\ValueObjects\UserPurchaseValue;
use DateTimeInterface;
use Override;

class PurchasePaymentEntity extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $purchaseId,
        public readonly UserPurchaseValue $user,
        public readonly DateTimeInterface $paymentDate,
        public readonly PaymentMethodEnum $paymentMethod,
        public readonly int $amount,
        public readonly ?string $referenceNumber = null,
        public readonly ?string $notes = null,
        public readonly ?DateTimeInterface $createdAt = null,
        public readonly ?DateTimeInterface $updatedAt = null,
        public readonly ?DateTimeInterface $deletedAt = null,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'purchase_id' => $this->purchaseId,
            'user' => $this->user->toArray(),
            'payment_date' => $this->paymentDate,
            'payment_method' => $this->paymentMethod->value,
            'amount' => $this->amount,
            'reference_number' => $this->referenceNumber,
            'notes' => $this->notes,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
