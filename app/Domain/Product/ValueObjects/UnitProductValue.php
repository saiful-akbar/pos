<?php

declare(strict_types=1);

namespace App\Domain\Product\ValueObjects;

use App\Base\DTO;
use Override;

class UnitProductValue extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $code,
        public readonly string $name,
        public readonly bool $isActive,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'is_active' => $this->isActive,
        ];
    }
}
