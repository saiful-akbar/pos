<?php

declare(strict_types=1);

namespace App\Application\Purchase\DTOs;

use App\Base\DTO;
use Override;

class AddProductPurchaseDTO extends DTO
{
    /**
     * ID product
     */
    public readonly string $productId;

    /**
     * Harga beli
     */
    public readonly int $price;

    /**
     * quantity
     */
    public readonly int $quantity;

    /**
     * subtotal amount
     */
    public readonly int $subtotal;

    /**
     * Create a new class instance.
     */
    public function __construct(string $productId, int $price, int $quantity)
    {
        $this->productId = $productId;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->subtotal = $price * $quantity;
    }

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'product_id' => $this->productId,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'subtotal' => $this->subtotal,
        ];
    }
}
