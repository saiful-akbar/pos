<?php

declare(strict_types=1);

namespace App\Application\StockOpname\DTOs;

use App\Base\DTO;
use App\Domain\StockOpname\Enums\TypeStockMovementEnum;
use Override;

class CreateStockMovementDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $userId,
        public readonly string $productId,
        public readonly TypeStockMovementEnum $type,
        public readonly int|float $quantity,
        public readonly int|float $stockBefore,
        public readonly int|float $stockAfter,
        public readonly ?string $referenceId = null,
        public readonly ?string $notes = null,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'product_id' => $this->productId,
            'type' => $this->type->value,
            'reference_id' => $this->referenceId,
            'quantity' => $this->quantity,
            'stock_before' => $this->stockBefore,
            'stock_after' => $this->stockAfter,
            'notes' => $this->notes,
        ];
    }
}
