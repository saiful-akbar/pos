<?php

declare(strict_types=1);

namespace App\Application\Purchase\DTOs;

use App\Base\DTO;
use Override;

class UpdatePurchaseCartDetailDTO extends DTO
{
    /**
     * ID purchase cart detail
     */
    public readonly string $id;

    /**
     * quantity
     */
    public readonly int|float $quantity;

    /**
     * harga pembelian
     */
    public readonly int|float $purchasePrice;

    /**
     * Subtotal harga pembelian.
     */
    public readonly int|float $subtotalPrice;

    /**
     * Create a new class instance.
     */
    public function __construct(
        string $id,
        int|float $quantity,
        int|float $purchasePrice,
    ) {
        $this->id = $id;
        $this->quantity = $quantity;
        $this->purchasePrice = $purchasePrice;
        $this->subtotalPrice = $quantity * $purchasePrice;
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'quantity' => $this->quantity,
            'purchase_price' => $this->purchasePrice,
            'subtotal_price' => $this->subtotalPrice,
        ];
    }
}
