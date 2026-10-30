<?php

declare(strict_types=1);

namespace App\Application\Unit\DTOs;

use App\Base\DTO;
use Override;

class UnitDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly bool $isActive = true,
    ) {}

    /**
     * KOnversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'code' => strtoupper($this->code),
            'name' => $this->name,
            'is_active' => $this->isActive,
        ];
    }
}
