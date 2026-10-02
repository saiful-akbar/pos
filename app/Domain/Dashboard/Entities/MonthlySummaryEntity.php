<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Entities;

use App\Base\Entity;
use Override;

class MonthlySummaryEntity extends Entity
{
    /**
     * Tahun
     */
    public readonly int $year;

    /**
     * Bulan (dalam format nomor)
     */
    public readonly int $month;

    /**
     * Bulan (dalam format string)
     */
    public readonly string $monthName;

    /**
     * Quantity
     */
    public readonly int|float $quantity;

    /**
     * Nominal value
     */
    public readonly int|float $amount;

    /**
     * Create a new class instance.
     */
    public function __construct(
        int $year,
        int $month,
        int|float $quantity,
        int|float $amount,
    ) {
        $this->year = $year;
        $this->month = $month;
        $this->monthName = $this->findMonthName($month);
        $this->quantity = $quantity;
        $this->amount = $amount;
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
            'month_name' => $this->monthName,
            'quantity' => $this->quantity,
            'amount' => $this->amount,
        ];
    }

    /**
     * Mengambil nama bulan
     */
    private function findMonthName(int $month): string
    {
        $months = [
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember',
        ];

        return $months[$month + 1];
    }
}
