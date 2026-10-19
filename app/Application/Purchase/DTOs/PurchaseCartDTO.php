<?php

declare(strict_types=1);

namespace App\Application\Purchase\DTOs;

use App\Base\DTO;
use DateTimeInterface;
use Override;

class PurchaseCartDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $userId,
        public readonly DateTimeInterface $transactionDate,
        public readonly float $totalProduct,
        public readonly float $totalQuantity,
        public readonly float $totalPrice,
    ) {}

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'transaction_date' => $this->transactionDate->format('Y-m-d'),
            'total_product' => $this->totalProduct,
            'total_quantity' => $this->totalQuantity,
            'total_price' => $this->totalPrice,
        ];
    }
}
