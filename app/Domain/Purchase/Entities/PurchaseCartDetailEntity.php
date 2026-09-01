<?php

declare(strict_types=1);

namespace App\Domain\Purchase\Entities;

use App\Base\Entity;
use App\Domain\Purchase\ValueObjects\ProductPurchaseValue;
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
        public readonly int $quantity,
        public readonly int $purchasePrice,
        public readonly int $subtotalPrice,
        public readonly ?DateTimeInterface $createdAt,
        public readonly ?DateTimeInterface $updatedAt,
        public readonly ?DateTimeInterface $deletedAt,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'purchase_cart_id' => $this->purchaseCartId,
            'product' => $this->product->toArray(),
            'quantity' => $this->quantity,
            'purchase_price' => $this->purchasePrice,
            'subtotal_price' => $this->subtotalPrice,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
