<?php

declare(strict_types=1);

namespace App\Application\Price\DTOs;

use App\Base\DTO;
use Override;

class UpdatePriceDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $productId,
        public readonly float $multiplesQuantity,
        public readonly float $purchasePrice,
        public readonly float $sellingPrice,
        public readonly bool $isDefault = false,
    ) {}

    /**
     * Konversi data object ke array (kolom yang diperbarui).
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'multiples_quantity' => $this->multiplesQuantity,
            'purchase_price' => $this->purchasePrice,
            'selling_price' => $this->sellingPrice,
            'is_default' => $this->isDefault,
        ];
    }
}
