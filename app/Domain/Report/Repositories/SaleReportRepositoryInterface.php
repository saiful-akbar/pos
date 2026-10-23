<?php

declare(strict_types=1);

namespace App\Domain\Report\Repositories;

use App\Application\Report\DTOs\SaleReportFilterDTO;
use App\Domain\Report\Entities\SaleReportSummaryEntity;
use Illuminate\Database\Eloquent\Builder;

interface SaleReportRepositoryInterface
{
    /**
     * Mengambil query laporan penjualan (satu baris per transaksi) sesuai filter.
     * Query yang sama dipakai oleh tabel, export excel, dan pdf.
     */
    public function getQuery(SaleReportFilterDTO $filter): Builder;

    /**
     * Mengambil ringkasan (KPI) laporan penjualan sesuai filter menggunakan agregasi SQL.
     */
    public function getSummary(SaleReportFilterDTO $filter): SaleReportSummaryEntity;

    /**
     * Mengambil pilihan filter kasir (id dan nama).
     *
     * @return array{cashiers: array<int, array{id: string, name: string}>}
     */
    public function getFilterOptions(): array;
}
