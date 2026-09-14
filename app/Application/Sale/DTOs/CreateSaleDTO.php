<?php

declare(strict_types=1);

namespace App\Application\Sale\DTOs;

use App\Base\DTO;
use App\Domain\Purchase\Enums\PaymentMethodEnum;
use DateTimeInterface;
use Override;

class CreateSaleDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $userId,
        public readonly ?string $customerId,
        public readonly string $invoiceNumber,
        public readonly DateTimeInterface $transactionDate,
        public readonly int|float $totalQuantity,
        public readonly int|float $totalAmount,
        public readonly int|float $paidAmount,
        public readonly int|float $changeAmount,
        public readonly PaymentMethodEnum $paymentMethod,
        public readonly ?string $notes,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'customer_id' => $this->customerId,
            'invoice_number' => $this->invoiceNumber,
            'transaction_date' => $this->transactionDate->format('Y-m-d'),
            'total_quantity' => $this->totalQuantity,
            'total_amount' => $this->totalAmount,
            'paid_amount' => $this->paidAmount,
            'change_amount' => $this->changeAmount,
            'payment_method' => $this->paymentMethod->value,
            'notes' => $this->notes,

        ];
    }
}
