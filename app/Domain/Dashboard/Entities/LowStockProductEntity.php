<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Entities;

use App\Base\Entity;
use Override;

class LowStockProductEntity extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $code,
        public readonly ?string $name,
        public readonly ?string $categoryName,
        public readonly string $currentStock,
        public readonly string $minimumStock,
    ) {}

    /**
     * Selisih stok terhadap batas minimum (negatif = di bawah minimum).
     */
    public function difference(): string
    {
        return bcsub($this->currentStock, $this->minimumStock, 2);
    }

    /**
     * Status stok: out (habis), below (di bawah minimum), atau reached (mencapai minimum).
     */
    public function status(): string
    {
        if (bccomp($this->currentStock, '0', 2) <= 0) {
            return 'out';
        }

        return bccomp($this->currentStock, $this->minimumStock, 2) < 0 ? 'below' : 'reached';
    }

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
            'category_name' => $this->categoryName,
            'current_stock' => $this->currentStock,
            'minimum_stock' => $this->minimumStock,
            'difference' => $this->difference(),
            'status' => $this->status(),
        ];
    }
}
