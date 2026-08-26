<?php

declare(strict_types=1);

namespace App\Domain\User\Entities;

use App\Base\Entity;
use App\Domain\User\ValueObjects\RoleUserValue;
use DateTimeInterface;
use Override;

class UserEntity extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly RoleUserValue $role,
        public readonly string $username,
        public readonly string $fullName,
        public readonly string $photo,
        public readonly DateTimeInterface $createdAt,
        public readonly DateTimeInterface $updatedAt,
        public readonly ?DateTimeInterface $deletedAt,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'role' => $this->role->toArray(),
            'username' => $this->username,
            'full_name' => $this->fullName,
            'photo' => $this->photo,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
