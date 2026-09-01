<?php

declare(strict_types=1);

namespace App\Application\Product\DTOs;

use App\Base\DTO;
use Override;

class UpdateProductDTO extends DTO
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
        public readonly int $purchasePrice,
        public readonly int $sellingPrice,
        public readonly int $discount,
        public readonly int $minimumStock,
        public readonly int $currentStock,
        public readonly bool $isActive,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'category_id' => $this->categoryId,
            'unit_id' => $this->unitId,
            'code' => $this->code,
            'barcode' => $this->barcode,
            'name' => $this->name,
            'purchase_price' => $this->purchasePrice,
            'selling_price' => $this->sellingPrice,
            'discount' => $this->discount,
            'minimum_stock' => $this->minimumStock,
            'current_stock' => $this->currentStock,
            'is_active' => $this->isActive,
        ];
    }
}
