<?php

declare(strict_types=1);

namespace App\Application\StockOpname\DTOs;

use App\Base\DTO;
use Override;

class DailyStockOpnameDetailDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $dailyStockOpnameId,
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
            'daily_stock_opname_id' => $this->dailyStockOpnameId,
            'product_id' => $this->productId,
            'quantity' => $this->quantity,
        ];
    }
}
