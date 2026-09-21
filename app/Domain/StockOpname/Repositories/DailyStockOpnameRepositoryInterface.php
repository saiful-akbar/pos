<?php

declare(strict_types=1);

namespace App\Domain\StockOpname\Repositories;

use App\Application\StockOpname\DTOs\DailyStockOpnameDTO;
use App\Domain\StockOpname\Entities\DailyStockOpnameEntity;
use DateTimeInterface;

interface DailyStockOpnameRepositoryInterface
{
    /**
     * Mengambil daftar stock opname berdasarkan tentang tanggal.
     *
     * @return DailyStockOpnameEntity[]
     */
    public function getByDate(DateTimeInterface $fromDate, DateTimeInterface $toDate): array;

    /**
     * Mengmabil data berdasarkan id.
     */
    public function findById(string $id): ?DailyStockOpnameEntity;

    /**
     * Memeriksa apakah data ada atau tidak berdasarkan ind.
     */
    public function existsById(string $id): bool;

    /**
     * Update data.
     */
    public function update(string $id, DailyStockOpnameDTO $value): int;
}
