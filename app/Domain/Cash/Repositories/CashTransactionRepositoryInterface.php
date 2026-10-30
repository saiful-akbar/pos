<?php

declare(strict_types=1);

namespace App\Domain\Cash\Repositories;

use App\Application\Cash\DTOs\CashReportFilterDTO;
use App\Application\Cash\DTOs\CashTransactionDTO;
use App\Domain\Cash\Entities\CashTransactionEntity;
use App\Domain\Cash\Enums\CashSourceTypeEnum;
use Illuminate\Database\Query\Builder;

interface CashTransactionRepositoryInterface
{
    /**
     * Mengambil query transaksi kas (beserta pencatat) sesuai filter; transaksi soft delete tidak ikut.
     */
    public function getQuery(CashReportFilterDTO $filter): Builder;

    /**
     * Mengambil total kas masuk dan kas keluar sesuai filter (agregasi SQL).
     *
     * @return array{incoming: string, outgoing: string, count: int}
     */
    public function getTotals(CashReportFilterDTO $filter): array;

    /**
     * Mengambil daftar pengguna (id dan nama) untuk filter pencatat.
     *
     * @return array<int, array{id: string, name: string}>
     */
    public function getRecorders(): array;

    /**
     * Mengambil transaksi berdasarkan id.
     */
    public function findById(string $id): ?CashTransactionEntity;

    /**
     * Membuat transaksi baru beserta nomor transaksi (CSHyymm-00001) yang aman terhadap request bersamaan.
     */
    public function create(CashTransactionDTO $value): CashTransactionEntity;

    /**
     * Memperbarui transaksi (nomor transaksi tidak berubah).
     */
    public function update(string $id, CashTransactionDTO $value): int;

    /**
     * Menghapus transaksi (soft delete).
     */
    public function delete(string $id): int;

    /**
     * Menghapus (soft delete) transaksi kas otomatis berdasarkan sumber transaksinya.
     */
    public function deleteBySource(CashSourceTypeEnum $sourceType, string $sourceId): int;
}
