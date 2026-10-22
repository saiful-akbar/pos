<?php

declare(strict_types=1);

namespace App\Application\Sale\DTOs;

use App\Base\DTO;
use Override;

class SaleReturnCartDetailDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $saleReturnCartId,
        public readonly string $productId,
        public readonly string $unitId,
        public readonly float $multiplesQuantity,
        public readonly float $purchasePrice,
        public readonly float $sellingPrice,
        public readonly float $quantity,
        public readonly float $subtotal,
        public readonly float $discount,
        public readonly float $grandTotal,
    ) {}

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'sale_return_cart_id' => $this->saleReturnCartId,
            'product_id' => $this->productId,
            'unit_id' => $this->unitId,
            'multiples_quantity' => $this->multiplesQuantity,
            'purchase_price' => $this->purchasePrice,
            'selling_price' => $this->sellingPrice,
            'quantity' => $this->quantity,
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'grand_total' => $this->grandTotal,
        ];
    }
}
