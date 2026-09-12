<?php

declare(strict_types=1);

namespace App\Application\Unit\DTOs;

use App\Base\DTO;
use Override;

class CreateUnitDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly bool $isActive = true,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'is_active' => $this->isActive,
        ];
    }
}
