<?php

declare(strict_types=1);

namespace App\Domain\Report\Repositories;

use App\Application\Report\DTOs\PurchaseReportFilterDTO;
use App\Domain\Report\Entities\PurchaseReportSummaryEntity;
use Illuminate\Database\Eloquent\Builder;

interface PurchaseReportRepositoryInterface
{
    /**
     * Mengambil query laporan pembelian (beserta pemasok dan petugas) sesuai filter.
     * Query yang sama dipakai oleh tabel, export excel, dan pdf.
     */
    public function getQuery(PurchaseReportFilterDTO $filter): Builder;

    /**
     * Mengambil ringkasan (KPI) laporan pembelian sesuai filter menggunakan agregasi SQL.
     */
    public function getSummary(PurchaseReportFilterDTO $filter): PurchaseReportSummaryEntity;
}
