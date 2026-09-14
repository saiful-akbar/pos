<?php

declare(strict_types=1);

namespace App\Domain\Sale\Entities;

use App\Base\Entity;
use App\Domain\Sale\ValueObjects\UserSaleValue;
use DateTimeInterface;
use Override;

class SaleReturnCartEntity extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $saleId,
        public readonly UserSaleValue $user,
        public readonly DateTimeInterface $transactionDate,
        public readonly float $totalQuantity,
        public readonly float $totalAmount,
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
            'sale_id' => $this->saleId,
            'user' => $this->user->toArray(),
            'transaction_date' => $this->transactionDate->format('Y-m-d'),
            'total_quantity' => $this->totalQuantity,
            'total_amount' => $this->totalAmount,
            'notes' => $this->notes,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
