<?php

declare(strict_types=1);

namespace App\Domain\StockOpname\Entities;

use App\Base\Entity;
use App\Domain\StockOpname\Enums\TypeStockMovementEnum;
use App\Domain\StockOpname\ValueObjects\ProductStockValue;
use App\Domain\StockOpname\ValueObjects\UserStockValue;
use DateTimeInterface;
use Override;

class StockMovementEntity extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly UserStockValue $user,
        public readonly ProductStockValue $product,
        public readonly TypeStockMovementEnum $type,
        public readonly int $quantity,
        public readonly int $stockBefore,
        public readonly int $stockAfter,
        public readonly ?string $referenceId = null,
        public readonly ?string $notes = null,
        public readonly ?DateTimeInterface $createdAt = null,
        public readonly ?DateTimeInterface $updatedAt = null,
        public readonly ?DateTimeInterface $deletedAt = null,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user' => $this->user->toArray(),
            'product' => $this->product->toArray(),
            'type' => $this->type->value,
            'reference_id' => $this->referenceId,
            'quantity' => $this->quantity,
            'stock_before' => $this->stockBefore,
            'stock_after' => $this->stockAfter,
            'notes' => $this->notes,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
