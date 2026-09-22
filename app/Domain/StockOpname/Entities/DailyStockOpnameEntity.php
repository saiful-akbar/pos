<?php

declare(strict_types=1);

namespace App\Domain\StockOpname\Entities;

use App\Base\Entity;
use App\Domain\StockOpname\ValueObjects\UserStockOpnameValue;
use DateTimeInterface;
use Override;

class DailyStockOpnameEntity extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly UserStockOpnameValue $user,
        public readonly DateTimeInterface $date,
        public readonly float|int $totalProduct,
        public readonly float|int $totalSystemStock,
        public readonly float|int $totalPhysicalStock,
        public readonly float|int $totalDifference,
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
            'user' => $this->user->toArray(),
            'date' => $this->date->format('Y-m-d H:i:s'),
            'total_product' => $this->totalProduct,
            'total_system_stock' => $this->totalSystemStock,
            'total_physical_stock' => $this->totalPhysicalStock,
            'total_difference' => $this->totalDifference,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
