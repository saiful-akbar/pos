<?php

declare(strict_types=1);

namespace App\Domain\Discount\Entities;

use App\Base\Entity;
use DateTimeInterface;
use Override;

class DiscountEntity extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly int|float $discount,
        public readonly string $name,
        public readonly DateTimeInterface $effectiveDate,
        public readonly ?DateTimeInterface $expirationDate,
        public readonly ?DateTimeInterface $createdAt,
        public readonly ?DateTimeInterface $updatedAt,
        public readonly ?DateTimeInterface $deletedAt,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'discount' => $this->discount,
            'name' => $this->name,
            'effective_date' => $this->effectiveDate->format('Y-m-d'),
            'expiration_date' => $this->expirationDate?->format('Y-m-d'),
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
