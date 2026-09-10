<?php

declare(strict_types=1);

namespace App\Domain\Sale\Entities;

use App\Base\Entity;
use App\Domain\Purchase\Enums\PaymentMethodEnum;
use App\Domain\Sale\ValueObjects\CustomerSaleValue;
use App\Domain\Sale\ValueObjects\UserSaleValue;
use DateTimeInterface;
use Override;

class SaleEntity extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly UserSaleValue $user,
        public readonly ?CustomerSaleValue $customer,
        public readonly string $invoiceNumber,
        public readonly DateTimeInterface $transactionDate,
        public readonly int $totalQuantity,
        public readonly int $totalAmount,
        public readonly int $paidAmount,
        public readonly int $changeAmount,
        public readonly PaymentMethodEnum $paymentMethod,
        public readonly ?string $notes,
        public readonly ?DateTimeInterface $createdAt,
        public readonly ?DateTimeInterface $updatedAt,
        public readonly ?DateTimeInterface $deletedAt,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user' => $this->user->toArray(),
            'customer' => $this->customer?->toArray(),
            'invoice_number' => $this->invoiceNumber,
            'transaction_date' => $this->transactionDate->format('Y-m-d'),
            'total_quantity' => $this->totalQuantity,
            'total_amount' => $this->totalAmount,
            'paid_amount' => $this->paidAmount,
            'change_amount' => $this->changeAmount,
            'payment_method' => $this->paymentMethod->value,
            'notes' => $this->notes,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),

        ];
    }
}
