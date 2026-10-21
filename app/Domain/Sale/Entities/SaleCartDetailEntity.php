<?php

declare(strict_types=1);

namespace App\Domain\Sale\Entities;

use App\Base\Entity;
use App\Domain\Sale\ValueObjects\ProductSaleValue;
use App\Domain\Sale\ValueObjects\UnitSaleValue;
use App\Models\SaleCartDetail;
use DateTimeInterface;
use Override;

class SaleCartDetailEntity extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $saleCartId,
        public readonly ProductSaleValue $product,
        public readonly UnitSaleValue $unit,
        public readonly float $multiplesQuantity,
        public readonly float $purchasePrice,
        public readonly float $sellingPrice,
        public readonly float $quantity,
        public readonly float $subtotal,
        public readonly float $discount,
        public readonly float $grandTotal,
        public readonly ?DateTimeInterface $createdAt,
        public readonly ?DateTimeInterface $updatedAt,
        public readonly ?DateTimeInterface $deletedAt,
    ) {}

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'sale_cart_id' => $this->saleCartId,
            'product' => $this->product->toArray(),
            'unit' => $this->unit->toArray(),
            'multiples_quantity' => $this->multiplesQuantity,
            'purchase_price' => $this->purchasePrice,
            'selling_price' => $this->sellingPrice,
            'quantity' => $this->quantity,
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'grand_total' => $this->grandTotal,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Membuat instance dari model.
     */
    public static function fromModel(SaleCartDetail $model): self
    {
        return new self(
            id: $model->id,
            saleCartId: $model->sale_cart_id,
            product: ProductSaleValue::fromModel($model->product),
            unit: UnitSaleValue::fromModel($model->unit),
            multiplesQuantity: parse_float($model->multiples_quantity),
            purchasePrice: parse_float($model->purchase_price),
            sellingPrice: parse_float($model->selling_price),
            quantity: parse_float($model->quantity),
            subtotal: parse_float($model->subtotal),
            discount: parse_float($model->discount),
            grandTotal: parse_float($model->grand_total),
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }
}
