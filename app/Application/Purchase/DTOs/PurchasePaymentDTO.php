<?php

declare(strict_types=1);

namespace App\Application\Purchase\DTOs;

use App\Base\DTO;
use App\Domain\Purchase\Enums\PaymentMethodEnum;
use DateTimeInterface;
use Override;

class PurchasePaymentDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $purchaseId,
        public readonly string $userId,
        public readonly DateTimeInterface $paymentDate,
        public readonly PaymentMethodEnum $paymentMethod,
        public readonly float $amount,
        public readonly ?string $notes,
    ) {}

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'purchase_id' => $this->purchaseId,
            'user_id' => $this->userId,
            'payment_date' => $this->paymentDate->format('Y-m-d'),
            'payment_method' => $this->paymentMethod->value,
            'amount' => $this->amount,
            'notes' => $this->notes,
        ];
    }
}
