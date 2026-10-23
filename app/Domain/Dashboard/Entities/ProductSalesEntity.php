<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Entities;

use App\Base\Entity;
use Override;

class ProductSalesEntity extends Entity
{
    /**
     * Create a new class instance.
     *
     * @param string $quantity Kuantitas terjual pada periode (satuan dasar: quantity x multiples_quantity).
     * @param string $value    Nilai penjualan produk pada periode (SUM sale_details.grand_total).
     */
    public function __construct(
        public readonly string $id,
        public readonly string $code,
        public readonly ?string $name,
        public readonly string $currentStock,
        public readonly string $quantity,
        public readonly string $value,
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
            'current_stock' => $this->currentStock,
            'quantity' => $this->quantity,
            'value' => $this->value,
            'sold' => bccomp($this->quantity, '0', 2) > 0,
        ];
    }
}
