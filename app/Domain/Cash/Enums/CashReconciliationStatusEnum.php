<?php

declare(strict_types=1);

namespace App\Domain\Cash\Enums;

enum CashReconciliationStatusEnum: string
{
    /** Saldo awal bulan berjalan belum dicatat sehingga saldo teoritis tidak dapat dihitung. */
    case INCOMPLETE = 'incomplete';

    /** Saldo awal bulan berikutnya belum dicatat sehingga belum ada pembanding. */
    case UNRECONCILED = 'unreconciled';

    /** Saldo aktual sama dengan saldo teoritis. */
    case MATCH = 'match';

    /** Saldo aktual berbeda dari saldo teoritis. */
    case DIFFERENCE = 'difference';

    /**
     * Label tampilan.
     */
    public function label(): string
    {
        return match ($this) {
            self::INCOMPLETE => 'Data belum lengkap',
            self::UNRECONCILED => 'Belum direkonsiliasi',
            self::MATCH => 'Sesuai',
            self::DIFFERENCE => 'Selisih',
        };
    }
}
