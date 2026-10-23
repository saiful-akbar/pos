<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Entities;

use App\Base\Entity;
use Override;

class MonthlySummaryEntity extends Entity
{
    /**
     * Nama bulan (indeks 0 = Januari).
     */
    private const MONTH_NAMES = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
    ];

    /**
     * Create a new class instance.
     *
     * @param string $quantity Total kuantitas terjual (satuan dasar: quantity x multiples_quantity).
     * @param string $amount   Total nilai penjualan (SUM sales.grand_total).
     */
    public function __construct(
        public readonly int $year,
        public readonly int $month,
        public readonly string $quantity,
        public readonly string $amount,
    ) {}

    /**
     * Nama bulan dalam bahasa Indonesia.
     */
    public function monthName(): string
    {
        return self::MONTH_NAMES[$this->month - 1];
    }

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'year' => $this->year,
            'month' => $this->month,
            'month_name' => $this->monthName(),
            'quantity' => $this->quantity,
            'amount' => $this->amount,
        ];
    }
}
