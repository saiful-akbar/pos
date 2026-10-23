<?php

declare(strict_types=1);

namespace App\Application\Stock\DTOs;

use App\Base\DTO;
use DateTimeInterface;
use Override;

class DailyStockDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $userId,
        public readonly DateTimeInterface $date,
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
            'date' => $this->date->format('Y-m-d'),
            'total_product' => $this->totalProduct,
            'total_quantity' => $this->totalQuantity,
            'notes' => $this->notes,
        ];
    }
}
