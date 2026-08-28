<?php

declare(strict_types=1);

namespace App\Domain\Customer\Entities;

use App\Base\DTO;
use DateTimeInterface;
use Override;

class CustomerEntity extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $phoneNumber,
        public readonly ?string $address,
        public readonly ?DateTimeInterface $createdAt,
        public readonly ?DateTimeInterface $updatedAt,
        public readonly ?DateTimeInterface $deletedAt,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone_number' => $this->phoneNumber,
            'address' => $this->address,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
