<?php

declare(strict_types=1);

namespace App\Domain\Report\Repositories;

use App\Application\Report\DTOs\StockReportFilterDTO;
use App\Domain\Report\Entities\StockReportSummaryEntity;
use Illuminate\Database\Query\Builder;

interface StockReportRepositoryInterface
{
    /**
     * Mengambil query rekonsiliasi stok per produk (stok awal, mutasi, book balance, stok akhir aktual, selisih, status).
     * Query yang sama dipakai oleh tabel, export excel, dan pdf.
     */
    public function getQuery(StockReportFilterDTO $filter): Builder;

    /**
     * Mengambil sebaran status rekonsiliasi dan ketersediaan snapshot (filter status diabaikan).
     */
    public function getSummary(StockReportFilterDTO $filter): StockReportSummaryEntity;

    /**
     * Mengambil query mutasi transaksi (pembelian, pengembalian, penjualan) beserta saldo berjalan.
     * Jika $productId diisi, hanya produk tersebut; jika tidak, semua produk pada laporan yang memenuhi filter.
     */
    public function getMutationQuery(StockReportFilterDTO $filter, ?string $productId = null): Builder;

    /**
     * Mengambil rincian satu produk pada laporan (stok awal, mutasi, book balance, stok akhir aktual, selisih).
     *
     * @return array<string, mixed>|null
     */
    public function findProductRow(StockReportFilterDTO $filter, string $productId): ?array;
}
