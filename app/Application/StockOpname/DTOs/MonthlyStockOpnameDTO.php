<?php

declare(strict_types=1);

namespace App\Application\StockOpname\DTOs;

use App\Base\DTO;
use Override;

class MonthlyStockOpnameDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $dailyStockOpnameId,
        public readonly string $userId,
        public readonly int $year,
        public readonly int $month,
        public readonly int|float $totalProduct,
        public readonly int|float $totalQuantity,
        public readonly ?string $notes,
    ) {}

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'daily_stock_opname_id' => $this->dailyStockOpnameId,
            'user_id' => $this->userId,
            'year' => $this->year,
            'month' => $this->month,
            'total_product' => $this->totalProduct,
            'total_quantity' => $this->totalQuantity,
            'notes' => $this->notes,
        ];
    }
}
