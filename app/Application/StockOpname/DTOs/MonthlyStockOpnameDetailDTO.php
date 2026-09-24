<?php

declare(strict_types=1);

namespace App\Application\StockOpname\DTOs;

use App\Base\DTO;
use Override;

class MonthlyStockOpnameDetailDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $monthlyStockOpnameId,
        public readonly string $productId,
        public readonly int|float $quantity,
    ) {}

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'monthly_stock_opname_id' => $this->monthlyStockOpnameId,
            'product_id' => $this->productId,
            'quantity' => $this->quantity,
        ];
    }
}
