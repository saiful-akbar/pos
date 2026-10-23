<?php

declare(strict_types=1);

namespace App\Application\Stock\DTOs;

use App\Base\DTO;
use Override;

class MonthlyStockDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $userId,
        public readonly string $dailyStockId,
        public readonly int $year,
        public readonly int $month,
        public readonly float $totalProduct,
        public readonly float $totalQuantity,
        public readonly ?string $notes,
    ) {}

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'daily_stock_id' => $this->dailyStockId,
            'year' => $this->year,
            'month' => $this->month,
            'total_product' => $this->totalProduct,
            'total_quantity' => $this->totalQuantity,
            'notes' => $this->notes,
        ];
    }
}
