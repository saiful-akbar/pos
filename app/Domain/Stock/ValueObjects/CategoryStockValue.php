<?php

declare(strict_types=1);

namespace App\Domain\Stock\ValueObjects;

use App\Base\Entity;
use App\Models\Category;
use Override;

class CategoryStockValue extends Entity
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

    /**
     * Konversi data object ke array.
     */
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

    /**
     * Membuat instance dari model.
     */
    public static function fromModel(Category $model): self
    {
        return new self(
            id: $model->id,
            code: $model->code,
            name: $model->name,
            isActive: $model->is_active === 1,
        );
    }
}
