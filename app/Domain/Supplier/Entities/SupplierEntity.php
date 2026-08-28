<?php

declare(strict_types=1);

namespace App\Domain\Supplier\Entities;

use App\Base\Entity;
use DateTimeInterface;
use Override;

class SupplierEntity extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $code,
        public readonly string $name,
        public readonly ?string $phoneNumber = null,
        public readonly ?string $address = null,
        public readonly ?string $notes = null,
        public readonly bool $isActive = true,
        public readonly ?DateTimeInterface $createdAt = null,
        public readonly ?DateTimeInterface $updatedAt = null,
        public readonly ?DateTimeInterface $deletedAt = null,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'phone_number' => $this->phoneNumber,
            'address' => $this->address,
            'notes' => $this->notes,
            'is_active' => $this->isActive,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
