<?php

declare(strict_types=1);

namespace App\Domain\Purchase\Entities;

use App\Base\Entity;
use App\Domain\Purchase\ValueObjects\ProductPurchaseValue;
use App\Domain\Purchase\ValueObjects\UnitPurchaseValue;
use DateTimeInterface;
use Override;

class PurchaseCartDetailEntity extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $purchaseCartId,
        public readonly ProductPurchaseValue $product,
        public readonly UnitPurchaseValue $unit,
        public readonly float $multiplesQuantity,
        public readonly float $quantity,
        public readonly float $price,
        public readonly float $subtotal,
        public readonly ?DateTimeInterface $createdAt,
        public readonly ?DateTimeInterface $updatedAt,
        public readonly ?DateTimeInterface $deletedAt,
    ) {}

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'purchase_cart_id' => $this->purchaseCartId,
            'product' => $this->product->toArray(),
            'unit' => $this->unit->toArray(),
            'multiples_quantity' => $this->multiplesQuantity,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'subtotal' => $this->subtotal,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
