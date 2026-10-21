<?php

declare(strict_types=1);

namespace App\Domain\Sale\Entities;

use App\Base\Entity;
use App\Domain\Sale\ValueObjects\UserSaleValue;
use App\Models\SaleCart;
use DateTimeInterface;
use Override;

class SaleCartEntity extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly UserSaleValue $user,
        public readonly DateTimeInterface $transactionDate,
        public readonly float $totalProduct,
        public readonly float $totalQuantity,
        public readonly float $totalAmount,
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
            'transaction_date' => $this->transactionDate->format('Y-m-d'),
            'total_product' => $this->totalProduct,
            'total_quantity' => $this->totalQuantity,
            'total_amount' => $this->totalAmount,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Membuat instance dari model.
     */
    public static function fromModel(SaleCart $model): self
    {
        return new self(
            id: $model->id,
            user: UserSaleValue::fromModel($model->user),
            transactionDate: parse_date_time_immutable($model->transaction_date),
            totalProduct: parse_float($model->total_product),
            totalQuantity: parse_float($model->total_quantity),
            totalAmount: parse_float($model->total_amount),
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }
}
