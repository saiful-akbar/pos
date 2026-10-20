<?php

declare(strict_types=1);

namespace App\Application\Purchase\DTOs;

use App\Base\DTO;
use Override;

class PurchaseDetailDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $purchaseId,
        public readonly string $productId,
        public readonly string $unitId,
        public readonly float $multiplesQuantity,
        public readonly float $quantity,
        public readonly float $price,
        public readonly float $subtotal,
    ) {}

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'purchase_id' => $this->purchaseId,
            'product_id' => $this->productId,
            'unit_id' => $this->unitId,
            'multiples_quantity' => $this->multiplesQuantity,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'subtotal' => $this->subtotal,
        ];
    }
}
