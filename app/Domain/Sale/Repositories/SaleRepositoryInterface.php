<?php

declare(strict_types=1);

namespace App\Domain\Sale\Repositories;

use App\Application\Sale\DTOs\SaleDTO;
use App\Domain\Sale\Entities\SaleEntity;
use DateTimeImmutable;
use DateTimeInterface;

interface SaleRepositoryInterface
{
    /**
     * Mencari data sale berdasarkan ID.
     */
    public function findById(string $id): ?SaleEntity;

    /**
     * Mencari data sale berdasarkan kolom dan value.
     */
    public function find(string $column, string|int|float|bool|null $value): ?SaleEntity;

    /**
     * Membuat data sale baru.
     */
    public function create(SaleDTO $value): SaleEntity;

    /**
     * Menghapus data sale berdasarkan ID.
     */
    public function deleteById(string $id): int;

    /**
     * Mengambil daftar sale berdasarkan rentang tanggal.
     *
     * @return array<SaleEntity>
     */
    public function getByDateRange(DateTimeInterface $startDate, DateTimeInterface $endDate): array;

    /**
     * Generate nomor invoice berdasarkan bulan transaksi.
     *
     * Format: INVyymm-00001
     */
    public function generateInvoiceNumber(DateTimeInterface $transactionDate = new DateTimeImmutable()): string;
}
