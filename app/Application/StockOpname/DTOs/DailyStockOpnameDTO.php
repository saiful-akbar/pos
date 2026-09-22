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
        public readonly float|int $totalQuantity,
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
            'total_quantity' => $this->totalQuantity,
        ];
    }
}
