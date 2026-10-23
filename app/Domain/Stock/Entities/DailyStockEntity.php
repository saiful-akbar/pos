<?php

declare(strict_types=1);

namespace App\Domain\Stock\Entities;

use App\Base\Entity;
use App\Domain\Stock\ValueObjects\UserStockValue;
use App\Models\DailyStock;
use App\Models\DailyStockDetail;
use DateTimeInterface;
use Override;

class DailyStockEntity extends Entity
{
    /**
     * Create a new class instance.
     *
     * @param  array<DailyStockDetailEntity>  $details
     */
    public function __construct(
        public readonly string $id,
        public readonly UserStockValue $user,
        public readonly DateTimeInterface $date,
        public readonly float $totalProduct,
        public readonly float $totalQuantity,
        public readonly ?string $notes,
        public readonly array $details,
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
            'date' => $this->date->format('Y-m-d'),
            'total_product' => $this->totalProduct,
            'total_quantity' => $this->totalQuantity,
            'notes' => $this->notes,
            'details' => array_map(
                fn(DailyStockDetailEntity $detail): array => $detail->toArray(),
                $this->details,
            ),
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Membuat instance dari model (relasi user, details.product.category dan details.unit harus sudah dimuat).
     */
    public static function fromModel(DailyStock $model): self
    {
        return new self(
            id: $model->id,
            user: UserStockValue::fromModel($model->user),
            date: parse_date_time_immutable($model->date),
            totalProduct: parse_float($model->total_product),
            totalQuantity: parse_float($model->total_quantity),
            notes: $model->notes,
            details: $model->details
                ->map(fn(DailyStockDetail $detail): DailyStockDetailEntity => DailyStockDetailEntity::fromModel($detail))
                ->all(),
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }
}
