<?php

declare(strict_types=1);

namespace App\Domain\Report\Repositories;

use App\Application\Report\DTOs\SaleReturnReportFilterDTO;
use App\Domain\Report\Entities\SaleReturnReportSummaryEntity;
use Illuminate\Database\Eloquent\Builder;

interface SaleReturnReportRepositoryInterface
{
    /**
     * Mengambil query laporan pengembalian (satu baris per dokumen) sesuai filter.
     * Query yang sama dipakai oleh tabel, export excel, dan pdf.
     */
    public function getQuery(SaleReturnReportFilterDTO $filter): Builder;

    /**
     * Mengambil ringkasan (KPI) laporan pengembalian sesuai filter menggunakan agregasi SQL.
     */
    public function getSummary(SaleReturnReportFilterDTO $filter): SaleReturnReportSummaryEntity;

    /**
     * Mengambil query detail produk satu dokumen pengembalian (beserta produk dan unit).
     */
    public function getDetailQuery(string $saleReturnId): Builder;

    /**
     * Mengambil peringkat produk dan kategori yang paling banyak dikembalikan pada dokumen yang memenuhi filter.
     *
     * @return array{products: array<int, array<string, mixed>>, categories: array<int, array<string, mixed>>}
     */
    public function getAnalysis(SaleReturnReportFilterDTO $filter, int $limit = 5): array;

    /**
     * Mengambil pilihan filter petugas (id dan nama).
     *
     * @return array{cashiers: array<int, array{id: string, name: string}>}
     */
    public function getFilterOptions(): array;
}
