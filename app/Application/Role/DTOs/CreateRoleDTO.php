<?php

declare(strict_types=1);

namespace App\Application\Role\DTOs;

use App\Base\DTO;
use Override;

class CreateRoleDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $name,
        public readonly ?string $description,
        public readonly array $modules,
    ) {}

    /**
     * Konversi data object ke array.
     *
     * @return array
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
