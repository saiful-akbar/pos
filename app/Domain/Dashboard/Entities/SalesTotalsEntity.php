<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Entities;

use App\Base\Entity;
use Override;

class SalesTotalsEntity extends Entity
{
    /**
     * Create a new class instance.
     *
     * Seluruh nilai berupa string desimal (SUM sales.grand_total) pada rentang masing-masing.
     */
    public function __construct(
        public readonly string $today,
        public readonly string $month,
        public readonly string $year,
    ) {}

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'today' => $this->today,
            'month' => $this->month,
            'year' => $this->year,
        ];
    }
}
