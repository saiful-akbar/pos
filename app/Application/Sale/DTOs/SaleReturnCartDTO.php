<?php

declare(strict_types=1);

namespace App\Application\Sale\DTOs;

use App\Base\DTO;
use DateTimeInterface;
use Override;

class SaleReturnCartDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $saleId,
        public readonly string $userId,
        public readonly DateTimeInterface $transactionDate,
        public readonly int|float $totalQuantity,
        public readonly int|float $totalAmount,
        public readonly ?string $notes,
    ) {}

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'sale_id' => $this->saleId,
            'user_id' => $this->userId,
            'transaction_date' => $this->transactionDate->format('Y-m-d'),
            'total_quantity' => $this->totalQuantity,
            'total_amount' => $this->totalAmount,
            'notes' => $this->notes,
        ];
    }
}
