<?php

declare(strict_types=1);

namespace App\Domain\Stock\Entities;

use App\Base\Entity;
use App\Domain\Stock\ValueObjects\UserStockValue;
use App\Models\MonthlyStock;
use DateTimeInterface;
use Override;

class MonthlyStockEntity extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly UserStockValue $user,
        public readonly string $dailyStockId,
        public readonly int $year,
        public readonly int $month,
        public readonly float $totalProduct,
        public readonly float $totalQuantity,
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
            'user' => $this->user->toArray(),
            'daily_stock_id' => $this->dailyStockId,
            'year' => $this->year,
            'month' => $this->month,
            'total_product' => $this->totalProduct,
            'total_quantity' => $this->totalQuantity,
            'notes' => $this->notes,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Membuat instance dari model (relasi user harus sudah dimuat).
     */
    public static function fromModel(MonthlyStock $model): self
    {
        return new self(
            id: $model->id,
            user: UserStockValue::fromModel($model->user),
            dailyStockId: $model->daily_stock_id,
            year: (int) $model->year,
            month: (int) $model->month,
            totalProduct: parse_float($model->total_product),
            totalQuantity: parse_float($model->total_quantity),
            notes: $model->notes,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }
}
