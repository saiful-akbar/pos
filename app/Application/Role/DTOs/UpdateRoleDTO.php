<?php

declare(strict_types=1);

namespace App\Application\Role\DTOs;

use App\Base\DTO;
use Override;

class UpdateRoleDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $description,
    ) {}

    /**
     * Konversi data object ke array
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}
