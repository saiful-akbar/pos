<?php

declare(strict_types=1);

namespace App\Application\Module\DTOs;

use App\Base\DTO;
use Override;

class ModuleAccessDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $roleId,
        public readonly string $moduleId,
        public readonly bool $read,
        public readonly bool $create,
        public readonly bool $update,
        public readonly bool $remove,
        public readonly bool $delete,
    ) {}

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'role_id' => $this->roleId,
            'module_id' => $this->moduleId,
            'read' => $this->read,
            'create' => $this->create,
            'update' => $this->update,
            'remove' => $this->remove,
            'delete' => $this->delete,
        ];
    }
}
