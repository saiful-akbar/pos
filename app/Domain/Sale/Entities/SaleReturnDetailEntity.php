<?php

declare(strict_types=1);

namespace App\Domain\Sale\Entities;

use App\Base\Entity;
use App\Domain\Sale\ValueObjects\ProductSaleValue;
use DateTimeInterface;
use Override;

class SaleReturnDetailEntity extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $saleReturnId,
        public readonly ProductSaleValue $product,
        public readonly int|float $quantity,
        public readonly int|float $purchasePrice,
        public readonly int|float $sellingPrice,
        public readonly int|float $discount,
        public readonly int|float $subtotal,
        public readonly ?DateTimeInterface $createdAt = null,
        public readonly ?DateTimeInterface $updatedAt = null,
        public readonly ?DateTimeInterface $deletedAt = null,
        public readonly ?SaleReturnEntity $saleReturn = null,
    ) {}

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'sale_return_id' => $this->saleReturnId,
            'product' => $this->product->toArray(),
            'quantity' => $this->quantity,
            'purchase_price' => $this->purchasePrice,
            'selling_price' => $this->sellingPrice,
            'discount' => $this->discount,
            'subtotal' => $this->subtotal,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
            'sale_return' => $this->saleReturn?->toArray(),
        ];
    }
}
