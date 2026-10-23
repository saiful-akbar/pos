<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Base\Repository;
use App\Domain\Cash\Entities\CashMonthSummaryEntity;
use App\Domain\Cash\Enums\CashReconciliationStatusEnum;
use App\Domain\Cash\Repositories\CashReportRepositoryInterface;
use App\Models\CashTransaction;
use Illuminate\Support\Facades\DB;
use Override;

class CashReportRepository extends Repository implements CashReportRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new CashTransaction());
    }

    /**
     * Mengambil ringkasan kas per bulan pada rentang bulan (inklusif).
     *
     * Rumus (dihitung di SQL dengan DECIMAL agar tepat):
     * - saldo teoritis = saldo awal + kas masuk - kas keluar (NULL jika saldo awal belum dicatat);
     * - selisih = saldo awal bulan berikutnya - saldo teoritis (NULL jika salah satunya belum ada);
     * - saldo yang belum dicatat tidak pernah dianggap nol.
     *
     * @return CashMonthSummaryEntity[]
     */
    #[Override]
    public function getMonthlySummaries(int $fromYear, int $fromMonth, int $toYear, int $toMonth): array
    {
        try {
            // Daftar bulan pada rentang (nilai berasal dari bilangan terverifikasi, bukan input mentah).
            $months = [];
            $cursor = $fromYear * 12 + ($fromMonth - 1);
            $last = $toYear * 12 + ($toMonth - 1);

            for (; $cursor <= $last; $cursor++) {
                $year = intdiv($cursor, 12);
                $month = $cursor % 12 + 1;
                $nextYear = $month === 12 ? $year + 1 : $year;
                $nextMonth = $month === 12 ? 1 : $month + 1;

                $months[] = sprintf('SELECT %d AS y, %d AS m, %d AS ny, %d AS nm', $year, $month, $nextYear, $nextMonth);
            }

            if ($months === []) {
                return [];
            }

            $rangeStart = sprintf('%04d-%02d-01', $fromYear, $fromMonth);
            $rangeEnd = date('Y-m-d', strtotime(sprintf('%04d-%02d-01 +1 month', $toYear, $toMonth)));

            $sql = "
                SELECT
                    mo.y AS year,
                    mo.m AS month,
                    b.id AS balance_id,
                    b.opening_balance AS opening_balance,
                    b.notes AS notes,
                    COALESCE(t.incoming, 0) AS incoming,
                    COALESCE(t.outgoing, 0) AS outgoing,
                    nb.opening_balance AS next_opening_balance,
                    CASE WHEN b.id IS NULL THEN NULL
                         ELSE b.opening_balance + COALESCE(t.incoming, 0) - COALESCE(t.outgoing, 0) END AS theoretical_closing,
                    CASE WHEN b.id IS NULL OR nb.id IS NULL THEN NULL
                         ELSE nb.opening_balance - (b.opening_balance + COALESCE(t.incoming, 0) - COALESCE(t.outgoing, 0)) END AS difference,
                    CASE WHEN b.id IS NULL THEN 'incomplete'
                         WHEN nb.id IS NULL THEN 'unreconciled'
                         WHEN nb.opening_balance - (b.opening_balance + COALESCE(t.incoming, 0) - COALESCE(t.outgoing, 0)) = 0 THEN 'match'
                         ELSE 'difference' END AS status
                FROM (".implode(' UNION ALL ', $months).") mo
                LEFT JOIN (
                    SELECT YEAR(transaction_date) AS y, MONTH(transaction_date) AS m,
                           SUM(CASE WHEN type = 'INCOMING' THEN amount ELSE 0 END) AS incoming,
                           SUM(CASE WHEN type = 'OUTGOING' THEN amount ELSE 0 END) AS outgoing
                    FROM cash_transactions
                    WHERE deleted_at IS NULL AND transaction_date >= ? AND transaction_date < ?
                    GROUP BY YEAR(transaction_date), MONTH(transaction_date)
                ) t ON t.y = mo.y AND t.m = mo.m
                LEFT JOIN cash_monthly_balances b ON b.year = mo.y AND b.month = mo.m AND b.deleted_at IS NULL
                LEFT JOIN cash_monthly_balances nb ON nb.year = mo.ny AND nb.month = mo.nm AND nb.deleted_at IS NULL
                ORDER BY mo.y, mo.m
            ";

            $rows = DB::connection($this->model->getConnectionName())->select($sql, [$rangeStart, $rangeEnd]);

            // Nilai uang dipertahankan sebagai string desimal 2 digit (tanpa konversi ke float).
            $money = fn (mixed $value): ?string => $value === null ? null : bcadd((string) $value, '0', 2);

            return array_map(
                fn (object $row): CashMonthSummaryEntity => new CashMonthSummaryEntity(
                    year: (int) $row->year,
                    month: (int) $row->month,
                    balanceId: $row->balance_id,
                    openingBalance: $money($row->opening_balance),
                    incoming: $money($row->incoming) ?? '0.00',
                    outgoing: $money($row->outgoing) ?? '0.00',
                    theoreticalClosing: $money($row->theoretical_closing),
                    nextOpeningBalance: $money($row->next_opening_balance),
                    difference: $money($row->difference),
                    status: CashReconciliationStatusEnum::from($row->status),
                    notes: $row->notes,
                ),
                $rows,
            );
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }
}
