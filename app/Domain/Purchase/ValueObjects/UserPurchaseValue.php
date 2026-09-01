<?php

declare(strict_types=1);

namespace App\Domain\Purchase\ValueObjects;

use App\Base\Entity;
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
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'full_name' => $this->fullName,
            'photo' => $this->photo,
            'isActive' => $this->isActive,
        ];
    }
}
