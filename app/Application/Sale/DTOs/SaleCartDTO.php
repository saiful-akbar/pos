<?php

declare(strict_types=1);

namespace App\Application\Sale\DTOs;

use App\Base\DTO;
use DateTimeInterface;
use Override;

class SaleCartDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $userId,
        public readonly bool $isPending,
        public readonly DateTimeInterface $transactionDate,
        public readonly int $totalQuantity,
        public readonly int $totalAmount,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'is_pending' => $this->isPending,
            'transaction_date' => $this->transactionDate->format('Y-m-d H:i:s'),
            'total_quantity' => $this->totalQuantity,
            'total_amount' => $this->totalAmount,
        ];
    }
}
