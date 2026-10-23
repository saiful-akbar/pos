<?php

declare(strict_types=1);

namespace App\Application\Stock\DTOs;

use App\Base\DTO;
use Override;

class MonthlyStockDetailDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $monthlyStockId,
        public readonly string $productId,
        public readonly string $unitId,
        public readonly float $quantity,
        public readonly float $multiplesQuantity,
    ) {}

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'monthly_stock_id' => $this->monthlyStockId,
            'product_id' => $this->productId,
            'unit_id' => $this->unitId,
            'quantity' => $this->quantity,
            'multiples_quantity' => $this->multiplesQuantity,
        ];
    }
}
