<?php

declare(strict_types=1);

namespace App\Application\Purchase\DTOs;

use App\Base\DTO;
use Override;

class CreatePurchaseCartDetailDTO extends DTO
{
    /**
     * ID product
     */
    public readonly string $productId;

    /**
     * Quantity
     */
    public readonly int $quantity;

    /**
     * Harga beli
     */
    public readonly int $purchasePrice;

    /**
     * Subtotal harga
     */
    public readonly int $subtotalPrice;

    /**
     * Create a new class instance.
     */
    public function __construct(string $productId, int $purchasePrice = 0, int $quantity = 1)
    {
        $this->productId = $productId;
        $this->purchasePrice = $purchasePrice;
        $this->quantity = $quantity;
        $this->subtotalPrice = $quantity * $purchasePrice;
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'product_id' => $this->productId,
            'quantity' => $this->quantity,
            'purchase_price' => $this->purchasePrice,
            'subtotal_price' => $this->subtotalPrice,
        ];
    }
}
