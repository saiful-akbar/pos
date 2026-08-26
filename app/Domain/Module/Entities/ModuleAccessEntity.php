<?php

declare(strict_types=1);

namespace App\Domain\Module\Entities;

use App\Base\Entity;
use Override;

class ModuleAccessEntity extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $roleId,
        public readonly string $moduleId,
        public readonly bool $read,
        public readonly bool $create,
        public readonly bool $update,
        public readonly bool $remove,
        public readonly bool $delete,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
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
