<?php

declare(strict_types=1);

namespace App\Application\Cash\DTOs;

use App\Base\DTO;
use Override;

class CashMonthlyBalanceDTO extends DTO
{
    /**
     * Create a new class instance.
     *
     * Saldo berupa string desimal agar tidak melewati bilangan floating point.
     */
    public function __construct(
        public readonly string $userId,
        public readonly int $year,
        public readonly int $month,
        public readonly string $openingBalance,
        public readonly ?string $notes,
    ) {}

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'year' => $this->year,
            'month' => $this->month,
            'opening_balance' => $this->openingBalance,
            'notes' => $this->notes,
        ];
    }
}
