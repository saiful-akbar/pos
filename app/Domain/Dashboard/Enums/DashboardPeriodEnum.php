<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Enums;

use DateTimeImmutable;

enum DashboardPeriodEnum: string
{
    /** Bulan berjalan (tanggal 1 sampai hari ini). */
    case MONTH = 'month';

    /** 30 hari terakhir termasuk hari ini. */
    case LAST_30_DAYS = '30days';

    /** Tahun berjalan (1 Januari sampai hari ini). */
    case YEAR = 'year';

    /**
     * Label tampilan.
     */
    public function label(): string
    {
        return match ($this) {
            self::MONTH => 'Bulan ini',
            self::LAST_30_DAYS => '30 hari terakhir',
            self::YEAR => 'Tahun ini',
        };
    }

    /**
     * Rentang tanggal periode (awal dan akhir, inklusif) berdasarkan tanggal hari ini.
     *
     * @return array{0: DateTimeImmutable, 1: DateTimeImmutable}
     */
    public function range(DateTimeImmutable $today): array
    {
        $today = $today->setTime(0, 0);

        return match ($this) {
            self::MONTH => [$today->modify('first day of this month'), $today],
            self::LAST_30_DAYS => [$today->modify('-29 days'), $today],
            self::YEAR => [$today->setDate((int) $today->format('Y'), 1, 1), $today],
        };
    }
}
