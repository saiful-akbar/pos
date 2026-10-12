<?php

declare(strict_types=1);

namespace App\Application\Product\DTOs;

use App\Base\DTO;
use Override;

class ProductDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $categoryId,
        public readonly string $code,
        public readonly ?string $name = null,
        public readonly ?string $image = null,
        public readonly float $minimumStock = 0.00,
        public readonly float $currentStock = 0.00,
        public readonly bool $isActive = true,
    ) {}

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'category_id' => $this->categoryId,
            'code' => $this->code,
            'name' => $this->name,
            'image' => $this->image,
            'minimum_stock' => $this->minimumStock,
            'current_stock' => $this->currentStock,
            'is_active' => $this->isActive,
        ];
    }
}
