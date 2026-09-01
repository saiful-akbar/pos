<?php

declare(strict_types=1);

namespace App\Application\Purchase\DTOs;

use App\Base\DTO;
use Override;

class CreatePurchaseCartDetailDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $cartId,
        public readonly string $productId,
        public readonly int $purchasePrice,
        public readonly int $quantity
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'product_id' => $this->productId,
            'quantity' => $this->quantity,
            'purchase_price' => $this->purchasePrice,
        ];
    }
}
