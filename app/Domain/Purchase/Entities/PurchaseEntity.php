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
        public readonly ?string $notes,
        public readonly ?DateTimeInterface $createdAt,
        public readonly ?DateTimeInterface $updatedAt,
        public readonly ?DateTimeInterface $deletedAt,
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
            'purchase_date' => $this->purchaseDate,
            'total_quantity' => $this->totalQuantity,
            'total_price' => $this->totalPrice,
            'payment_status' => $this->paymentStatus->value,
            'notes' => $this->notes,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
