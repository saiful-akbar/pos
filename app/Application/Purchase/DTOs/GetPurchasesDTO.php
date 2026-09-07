<?php

declare(strict_types=1);

namespace App\Application\Purchase\DTOs;

use App\Base\DTO;
use App\Domain\Purchase\Enums\PaymentStatusEnum;
use DateTimeInterface;
use Override;

class GetPurchasesDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly DateTimeInterface $fromDate,
        public readonly DateTimeInterface $toDate,
        public readonly ?PaymentStatusEnum $paymentStatus,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'from_date' => $this->fromDate->format('Y-m-d 00:00:00'),
            'to_date' => $this->toDate->format('Y-m-d 23:59:59'),
            'payment_status' => $this->paymentStatus?->value,
        ];
    }
}
