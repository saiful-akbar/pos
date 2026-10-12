<?php

declare(strict_types=1);

namespace App\Domain\Product\Entities;

use App\Base\Entity;
use App\Domain\Product\ValueObjects\CategoryProductValue;
use DateTimeInterface;
use Override;

class ProductEntity extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly CategoryProductValue $category,
        public readonly string $code,
        public readonly ?string $name,
        public readonly ?string $image,
        public readonly float $minimumStock,
        public readonly float $currentStock,
        public readonly bool $isActive,
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
            'category' => $this->category->toArray(),
            'code' => $this->code,
            'name' => $this->name,
            'image' => $this->image,
            'minimum_stock' => $this->minimumStock,
            'current_stock' => $this->currentStock,
            'is_active' => $this->isActive,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
