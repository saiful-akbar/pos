<?php

declare(strict_types=1);

namespace App\Domain\Report\Entities;

use App\Base\Entity;
use Override;

class MonitoringMonthlyStockEntity extends Entity
{
    /**
     * Kode kategori produk.
     */
    public readonly string $categoryCode;

    /**
     * Nama kategori produk.
     */
    public readonly string $categoryName;

    /**
     * Kode produk.
     */
    public readonly string $productCode;

    /**
     * Nama produk.
     */
    public readonly string $productName;

    /**
     * Stok awal.
     */
    public readonly int|float $beginningStock;

    /**
     * Quantity penjualan.
     */
    public readonly int|float $purchaseQuantity;

    /**
     * Quantity return pembelian.
     */
    public readonly int|float $saleReturnQuantity;

    /**
     * Quantity penjualan.
     */
    public readonly int|float $saleQuantity;

    /**
     * Book balance stock.
     */
    public readonly int|float $bookBalanceQuantity;

    /**
     * Stock akhir.
     */
    public readonly int|float $endingStock;

    /**
     * Selisih stock.
     */
    public readonly int|float $differenceQuantity;

    /**
     * Create a new class instance.
     */
    public function __construct(
        string $categoryCode,
        string $categoryName,
        string $productCode,
        string $productName,
        int|float $beginningStock,
        int|float $purchaseQuantity,
        int|float $saleReturnQuantity,
        int|float $saleQuantity,
        int|float $endingStock,
    ) {
        $this->categoryCode = $categoryCode;
        $this->categoryName = $categoryName;
        $this->productCode = $productCode;
        $this->productName = $productName;
        $this->beginningStock = $beginningStock;
        $this->purchaseQuantity = $purchaseQuantity;
        $this->saleReturnQuantity = $saleReturnQuantity;
        $this->saleQuantity = $saleQuantity;
        $this->bookBalanceQuantity = $beginningStock + $purchaseQuantity + $saleReturnQuantity - $saleQuantity;
        $this->endingStock = $endingStock;
        $this->differenceQuantity = $this->endingStock - $this->bookBalanceQuantity;
    }

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'category_code' => $this->categoryCode,
            'category_name' => $this->categoryName,
            'product_code' => $this->productCode,
            'product_name' => $this->productName,
            'beginning_stock' => $this->beginningStock,
            'purchase_quantity' => $this->purchaseQuantity,
            'sale_return_quantity' => $this->saleReturnQuantity,
            'sale_quantity' => $this->saleQuantity,
            'book_balance_quantity' => $this->bookBalanceQuantity,
            'ending_stock' => $this->endingStock,
            'difference_quantity' => $this->differenceQuantity,
        ];
    }
}
