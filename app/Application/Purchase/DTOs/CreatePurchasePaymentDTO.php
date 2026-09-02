<?php

declare(strict_types=1);

namespace App\Application\Purchase\DTOs;

use App\Base\DTO;
use App\Domain\Purchase\Enums\PaymentMethodEnum;
use DateTimeInterface;
use Override;

class CreatePurchasePaymentDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $purchaseId,
        public readonly string $userId,
        public readonly DateTimeInterface $paymentDate,
        public readonly PaymentMethodEnum $paymentMethod,
        public readonly int $amount,
        public readonly ?string $referencesNumber = null,
        public readonly ?string $notes = null,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'purchase_id' => $this->purchaseId,
            'user_id' => $this->userId,
            'payment_date' => $this->paymentDate->format('Y-m-d'),
            'payment_method' => $this->paymentMethod->value,
            'amount' => $this->amount,
            'references_number' => $this->referencesNumber,
            'notes' => $this->notes,
        ];
    }
}
