<?php

declare(strict_types=1);

namespace App\Application\Module\DTOs;

use App\Base\DTO;
use DateTimeInterface;
use Override;

class CreateModuleAccessDTO extends DTO
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
        public readonly DateTimeInterface $createdAt,
        public readonly DateTimeInterface $updatedAt,
        public readonly ?DateTimeInterface $deletedAt = null,
    ) {}

    /**
     * Konversi data object ke array.
     */
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
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'deleted_at' => $this->deletedAt,
        ];
    }
}
