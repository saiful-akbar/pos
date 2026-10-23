<?php

declare(strict_types=1);

namespace App\Domain\Cash\Entities;

use App\Base\Entity;
use App\Domain\Cash\Enums\CashReconciliationStatusEnum;
use Override;

/**
 * Ringkasan kas satu bulan. Seluruh nilai uang berupa string desimal (hasil perhitungan DECIMAL di database).
 * Nilai null berarti data belum tersedia (bukan nol).
 */
class CashMonthSummaryEntity extends Entity
{
    /**
     * Create a new class instance.
     *
     * @param ?string $openingBalance     Saldo awal aktual bulan ini (null jika belum dicatat).
     * @param string  $incoming           Total kas masuk.
     * @param string  $outgoing           Total kas keluar.
     * @param ?string $theoreticalClosing Saldo awal + kas masuk - kas keluar (null jika saldo awal belum dicatat).
     * @param ?string $nextOpeningBalance Saldo aktual awal bulan berikutnya (null jika belum dicatat).
     * @param ?string $difference         Saldo aktual bulan berikutnya - saldo teoritis (null jika belum dapat dihitung).
     */
    public function __construct(
        public readonly int $year,
        public readonly int $month,
        public readonly ?string $balanceId,
        public readonly ?string $openingBalance,
        public readonly string $incoming,
        public readonly string $outgoing,
        public readonly ?string $theoreticalClosing,
        public readonly ?string $nextOpeningBalance,
        public readonly ?string $difference,
        public readonly CashReconciliationStatusEnum $status,
        public readonly ?string $notes,
    ) {}

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'year' => $this->year,
            'month' => $this->month,
            'balance_id' => $this->balanceId,
            'opening_balance' => $this->openingBalance,
            'incoming' => $this->incoming,
            'outgoing' => $this->outgoing,
            'theoretical_closing' => $this->theoreticalClosing,
            'next_opening_balance' => $this->nextOpeningBalance,
            'difference' => $this->difference,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'notes' => $this->notes,
        ];
    }
}
