<?php

declare(strict_types=1);

namespace App\Domain\StockOpname\Entities;

use App\Base\Entity;
use App\Domain\StockOpname\ValueObjects\UserStockOpnameValue;
use DateTimeInterface;
use Override;

class MonthlyStockOpnameEntity extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly DailyStockOpnameEntity $dailyStockOpname,
        public readonly UserStockOpnameValue $user,
        public readonly int $year,
        public readonly int $month,
        public readonly int|float $totalProduct,
        public readonly int|float $totalQuantity,
        public readonly ?string $notes,
        public readonly ?DateTimeInterface $createdAt,
        public readonly ?DateTimeInterface $updatedAt,
        public readonly ?DateTimeInterface $deletedAt,
    ) {}

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'daily_stock_opname' => $this->dailyStockOpname->toArray(),
            'user' => $this->user->toArray(),
            'year' => $this->year,
            'month' => $this->month,
            'total_product' => $this->totalProduct,
            'total_quantity' => $this->totalQuantity,
            'notes' => $this->notes,
            'created_at' => $this->createdAt?->format('Y-m-d'),
            'updated_at' => $this->updatedAt?->format('Y-m-d'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d'),
        ];
    }
}
