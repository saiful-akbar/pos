<?php

declare(strict_types=1);

namespace App\Domain\Sale\Entities;

use App\Base\Entity;
use App\Domain\Sale\ValueObjects\UserSaleValue;
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
        public readonly bool $isPending,
        public readonly DateTimeInterface $transactionDate,
        public readonly int $totalQuantity,
        public readonly int $totalAmount,
        public readonly ?DateTimeInterface $createdAt,
        public readonly ?DateTimeInterface $updatedAt,
        public readonly ?DateTimeInterface $deletedAt,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user' => $this->user->toArray(),
            'is_pending' => $this->isPending,
            'transaction_date' => $this->transactionDate->format('Y-m-d H:i:s'),
            'total_quantity' => $this->totalQuantity,
            'total_amount' => $this->totalAmount,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
