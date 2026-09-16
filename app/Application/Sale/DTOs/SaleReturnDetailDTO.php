<?php

declare(strict_types=1);

namespace App\Application\Sale\DTOs;

use App\Base\DTO;
use Override;

class SaleReturnDetailDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $saleReturnId,
        public readonly string $productId,
        public readonly int|float $quantity,
        public readonly int|float $purchasePrice,
        public readonly int|float $sellingPrice,
        public readonly int|float $discount,
        public readonly int|float $subtotal,
    ) {}

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'sale_return_id' => $this->saleReturnId,
            'product_id' => $this->productId,
            'quantity' => $this->quantity,
            'purchase_price' => $this->purchasePrice,
            'selling_price' => $this->sellingPrice,
            'discount' => $this->discount,
            'subtotal' => $this->subtotal,
        ];
    }
}
