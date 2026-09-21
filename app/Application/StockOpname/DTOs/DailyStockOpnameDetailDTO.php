<?php

declare(strict_types=1);

namespace App\Application\StockOpname\DTOs;

use App\Base\DTO;
use Override;

class DailyStockOpnameDetailDTO extends DTO
{
    /**
     * Stock opname detail id
     */
    public readonly string $dailyStockOpnameId;

    /**
     * Product id
     */
    public readonly string $productId;

    /**
     * System stock
     */
    public readonly int|float $systemStock;

    /**
     * Fisik stock
     */
    public readonly int|float $physicalStock;

    /**
     * Selisih (fisik stock - system stock)
     */
    public readonly int|float $difference;

    /**
     * Create a new class instance.
     */
    public function __construct(
        string $dailyStockOpnameId,
        string $productId,
        int|float $systemStock,
        int|float $physicalStock,
    ) {
        $this->dailyStockOpnameId = $dailyStockOpnameId;
        $this->productId = $productId;
        $this->systemStock = $systemStock;
        $this->physicalStock = $physicalStock;
        $this->difference = $physicalStock - $systemStock;
    }

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
