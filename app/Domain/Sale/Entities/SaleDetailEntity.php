<?php

declare(strict_types=1);

namespace App\Domain\Sale\Entities;

use App\Base\Entity;
use App\Domain\Sale\ValueObjects\ProductSaleValue;
use DateTimeInterface;
use Override;

class SaleDetailEntity extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $saleId,
        public readonly ProductSaleValue $product,
        public readonly int $quantity,
        public readonly int $purchasePrice,
        public readonly int $sellingPrice,
        public readonly int $discount,
        public readonly int $subtotal,
        public readonly ?DateTimeInterface $createdAt,
        public readonly ?DateTimeInterface $updatedAt,
        public readonly ?DateTimeInterface $deletedAt,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'sale_id' => $this->saleId,
            'product' => $this->product->toArray(),
            'quantity' => $this->quantity,
            'purchase_price' => $this->purchasePrice,
            'selling_price' => $this->sellingPrice,
            'discount' => $this->discount,
            'subtotal' => $this->subtotal,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
