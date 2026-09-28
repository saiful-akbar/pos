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
        public readonly ?string $name = null,
        public readonly ?string $image = null,
        public readonly bool $isActive = true,
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
            'is_active' => $this->isActive,
            'category' => $this->category,
            'unit' => $this->unit,
        ];
    }
}
