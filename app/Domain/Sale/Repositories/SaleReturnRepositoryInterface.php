<?php

declare(strict_types=1);

namespace App\Domain\Sale\Repositories;

use App\Application\Sale\DTOs\SaleReturnDTO;
use App\Domain\Sale\Entities\SaleReturnEntity;
use DateTimeInterface;

interface SaleReturnRepositoryInterface
{
    /**
     * Menyimpan data pengembalian penjualan.
     */
    public function create(SaleReturnDTO $dto): SaleReturnEntity;

    /**
     * Mengambil daftar berdasarkan tanggal.
     *
     * @return SaleReturnEntity[]
     */
    public function getByDate(DateTimeInterface $fromDate, DateTimeInterface $toDate): array;
}
