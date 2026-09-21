<?php

declare(strict_types=1);

namespace App\Domain\StockOpname\ValueObjects;

use App\Base\Entity;
use Override;

class UserStockOpnameValue extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $fullName,
        public readonly string $username,
        public readonly ?string $photo,
        public readonly bool $isActive,
    ) {}

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->fullName,
            'username' => $this->username,
            'photo' => $this->photo,
            'is_active' => $this->isActive,
        ];
    }
}
