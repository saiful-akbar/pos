<?php

declare(strict_types=1);

namespace App\Domain\Module\Entities;

use App\Base\Entity;
use Override;

class DetailModuleEntity extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $icon,
        public readonly string $path,
        public readonly string $route,
        public readonly bool $create,
        public readonly bool $read,
        public readonly bool $update,
        public readonly bool $remove,
        public readonly bool $delete,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'icon' => $this->icon,
            'path' => $this->path,
            'route' => $this->route,
            'create' => $this->create,
            'read' => $this->read,
            'update' => $this->update,
            'remove' => $this->remove,
            'delete' => $this->delete,
        ];
    }
}
