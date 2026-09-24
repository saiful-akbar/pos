<?php

declare(strict_types=1);

namespace App\Domain\StockOpname\Entities;

use App\Base\Entity;
use App\Domain\StockOpname\ValueObjects\ProductStockOpnameValue;
use DateTimeInterface;
use Override;

class MonthlyStockOpnameDetailEntity extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $monthlyStockOpnameId,
        public readonly ProductStockOpnameValue $product,
        public readonly int|float $systemStock,
        public readonly int|float $physicalStock,
        public readonly int|float $difference,
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
            'monthly_stock_opname_id' => $this->monthlyStockOpnameId,
            'product' => $this->product->toArray(),
            'system_stock' => $this->systemStock,
            'physical_stock' => $this->physicalStock,
            'difference' => $this->difference,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
