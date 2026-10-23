<?php

declare(strict_types=1);

namespace App\Domain\Cash\Repositories;

use App\Application\Cash\DTOs\CashMonthlyBalanceDTO;
use App\Domain\Cash\Entities\CashMonthlyBalanceEntity;

interface CashMonthlyBalanceRepositoryInterface
{
    /**
     * Mengambil saldo awal berdasarkan periode (tahun dan bulan); null jika belum dicatat.
     */
    public function findByPeriod(int $year, int $month): ?CashMonthlyBalanceEntity;

    /**
     * Membuat saldo awal periode. Jika periode pernah di-soft delete, baris lama dipulihkan dan diperbarui
     * (tidak membuat baris ganda).
     */
    public function create(CashMonthlyBalanceDTO $value): CashMonthlyBalanceEntity;

    /**
     * Memperbarui saldo awal pada periode yang sama.
     */
    public function update(string $id, CashMonthlyBalanceDTO $value): int;
}
