<?php

declare(strict_types=1);

namespace App\Domain\StockOpname\ValueObjects;

use App\Base\Entity;
use Override;

class ProductStockOpnameValue extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly CategoryProductStockOpnameValue $category,
        public readonly UnitProductStockOpnameValue $unit,
        public readonly string $code,
        public readonly string $barcode,
        public readonly string $name,
        public readonly ?string $image,
        public readonly float|int $purchasePrice,
        public readonly float|int $sellingPrice,
        public readonly float|int $discount,
        public readonly float|int $minimumStock,
        public readonly float|int $currentStock,
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
            'category' => $this->category->toArray(),
            'unit' => $this->unit->toArray(),
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
        ];
    }
}
