<?php

declare(strict_types=1);

namespace App\Application\Purchase\DTOs;

use App\Base\DTO;
use Override;

class CreatePurchaseDetailDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $purchaseId,
        public readonly string $productId,
        public readonly int|float $quantity,
        public readonly int|float $purchasePrice,
        public readonly int|float $subtotalPrice,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'purchase_id' => $this->purchaseId,
            'product_id' => $this->productId,
            'quantity' => $this->quantity,
            'purchase_price' => $this->purchasePrice,
            'subtotal_price' => $this->subtotalPrice,
        ];
    }
}
