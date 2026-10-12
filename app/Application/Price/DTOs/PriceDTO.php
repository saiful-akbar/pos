<?php

declare(strict_types=1);

namespace App\Application\Price\DTOs;

use App\Base\DTO;
use Override;

class PriceDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $productId,
        public readonly string $unitId,
        public readonly float $multiplesQuantity,
        public readonly float $purchasePrice,
        public readonly float $sellingPrice,
        public readonly bool $isDefault = false,
    ) {}

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'product_id' => $this->productId,
            'unit_id' => $this->unitId,
            'multiples_quantity' => $this->multiplesQuantity,
            'purchase_price' => $this->purchasePrice,
            'selling_price' => $this->sellingPrice,
            'is_default' => $this->isDefault,
        ];
    }
}
