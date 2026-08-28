<?php

declare(strict_types=1);

namespace App\Domain\Unit\Entities;

use App\Base\Entity;
use DateTimeInterface;
use Override;

class UnitEntity extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $code,
        public readonly string $name,
        public readonly bool $isActive,
        public readonly ?DateTimeInterface $createdAt,
        public readonly ?DateTimeInterface $updatedAt,
        public readonly ?DateTimeInterface $deletedAt,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'is_active' => $this->isActive,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
