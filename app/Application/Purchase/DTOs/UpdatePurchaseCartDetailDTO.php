<?php

declare(strict_types=1);

namespace App\Application\Purchase\DTOs;

use App\Base\DTO;
use Override;

class UpdatePurchaseCartDetailDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly int $purchasePrice,
        public readonly int $quantity,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'purchase_price' => $this->purchasePrice,
            'quantity' => $this->quantity,
        ];
    }
}
