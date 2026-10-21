<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Sale\DTOs\SaleDTO;
use App\Base\Repository;
use App\Domain\Sale\Entities\SaleEntity;
use App\Domain\Sale\Repositories\SaleRepositoryInterface;
use App\Models\Sale;
use DateTimeImmutable;
use DateTimeInterface;
use Override;

class SaleRepository extends Repository implements SaleRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new Sale());
    }

    /**
     * Mencari data sale berdasarkan ID.
     */
    #[Override]
    public function findById(string $id): ?SaleEntity
    {
        try {
            $model = $this->model
                ->newQuery()
                ->with(['user', 'customer'])
                ->find($id);

            return $model !== null
                ? SaleEntity::fromModel($model)
                : null;
        } catch (\Throwable $e) {
            $this->errorLog(
                __METHOD__,
                $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Membuat data sale baru.
     */
    #[Override]
    public function create(SaleDTO $value): SaleEntity
    {
        try {
            $model = $this->model
                ->newQuery()
                ->create($value->toArray());

            $model->load(['user', 'customer']);

            return SaleEntity::fromModel($model);
        } catch (\Throwable $e) {
            $this->errorLog(
                __METHOD__,
                $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Menghapus data sale berdasarkan ID.
     */
    #[Override]
    public function deleteById(string $id): int
    {
        try {
            return $this->model
                ->newQuery()
                ->whereKey($id)
                ->delete();
        } catch (\Throwable $e) {
            $this->errorLog(
                __METHOD__,
                $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil daftar sale berdasarkan rentang tanggal transaksi.
     *
     * @return array<SaleEntity>
     */
    #[Override]
    public function getByDateRange(DateTimeInterface $startDate, DateTimeInterface $endDate): array
    {
        try {
            $models = $this->model
                ->newQuery()
                ->with(['user', 'customer'])
                ->whereBetween('transaction_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->orderBy('transaction_date', 'asc')
                ->get();

            return $models
                ->map(fn (Sale $model): SaleEntity => SaleEntity::fromModel($model))
                ->all();
        } catch (\Throwable $e) {
            $this->errorLog(
                __METHOD__,
                $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Generate nomor invoice berdasarkan bulan transaksi.
     *
     * Format: INVyymm-00001
     */
    #[Override]
    public function generateInvoiceNumber(DateTimeInterface $transactionDate = new DateTimeImmutable()): string
    {
        try {
            $prefix = 'INV'.$transactionDate->format('ym').'-';

            $lastInvoiceNumber = $this->model
                ->newQuery()
                ->where('invoice_number', 'like', $prefix.'%')
                ->orderByDesc('invoice_number')
                ->value('invoice_number');

            $nextNumber = $lastInvoiceNumber !== null
                ? (int) substr($lastInvoiceNumber, -5) + 1
                : 1;

            if ($nextNumber > 99999) {
                throw new \RuntimeException(
                    'Nomor invoice untuk bulan tersebut sudah mencapai batas maksimum.'
                );
            }

            return $prefix.str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);
        } catch (\Throwable $e) {
            $this->errorLog(__METHOD__, $e->getMessage());

            throw $e;
        }
    }
}
