<?php

declare(strict_types=1);

namespace App\Application\Report\DTOs;

use App\Base\DTO;
use Override;

class GetMonitoringMonthlyDTO extends DTO
{
    /**
     * Tahun saat ini
     *
     * @var integer
     */
    public readonly int $year;

    /**
     * Bulan saat ini
     *
     * @var integer
     */
    public readonly int $month;

    /**
     * Tahun untuk stok akkhir
     *
     * @var integer
     */
    public readonly int $endYear;

    /**
     * Bulan untuk stok akhir
     *
     * @var integer
     */
    public readonly int $endMonth;

    /**
     * Create a new class instance.
     */
    public function __construct(
        int $year,
        int $month,
    ) {
        $this->year = $year;
        $this->month = $month;
        $this->endYear = $month >= 12 ? $year + 1 : $year;
        $this->endMonth = $month >= 12 ? 1 : $month + 1;
    }

    /**
     * Konversi data object ke array
     *
     * @return array
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'year' => $this->year,
            'month' => $this->month,
            'end_year' => $this->endYear,
            'end_month' => $this->endMonth,
        ];
    }
}
