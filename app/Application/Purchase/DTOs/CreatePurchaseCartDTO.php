<?php

declare(strict_types=1);

namespace App\Application\Purchase\DTOs;

use App\Base\DTO;
use DateTimeInterface;
use Override;

class CreatePurchaseCartDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $userId,
        public readonly DateTimeInterface $purchaseDate,
        public readonly int $totalQuantity,
        public readonly int $totalPrice,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'purchase_date' => $this->purchaseDate->format('Y-m-d'),
            'total_quantity' => $this->totalQuantity,
            'total_price' => $this->totalPrice,
        ];
    }
}
