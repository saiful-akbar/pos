<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Base\Repository;
use App\Domain\Dashboard\Entities\MonthlySummaryEntity;
use App\Domain\Dashboard\Entities\ProductSalesEntity;
use App\Domain\Dashboard\Entities\SalesTotalsEntity;
use App\Domain\Dashboard\Repositories\SaleDashboardRepositoryInterface;
use App\Models\Sale;
use DateTimeInterface;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Override;

class SaleDashboardRepository extends Repository implements SaleDashboardRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new Sale());
    }

    /**
     * Mengambil total nilai penjualan hari ini, bulan ini, dan tahun ini.
     *
     * Nilai penjualan = SUM(sales.grand_total) (nilai akhir setelah diskon, sama dengan laporan penjualan).
     * Dihitung langsung dari tabel header tanpa join detail sehingga tidak berlipat; penjualan soft delete tidak ikut.
     */
    #[Override]
    public function getSalesTotals(DateTimeInterface $today): SalesTotalsEntity
    {
        try {
            $day = $today->format('Y-m-d');
            $monthStart = $today->format('Y-m-01');
            $yearStart = $today->format('Y-01-01');

            $row = $this->model->newQuery()->toBase()
                ->whereBetween('sales.transaction_date', [$yearStart, $day])
                ->selectRaw('COALESCE(SUM(CASE WHEN sales.transaction_date = ? THEN sales.grand_total ELSE 0 END), 0) as today', [$day])
                ->selectRaw('COALESCE(SUM(CASE WHEN sales.transaction_date >= ? THEN sales.grand_total ELSE 0 END), 0) as month', [$monthStart])
                ->selectRaw('COALESCE(SUM(sales.grand_total), 0) as year')
                ->first();

            return new SalesTotalsEntity(
                today: bcadd((string) $row->today, '0', 2),
                month: bcadd((string) $row->month, '0', 2),
                year: bcadd((string) $row->year, '0', 2),
            );
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil ringkasan penjualan per bulan (selalu 12 bulan).
     *
     * Nilai (header) dan kuantitas (detail) diagregasi pada query terpisah agar nilai header tidak berlipat
     * akibat banyaknya baris detail. Kuantitas memakai satuan dasar: quantity x multiples_quantity.
     *
     * @return MonthlySummaryEntity[]
     */
    #[Override]
    public function getMonthlySummary(int $year): array
    {
        try {
            $from = sprintf('%04d-01-01', $year);
            $to = sprintf('%04d-12-31', $year);

            $amounts = $this->model->newQuery()->toBase()
                ->whereBetween('sales.transaction_date', [$from, $to])
                ->selectRaw('MONTH(sales.transaction_date) as month, COALESCE(SUM(sales.grand_total), 0) as amount')
                ->groupByRaw('MONTH(sales.transaction_date)')
                ->pluck('amount', 'month');

            $quantities = $this->connection()->table('sale_details as d')
                ->join('sales as s', 's.id', '=', 'd.sale_id')
                ->whereNull('d.deleted_at')
                ->whereNull('s.deleted_at')
                ->whereBetween('s.transaction_date', [$from, $to])
                ->selectRaw('MONTH(s.transaction_date) as month, ROUND(COALESCE(SUM(d.quantity * d.multiples_quantity), 0), 2) as quantity')
                ->groupByRaw('MONTH(s.transaction_date)')
                ->pluck('quantity', 'month');

            $summaries = [];

            for ($month = 1; $month <= 12; $month++) {
                $summaries[] = new MonthlySummaryEntity(
                    year: $year,
                    month: $month,
                    quantity: bcadd((string) ($quantities[$month] ?? '0'), '0', 2),
                    amount: bcadd((string) ($amounts[$month] ?? '0'), '0', 2),
                );
            }

            return $summaries;
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil produk terlaris berdasarkan kuantitas terjual pada rentang tanggal.
     *
     * @return ProductSalesEntity[]
     */
    #[Override]
    public function getFastMovingProducts(DateTimeInterface $fromDate, DateTimeInterface $toDate, int $limit): array
    {
        try {
            $rows = $this->connection()->query()
                ->fromSub($this->productSalesQuery($fromDate, $toDate), 'a')
                ->join('products as p', 'p.id', '=', 'a.product_id')
                ->select('p.id', 'p.code', 'p.name', 'p.current_stock', 'a.quantity', 'a.value')
                // Urut kuantitas terjual (bukan jumlah transaksi), nilai penjualan sebagai pemecah seri.
                ->orderByDesc('a.quantity')
                ->orderByDesc('a.value')
                ->orderBy('p.code')
                ->limit($limit)
                ->get();

            return $rows->map(fn (object $row): ProductSalesEntity => $this->toEntity($row))->all();
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil produk aktif yang tidak atau sedikit terjual pada rentang tanggal.
     *
     * Produk layak dianalisis jika: tidak dihapus, aktif, masih memiliki stok (> 0), dan sudah ada sampai akhir periode.
     * Produk tanpa penjualan tetap ikut (LEFT JOIN) dengan kuantitas nol dan tampil lebih dahulu.
     *
     * @return ProductSalesEntity[]
     */
    #[Override]
    public function getSlowMovingProducts(DateTimeInterface $fromDate, DateTimeInterface $toDate, int $limit): array
    {
        try {
            $rows = $this->connection()->table('products as p')
                ->leftJoinSub($this->productSalesQuery($fromDate, $toDate), 'a', 'a.product_id', '=', 'p.id')
                ->whereNull('p.deleted_at')
                ->where('p.is_active', true)
                ->where('p.current_stock', '>', 0)
                ->where('p.created_at', '<=', $toDate->format('Y-m-d').' 23:59:59')
                ->selectRaw('p.id, p.code, p.name, p.current_stock, COALESCE(a.quantity, 0) as quantity, COALESCE(a.value, 0) as value')
                ->orderByRaw('COALESCE(a.quantity, 0) asc')
                ->orderByRaw('COALESCE(a.value, 0) asc')
                ->orderBy('p.code')
                ->limit($limit)
                ->get();

            return $rows->map(fn (object $row): ProductSalesEntity => $this->toEntity($row))->all();
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Agregasi penjualan per produk pada rentang tanggal: kuantitas satuan dasar dan nilai (SUM detail.grand_total).
     * Hanya penjualan dan detail yang belum dihapus; retur tidak mengurangi (sama dengan laporan penjualan).
     */
    private function productSalesQuery(DateTimeInterface $fromDate, DateTimeInterface $toDate): Builder
    {
        return $this->connection()->table('sale_details as d')
            ->join('sales as s', 's.id', '=', 'd.sale_id')
            ->whereNull('d.deleted_at')
            ->whereNull('s.deleted_at')
            ->whereBetween('s.transaction_date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')])
            ->groupBy('d.product_id')
            ->selectRaw('d.product_id, ROUND(SUM(d.quantity * d.multiples_quantity), 2) as quantity, SUM(d.grand_total) as value');
    }

    /**
     * Memetakan baris hasil query ke entity.
     */
    private function toEntity(object $row): ProductSalesEntity
    {
        return new ProductSalesEntity(
            id: $row->id,
            code: $row->code,
            name: $row->name,
            currentStock: bcadd((string) $row->current_stock, '0', 2),
            quantity: bcadd((string) $row->quantity, '0', 2),
            value: bcadd((string) $row->value, '0', 2),
        );
    }

    /**
     * Koneksi database aplikasi.
     */
    private function connection(): Connection
    {
        return DB::connection($this->model->getConnectionName());
    }
}
