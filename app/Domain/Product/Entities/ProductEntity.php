<?php

declare(strict_types=1);

namespace App\Domain\Product\Entities;

use App\Base\Entity;
use DateTimeInterface;
use Override;

class ProductEntity extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $categoryId,
        public readonly string $unitId,
        public readonly string $code,
        public readonly string $barcode,
        public readonly ?string $name,
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
            'category_id' => $this->categoryId,
            'unit_id' => $this->unitId,
            'code' => $this->code,
            'barcode' => $this->barcode,
            'name' => $this->name,
            'selling_price' => $this->sellingPrice,
            'discount' => $this->discount,
            'minimum_stock' => $this->minimumStock,
            'current_stock' => $this->currentStock,
            'is_active' => $this->isActive,
            'image' => $this->image,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
