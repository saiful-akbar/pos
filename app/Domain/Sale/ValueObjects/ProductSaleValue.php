<?php

declare(strict_types=1);

namespace App\Domain\Sale\ValueObjects;

use App\Base\Entity;
use App\Models\Product;
use Override;

class ProductSaleValue extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $code,
        public readonly ?string $name,
        public readonly ?string $image,
        public readonly float $minimumStock,
        public readonly float $currentStock,
        public readonly float $discount,
        public readonly bool $isActive,
    ) {}

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'image' => $this->image,
            'minimum_stock' => $this->minimumStock,
            'current_stock' => $this->currentStock,
            'discount' => $this->discount,
            'is_active' => $this->isActive,
        ];
    }

    /**
     * Membuat instance dari model.
     */
    public static function fromModel(Product $model): self
    {
        return new self(
            id: $model->id,
            code: $model->code,
            name: $model->name,
            image: $model->image,
            minimumStock: parse_float($model->minimum_stock),
            currentStock: parse_float($model->current_stock),
            discount: parse_float($model->discount),
            isActive: $model->is_active === 1,
        );
    }
}
