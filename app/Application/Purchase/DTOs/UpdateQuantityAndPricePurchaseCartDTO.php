<?php

declare(strict_types=1);

namespace App\Application\Purchase\DTOs;

use App\Base\DTO;
use Override;

class UpdateQuantityAndPricePurchaseCartDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly int|float $totalQuantity,
        public readonly int|float $totalPrice,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'total_quantity' => $this->totalQuantity,
            'total_price' => $this->totalPrice,
        ];
    }
}
