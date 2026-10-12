<?php

declare(strict_types=1);

namespace App\Application\Price\DTOs;

use App\Base\DTO;
use Override;

class FindPriceDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $productId,
        public readonly string $unitId,
        public readonly ?bool $isDefault,
    ) {}

    /**
     * Konversi data object ke array
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'product_id' => $this->productId,
            'unit_id' => $this->unitId,
            'is_default' => $this->isDefault,
        ];
    }
}
