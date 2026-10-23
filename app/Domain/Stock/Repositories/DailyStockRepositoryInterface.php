<?php

declare(strict_types=1);

namespace App\Domain\Stock\Repositories;

use App\Application\Stock\DTOs\DailyStockDTO;
use App\Domain\Stock\Entities\DailyStockEntity;
use DateTimeInterface;

interface DailyStockRepositoryInterface
{
    /**
     * Mengambil daftar stok harian berdasarkan rentang tanggal.
     *
     * @return array<DailyStockEntity>
     */
    public function getByDateRange(DateTimeInterface $startDate, DateTimeInterface $endDate): array;

    /**
     * Mengambil data stok harian beserta detailnya berdasarkan ID.
     */
    public function findById(string $id): ?DailyStockEntity;

    /**
     * Memeriksa apakah sudah ada stok harian pada tanggal tertentu.
     */
    public function existsByDate(DateTimeInterface $date): bool;

    /**
     * Membuat (insert) data stok harian baru.
     */
    public function create(DailyStockDTO $value): DailyStockEntity;

    /**
     * Update data stok harian berdasarkan ID.
     */
    public function update(string $id, DailyStockDTO $value): int;

    /**
     * Hapus permanen stok harian (detail ikut terhapus oleh relasi cascade).
     */
    public function delete(string $id): int;
}
