<?php

declare(strict_types=1);

namespace App\Application\Sale\DTOs;

use App\Base\DTO;
use Override;

class CreateSaleDetailDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $saleId,
        public readonly string $productId,
        public readonly int $quantity,
        public readonly int $purchasePrice,
        public readonly int $sellingPrice,
        public readonly int $discount,
        public readonly int $subtotal,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'sale_id' => $this->saleId,
            'product_id' => $this->productId,
            'quantity' => $this->quantity,
            'purchase_price' => $this->purchasePrice,
            'selling_price' => $this->sellingPrice,
            'discount' => $this->discount,
            'subtotal' => $this->subtotal,
        ];
    }
}
