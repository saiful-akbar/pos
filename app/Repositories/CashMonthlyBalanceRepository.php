<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Cash\DTOs\CashMonthlyBalanceDTO;
use App\Base\Repository;
use App\Domain\Cash\Entities\CashMonthlyBalanceEntity;
use App\Domain\Cash\Repositories\CashMonthlyBalanceRepositoryInterface;
use App\Models\CashMonthlyBalance;
use Override;

class CashMonthlyBalanceRepository extends Repository implements CashMonthlyBalanceRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new CashMonthlyBalance());
    }

    /**
     * Mengambil saldo awal berdasarkan periode (tahun dan bulan); null jika belum dicatat.
     */
    #[Override]
    public function findByPeriod(int $year, int $month): ?CashMonthlyBalanceEntity
    {
        try {
            $model = $this->model->newQuery()
                ->with('user')
                ->where('year', $year)
                ->where('month', $month)
                ->first();

            return $model !== null
                ? CashMonthlyBalanceEntity::fromModel($model)
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
     * Membuat saldo awal periode; periode yang pernah di-soft delete dipulihkan (indeks unik tetap terpenuhi).
     */
    #[Override]
    public function create(CashMonthlyBalanceDTO $value): CashMonthlyBalanceEntity
    {
        try {
            // Cari baris periode yang sama termasuk yang sudah di-soft delete.
            $model = $this->model->newQuery()
                ->withTrashed()
                ->where('year', $value->year)
                ->where('month', $value->month)
                ->first();

            if ($model === null) {
                $model = $this->model->newQuery()->create($value->toArray());
            } else {
                $model->restore();
                $model->fill($value->toArray())->save();
            }

            return CashMonthlyBalanceEntity::fromModel($model->load('user'));
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Memperbarui saldo awal pada periode yang sama.
     */
    #[Override]
    public function update(string $id, CashMonthlyBalanceDTO $value): int
    {
        try {
            // Periode (tahun dan bulan) tidak diubah; hanya saldo, catatan, dan pencatat terakhir.
            return $this->model->newQuery()
                ->whereKey($id)
                ->update([
                    'user_id' => $value->userId,
                    'opening_balance' => $value->openingBalance,
                    'notes' => $value->notes,
                ]);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }
}
