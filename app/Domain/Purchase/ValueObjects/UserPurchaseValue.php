<?php

declare(strict_types=1);

namespace App\Domain\Purchase\ValueObjects;

use App\Base\Entity;
use DateTimeInterface;
use Override;

class UserPurchaseValue extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $username,
        public readonly string $fullName,
        public readonly ?string $photo,
        public readonly bool $isActive,
        public readonly ?DateTimeInterface $createdAt,
        public readonly ?DateTimeInterface $updatedAt,
        public readonly ?DateTimeInterface $celetedAt,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'full_name' => $this->fullName,
            'photo' => $this->photo,
            'is_active' => $this->isActive,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'celeted_at' => $this->celetedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
