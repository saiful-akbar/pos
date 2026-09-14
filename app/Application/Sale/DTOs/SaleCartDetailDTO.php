<?php

declare(strict_types=1);

namespace App\Application\Sale\DTOs;

use App\Base\DTO;
use App\Domain\Product\Entities\ProductEntity;
use App\Domain\Sale\Entities\SaleCartEntity;
use Override;

class SaleCartDetailDTO extends DTO
{
    /**
     * Sale cart
     */
    public readonly SaleCartEntity $cart;

    /**
     * Produk
     */
    public readonly ProductEntity $product;

    /**
     * Quantity
     */
    public readonly int|float $quantity;

    /**
     * Subtotal harga
     */
    public readonly int|float $subtotal;

    /**
     * Create a new class instance.
     */
    public function __construct(SaleCartEntity $cart, ProductEntity $product, int|float $quantity)
    {
        $this->cart = $cart;
        $this->product = $product;
        $this->quantity = $quantity;

        $this->subtotal = $this->subtotalCalculation(
            product: $product,
            quantity: $quantity,
        );
    }

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'sale_cart_id' => $this->cart->id,
            'product_id' => $this->product->id,
            'quantity' => $this->quantity,
            'purchase_price' => $this->product->purchasePrice,
            'selling_price' => $this->product->sellingPrice,
            'discount' => $this->product->discount,
            'subtotal' => $this->subtotal,
        ];
    }

    /**
     * Menghitung subtotal
     */
    private function subtotalCalculation(ProductEntity $product, int|float $quantity): int|float
    {
        $discountPrice = 0;

        if ($product->discount > 0) {
            $discountPrice = ($product->sellingPrice * $product->discount) / 100;
        }

        return ($quantity * $product->sellingPrice) - $discountPrice;
    }
}
