<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Sale\DTOs\SaleReturnDTO;
use App\Base\Repository;
use App\Domain\Sale\Entities\SaleReturnEntity;
use App\Domain\Sale\Repositories\SaleReturnRepositoryInterface;
use App\Models\SaleReturn;
use DateTimeImmutable;
use DateTimeInterface;
use Override;

class SaleReturnRepository extends Repository implements SaleReturnRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new SaleReturn());
    }

    /**
     * Mengambil daftar sale return berdasarkan rentang tanggal transaksi.
     *
     * @return array<SaleReturnEntity>
     */
    #[Override]
    public function getByDateRange(DateTimeInterface $startDate, DateTimeInterface $endDate): array
    {
        try {
            $models = $this->model
                ->newQuery()
                ->with(['user'])
                ->whereBetween('transaction_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->orderBy('transaction_date', 'asc')
                ->get();

            return $models
                ->map(fn (SaleReturn $model): SaleReturnEntity => SaleReturnEntity::fromModel($model))
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
     * Membuat (insert) data sale return baru.
     */
    #[Override]
    public function create(SaleReturnDTO $value): SaleReturnEntity
    {
        try {
            // Simpan pengembalian lalu muat relasi user untuk entity.
            $model = $this->model
                ->newQuery()
                ->create($value->toArray());

            $model->load(['user']);

            return SaleReturnEntity::fromModel($model);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Menghapus (soft delete) data sale return berdasarkan ID.
     */
    #[Override]
    public function removeById(string $id): int
    {
        try {
            // Soft delete: hanya mengisi deleted_at.
            return $this->model
                ->newQuery()
                ->whereKey($id)
                ->delete();
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Membuat nomor pengembalian baru.
     *
     * Format: RTNyymm-00001
     */
    #[Override]
    public function generateReturnNumber(DateTimeInterface $transactionDate = new DateTimeImmutable()): string
    {
        try {
            // Prefix berisi tahun dan bulan sehingga urutan reset setiap bulan.
            $prefix = 'RTN'.$transactionDate->format('ym').'-';

            // Ambil nomor terakhir pada bulan tersebut (dikunci dalam transaksi, termasuk yang soft delete).
            $lastReturnNumber = $this->model
                ->newQuery()
                ->withTrashed()
                ->where('return_number', 'like', $prefix.'%')
                ->orderByDesc('return_number')
                ->lockForUpdate()
                ->value('return_number');

            $nextNumber = $lastReturnNumber !== null
                ? (int) substr($lastReturnNumber, -5) + 1
                : 1;

            if ($nextNumber > 99999) {
                throw new \RuntimeException(
                    'Nomor pengembalian untuk bulan tersebut sudah mencapai batas maksimum.'
                );
            }

            return $prefix.str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }
}
