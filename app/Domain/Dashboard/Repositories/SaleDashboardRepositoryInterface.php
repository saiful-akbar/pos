<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Repositories;

use App\Domain\Dashboard\Entities\MonthlySummaryEntity;
use App\Domain\Dashboard\Entities\ProductSalesEntity;
use App\Domain\Dashboard\Entities\SalesTotalsEntity;
use DateTimeInterface;

interface SaleDashboardRepositoryInterface
{
    /**
     * Mengambil total nilai penjualan hari ini, bulan ini (tanggal 1 s.d. hari ini), dan tahun ini (1 Januari s.d. hari ini).
     */
    public function getSalesTotals(DateTimeInterface $today): SalesTotalsEntity;

    /**
     * Mengambil ringkasan penjualan per bulan (selalu 12 bulan, bulan tanpa transaksi bernilai nol).
     *
     * @return MonthlySummaryEntity[]
     */
    public function getMonthlySummary(int $year): array;

    /**
     * Mengambil produk terlaris berdasarkan kuantitas terjual pada rentang tanggal (inklusif).
     *
     * @return ProductSalesEntity[]
     */
    public function getFastMovingProducts(DateTimeInterface $fromDate, DateTimeInterface $toDate, int $limit): array;

    /**
     * Mengambil produk aktif yang tidak atau sedikit terjual pada rentang tanggal (inklusif), termasuk produk tanpa penjualan.
     *
     * @return ProductSalesEntity[]
     */
    public function getSlowMovingProducts(DateTimeInterface $fromDate, DateTimeInterface $toDate, int $limit): array;
}
