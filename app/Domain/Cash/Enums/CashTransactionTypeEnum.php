<?php

declare(strict_types=1);

namespace App\Domain\Cash\Enums;

enum CashTransactionTypeEnum: string
{
    case INCOMING = 'INCOMING';
    case OUTGOING = 'OUTGOING';

    /**
     * Label tampilan.
     */
    public function label(): string
    {
        return match ($this) {
            self::INCOMING => 'Kas Masuk',
            self::OUTGOING => 'Kas Keluar',
        };
    }
}
