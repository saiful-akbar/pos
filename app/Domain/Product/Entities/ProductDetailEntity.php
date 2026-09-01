<?php

declare(strict_types=1);

namespace App\Domain\Product\Entities;

use App\Base\Entity;
use App\Domain\Product\ValueObjects\CategoryProductValue;
use App\Domain\Product\ValueObjects\UnitProductValue;
use DateTimeInterface;
use Override;

class ProductDetailEntity extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly CategoryProductValue $category,
        public readonly UnitProductValue $unit,
        public readonly string $code,
        public readonly string $barcode,
        public readonly ?string $name,
        public readonly int $purchasePrice,
        public readonly int $sellingPrice,
        public readonly int $discount,
        public readonly int $minimumStock,
        public readonly int $currentStock,
        public readonly bool $isActive,
        public readonly ?string $image = null,
        public readonly ?DateTimeInterface $createdAt = null,
        public readonly ?DateTimeInterface $updatedAt = null,
        public readonly ?DateTimeInterface $deletedAt = null,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'category' => $this->category->toArray(),
            'unit' => $this->unit->toArray(),
            'code' => $this->code,
            'barcode' => $this->barcode,
            'name' => $this->name,
            'purcahse_price' => $this->purchasePrice,
            'selling_price' => $this->sellingPrice,
            'minimum_stock' => $this->minimumStock,
            'discount' => $this->discount,
            'current_stock' => $this->currentStock,
            'is_active' => $this->isActive,
            'image' => $this->image,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
