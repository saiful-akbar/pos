<?php

declare(strict_types=1);

namespace App\Domain\Stock\Entities;

use App\Base\Entity;
use App\Domain\Stock\ValueObjects\ProductStockValue;
use App\Domain\Stock\ValueObjects\UnitStockValue;
use App\Models\DailyStockDetail;
use DateTimeInterface;
use Override;

class DailyStockDetailEntity extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $dailyStockId,
        public readonly ProductStockValue $product,
        public readonly UnitStockValue $unit,
        public readonly float $quantity,
        public readonly float $multiplesQuantity,
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
            'daily_stock_id' => $this->dailyStockId,
            'product' => $this->product->toArray(),
            'unit' => $this->unit->toArray(),
            'quantity' => $this->quantity,
            'multiples_quantity' => $this->multiplesQuantity,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Membuat instance dari model (relasi product.category dan unit harus sudah dimuat).
     */
    public static function fromModel(DailyStockDetail $model): self
    {
        return new self(
            id: $model->id,
            dailyStockId: $model->daily_stock_id,
            product: ProductStockValue::fromModel($model->product),
            unit: UnitStockValue::fromModel($model->unit),
            quantity: parse_float($model->quantity),
            multiplesQuantity: parse_float($model->multiples_quantity),
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }
}
