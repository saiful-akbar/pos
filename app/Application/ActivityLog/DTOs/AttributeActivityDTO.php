<?php

declare(strict_types=1);

namespace App\Application\ActivityLog\DTOs;

use App\Base\DTO;
use Override;

class AttributeActivityDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly ?array $old = null,
        public readonly ?array $attributes = null,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'old' => $this->old,
            'attributes' => $this->attributes,
        ];
    }
}
