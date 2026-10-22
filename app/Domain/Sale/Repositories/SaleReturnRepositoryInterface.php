<?php

declare(strict_types=1);

namespace App\Domain\Sale\Repositories;

use App\Application\Sale\DTOs\SaleReturnDTO;
use App\Domain\Sale\Entities\SaleReturnEntity;
use DateTimeImmutable;
use DateTimeInterface;

interface SaleReturnRepositoryInterface
{
    /**
     * Mengambil daftar sale return berdasarkan rentang tanggal transaksi.
     *
     * @return array<SaleReturnEntity>
     */
    public function getByDateRange(DateTimeInterface $startDate, DateTimeInterface $endDate): array;

    /**
     * Membuat (insert) data sale return baru.
     */
    public function create(SaleReturnDTO $value): SaleReturnEntity;

    /**
     * Menghapus (soft delete) data sale return berdasarkan ID.
     */
    public function removeById(string $id): int;

    /**
     * Membuat nomor pengembalian baru.
     *
     * Format: RTNyymm-00001
     */
    public function generateReturnNumber(DateTimeInterface $transactionDate = new DateTimeImmutable()): string;
}
