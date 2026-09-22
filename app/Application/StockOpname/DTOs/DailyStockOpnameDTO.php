<?php

declare(strict_types=1);

namespace App\Application\StockOpname\DTOs;

use App\Base\DTO;
use DateTimeInterface;
use Override;

class DailyStockOpnameDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $userId,
        public readonly DateTimeInterface $date,
        public readonly float|int $totalProduct,
        public readonly float|int $totalSystemStock,
        public readonly float|int $totalPhysicalStock,
        public readonly float|int $totalDifference,
    ) {}

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'date' => $this->date->format('Y-m-d H:i:s'),
            'total_product' => $this->totalProduct,
            'total_system_stock' => $this->totalSystemStock,
            'total_physical_stock' => $this->totalPhysicalStock,
            'total_difference' => $this->totalDifference,
        ];
    }
}
