<?php

declare(strict_types=1);

namespace App\Domain\Sale\ValueObjects;

use App\Base\Entity;
use Override;

class UserSaleValue extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $username,
        public readonly string $fullName,
        public readonly ?string $photo,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'full_name' => $this->fullName,
            'photo' => $this->photo,
        ];
    }
}
