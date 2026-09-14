<?php

declare(strict_types=1);

namespace App\Domain\Purchase\Entities;

use App\Base\Entity;
use App\Domain\Purchase\ValueObjects\UserPurchaseValue;
use DateTimeInterface;
use Override;

class PurchaseCartEntity extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly UserPurchaseValue $user,
        public readonly DateTimeInterface $purchaseDate,
        public readonly int|float $totalQuantity,
        public readonly int|float $totalPrice,
        public readonly ?DateTimeInterface $createdAt,
        public readonly ?DateTimeInterface $updatedAt,
        public readonly ?DateTimeInterface $deletedAt,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user' => $this->user,
            'purchase_date' => $this->purchaseDate->format('Y-m-d H:i:s'),
            'total_quantity' => $this->totalQuantity,
            'total_price' => $this->totalPrice,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s;'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s;'),
        ];
    }
}
