<?php

declare(strict_types=1);

namespace App\Domain\Purchase\ValueObjects;

use App\Base\Entity;
use Override;

class ProductPurchaseValue extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $code,
        public readonly string $barcode,
        public readonly ?string $name,
        public readonly ?string $image,
        public readonly int|float $purchasePrice,
        public readonly int|float $sellingPrice,
        public readonly int|float $discount,
        public readonly int|float $minimumStock,
        public readonly int|float $currentStock,
        public readonly bool $isActive,
        public readonly ?CategoryProductPurchaseValue $category = null,
        public readonly ?UnitProductPurchaseValue $unit = null,
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
            'barcode' => $this->barcode,
            'name' => $this->name,
            'image' => $this->image,
            'purchase_price' => $this->purchasePrice,
            'selling_price' => $this->sellingPrice,
            'discount' => $this->discount,
            'minimum_stock' => $this->minimumStock,
            'current_stock' => $this->currentStock,
            'is_active' => $this->isActive,
            'category' => $this->category,
            'unit' => $this->unit,
        ];
    }
}
