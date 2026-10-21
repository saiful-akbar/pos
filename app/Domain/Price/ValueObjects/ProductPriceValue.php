<?php

declare(strict_types=1);

namespace App\Domain\Price\ValueObjects;

use App\Base\Entity;
use Override;

class ProductPriceValue extends Entity
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
}
