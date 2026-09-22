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
        public readonly int|float $systemStock,
        public readonly int|float $physicalStock,
        public readonly int|float $difference,
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
            'system_stock' => $this->systemStock,
            'physical_stock' => $this->physicalStock,
            'difference' => $this->difference,
        ];
    }
}
