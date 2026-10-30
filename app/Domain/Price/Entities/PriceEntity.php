<?php

declare(strict_types=1);

namespace App\Domain\Price\Entities;

use App\Base\Entity;
use App\Domain\Price\ValueObjects\ProductPriceValue;
use App\Domain\Price\ValueObjects\UnitPriceValue;
use DateTimeInterface;
use Override;

class PriceEntity extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly ProductPriceValue $product,
        public readonly UnitPriceValue $unit,
        public readonly float $multiplesQuantity,
        public readonly float $purchasePrice,
        public readonly float $sellingPrice,
        public readonly bool $isDefault = false,
        public readonly ?DateTimeInterface $createdAt = null,
        public readonly ?DateTimeInterface $updatedAt = null,
        public readonly ?DateTimeInterface $deletedAt = null,
    ) {}

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'product' => $this->product->toArray(),
            'unit' => $this->unit->toArray(),
            'multiples_quantity' => $this->multiplesQuantity,
            'purchase_price' => $this->purchasePrice,
            'selling_price' => $this->sellingPrice,
            'is_default' => $this->isDefault,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
