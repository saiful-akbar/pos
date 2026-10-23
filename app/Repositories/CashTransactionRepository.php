<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Cash\DTOs\CashReportFilterDTO;
use App\Application\Cash\DTOs\CashTransactionDTO;
use App\Base\Repository;
use App\Domain\Cash\Entities\CashTransactionEntity;
use App\Domain\Cash\Repositories\CashTransactionRepositoryInterface;
use App\Models\CashTransaction;
use App\Models\User;
use DateTimeInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\QueryException;
use Override;

class CashTransactionRepository extends Repository implements CashTransactionRepositoryInterface
{
    /**
     * Jumlah maksimum percobaan membuat nomor transaksi ketika terjadi benturan nomor.
     */
    private const MAX_NUMBER_ATTEMPTS = 5;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new CashTransaction());
    }

    /**
     * Mengambil query transaksi kas (beserta pencatat) sesuai filter; transaksi soft delete tidak ikut.
     */
    #[Override]
    public function getQuery(CashReportFilterDTO $filter): Builder
    {
        try {
            $columns = [
                'cash_transactions.id as id',
                'cash_transactions.transaction_number as transaction_number',
                'cash_transactions.transaction_date as transaction_date',
                'cash_transactions.type as type',
                'cash_transactions.amount as amount',
                'cash_transactions.notes as notes',
                'cash_transactions.reference_number as reference_number',
                'users.full_name as user_name',
            ];

            // Scope soft delete model diterapkan lewat toBase(); pencatat diambil lewat join (tanpa N+1).
            return $this->applyFilter(
                $this->model->newQuery()
                    ->select($columns)
                    ->leftJoin('users', 'users.id', '=', 'cash_transactions.user_id')
                    ->toBase(),
                $filter,
            );
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil total kas masuk dan kas keluar sesuai filter (agregasi SQL).
     *
     * @return array{incoming: string, outgoing: string, count: int}
     */
    #[Override]
    public function getTotals(CashReportFilterDTO $filter): array
    {
        try {
            $row = $this->applyFilter($this->model->newQuery()->toBase(), $filter)
                ->selectRaw("COALESCE(SUM(CASE WHEN cash_transactions.type = 'INCOMING' THEN cash_transactions.amount ELSE 0 END), 0) as incoming")
                ->selectRaw("COALESCE(SUM(CASE WHEN cash_transactions.type = 'OUTGOING' THEN cash_transactions.amount ELSE 0 END), 0) as outgoing")
                ->selectRaw('COUNT(*) as total')
                ->first();

            return [
                'incoming' => bcadd((string) $row->incoming, '0', 2),
                'outgoing' => bcadd((string) $row->outgoing, '0', 2),
                'count' => (int) $row->total,
            ];
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil daftar pengguna (id dan nama) untuk filter pencatat.
     *
     * @return array<int, array{id: string, name: string}>
     */
    #[Override]
    public function getRecorders(): array
    {
        try {
            return User::query()
                ->orderBy('full_name')
                ->get(['id', 'full_name'])
                ->map(fn (User $user): array => ['id' => $user->id, 'name' => $user->full_name])
                ->all();
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil transaksi berdasarkan id.
     */
    #[Override]
    public function findById(string $id): ?CashTransactionEntity
    {
        try {
            $model = $this->model->newQuery()->with('user')->find($id);

            return $model !== null
                ? CashTransactionEntity::fromModel($model)
                : null;
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Membuat transaksi baru beserta nomor transaksi (CSHyymm-00001).
     *
     * Aman terhadap request bersamaan: nomor urut diambil dengan menaikkan penghitung bulan tersebut pada tabel
     * cash_transaction_sequences. Baris penghitung terkunci sampai transaksi database selesai, sehingga request lain
     * pada bulan yang sama menunggu dan mendapat nomor berikutnya (tanpa duplikat); jika transaksi dibatalkan,
     * kenaikan penghitung ikut dibatalkan. Indeks unik transaction_number tetap menjadi pengaman terakhir.
     */
    #[Override]
    public function create(CashTransactionDTO $value): CashTransactionEntity
    {
        try {
            for ($attempt = 1; $attempt <= self::MAX_NUMBER_ATTEMPTS; $attempt++) {
                try {
                    $model = $this->model->newQuery()->create([
                        ...$value->toArray(),
                        'transaction_number' => $this->nextTransactionNumber($value->transactionDate),
                    ]);

                    return CashTransactionEntity::fromModel($model->load('user'));
                } catch (QueryException $e) {
                    // Hanya benturan nomor transaksi yang diulang; kesalahan lain dilempar kembali.
                    if (! $this->isDuplicateNumber($e) || $attempt === self::MAX_NUMBER_ATTEMPTS) {
                        throw $e;
                    }
                }
            }

            throw new \RuntimeException('Gagal membuat nomor transaksi kas.');
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Memperbarui transaksi (nomor transaksi tidak berubah).
     */
    #[Override]
    public function update(string $id, CashTransactionDTO $value): int
    {
        try {
            // user_id pencatat awal dipertahankan; hanya data transaksi yang diperbarui.
            return $this->model->newQuery()
                ->whereKey($id)
                ->update([
                    'transaction_date' => $value->transactionDate->format('Y-m-d'),
                    'type' => $value->type->value,
                    'amount' => $value->amount,
                    'notes' => $value->notes,
                    'reference_number' => $value->referenceNumber,
                ]);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Menghapus transaksi (soft delete).
     */
    #[Override]
    public function delete(string $id): int
    {
        try {
            return $this->model->newQuery()->whereKey($id)->delete();
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Menentukan nomor transaksi berikutnya untuk bulan transaksi: CSH + yymm + '-' + urutan 5 digit.
     */
    protected function nextTransactionNumber(DateTimeInterface $transactionDate): string
    {
        $period = $transactionDate->format('ym');
        $prefix = 'CSH'.$period.'-';
        $connection = $this->model->getConnection();

        // Nilai awal penghitung (hanya dipakai saat baris bulan ini dibuat pertama kali): nomor terakhir yang
        // sudah ada pada bulan tersebut, termasuk transaksi soft delete karena nomornya tetap terpakai.
        $last = $this->model->newQuery()
            ->withTrashed()
            ->where('transaction_number', 'like', $prefix.'%')
            ->max('transaction_number');

        $initial = $last !== null ? ((int) substr((string) $last, -5)) + 1 : 1;

        // Naikkan penghitung secara atomik; baris terkunci hingga transaksi database selesai.
        $connection->statement(
            'INSERT INTO cash_transaction_sequences (period, last_number, created_at, updated_at) VALUES (?, ?, NOW(), NOW()) '
            .'ON DUPLICATE KEY UPDATE last_number = last_number + 1, updated_at = NOW()',
            [$period, $initial],
        );

        $next = (int) $connection->table('cash_transaction_sequences')->where('period', $period)->value('last_number');

        if ($next > 99999) {
            throw new \RuntimeException('Nomor transaksi kas untuk bulan tersebut sudah mencapai batas maksimum.');
        }

        return $prefix.str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Apakah kesalahan database berupa benturan indeks unik nomor transaksi.
     */
    private function isDuplicateNumber(QueryException $e): bool
    {
        return ($e->errorInfo[1] ?? null) === 1062 && str_contains($e->getMessage(), 'transaction_number');
    }

    /**
     * Menerapkan filter periode, jenis, dan pencatat.
     */
    private function applyFilter(Builder $query, CashReportFilterDTO $filter): Builder
    {
        return $query
            ->whereBetween('cash_transactions.transaction_date', [
                $filter->startDate->format('Y-m-d'),
                $filter->endDate->format('Y-m-d'),
            ])
            ->when($filter->type !== null, fn (Builder $q) => $q->where('cash_transactions.type', $filter->type?->value))
            ->when($filter->userId !== null, fn (Builder $q) => $q->where('cash_transactions.user_id', $filter->userId));
    }
}
