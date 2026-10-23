<?php

declare(strict_types=1);

namespace App\Domain\Report\Entities;

use App\Base\Entity;
use Override;

class StockReportSummaryEntity extends Entity
{
    /**
     * Create a new class instance.
     *
     * @param int  $totalProducts    Jumlah produk yang dilaporkan (punya snapshot atau mutasi pada periode).
     * @param int  $matched          Produk dengan selisih 0.
     * @param int  $negative         Produk dengan stok akhir aktual kurang dari Book Balance.
     * @param int  $positive         Produk dengan stok akhir aktual lebih dari Book Balance.
     * @param int  $unavailable      Produk yang belum dapat direkonsiliasi (snapshot awal atau akhir belum tersedia).
     * @param bool $openingAvailable Snapshot stok awal periode tersedia.
     * @param bool $closingAvailable Snapshot stok awal bulan berikutnya (stok akhir aktual) tersedia.
     */
    public function __construct(
        public readonly int $totalProducts,
        public readonly int $matched,
        public readonly int $negative,
        public readonly int $positive,
        public readonly int $unavailable,
        public readonly bool $openingAvailable,
        public readonly bool $closingAvailable,
    ) {}

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'total_products' => $this->totalProducts,
            'matched' => $this->matched,
            'negative' => $this->negative,
            'positive' => $this->positive,
            'unavailable' => $this->unavailable,
            'opening_available' => $this->openingAvailable,
            'closing_available' => $this->closingAvailable,
        ];
    }
}
