<?php

declare(strict_types=1);

namespace App\Domain\Sale\ValueObjects;

use App\Base\Entity;
use Override;

class ProductSaleValue extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly UnitProductSaleValue $unit,
        public readonly CategoryProductSaleValue $category,
        public readonly string $code,
        public readonly string $barcode,
        public readonly ?string $name,
        public readonly ?string $image,
        public readonly int $purchasePrice,
        public readonly int $sellingPrice,
        public readonly int $discount,
        public readonly int $currentStock,
        public readonly int $minimumStock,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'unit' => $this->unit->toArray(),
            'category' => $this->category->toArray(),
            'code' => $this->code,
            'barcode' => $this->barcode,
            'name' => $this->name,
            'image' => $this->image,
            'purchase_price' => $this->purchasePrice,
            'selling_price' => $this->sellingPrice,
            'discount' => $this->discount,
            'current_stock' => $this->currentStock,
            'minimum_stock' => $this->minimumStock,
        ];
    }
}
