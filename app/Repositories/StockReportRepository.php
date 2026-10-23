<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Report\DTOs\StockReportFilterDTO;
use App\Base\Repository;
use App\Domain\Report\Entities\StockReportSummaryEntity;
use App\Domain\Report\Repositories\StockReportRepositoryInterface;
use App\Models\Product;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Override;

class StockReportRepository extends Repository implements StockReportRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new Product());
    }

    /**
     * Mengambil query rekonsiliasi stok per produk.
     *
     * Semua kuantitas dinormalisasi ke satuan dasar: quantity x multiples_quantity (aturan yang sama dengan
     * proses pembelian, penjualan, pengembalian, dan aktivasi stok yang mengubah products.current_stock).
     */
    #[Override]
    public function getQuery(StockReportFilterDTO $filter): Builder
    {
        try {
            $availability = $this->snapshotAvailability($filter);
            $reconcilable = $availability['opening'] && $availability['closing'];

            // Lapis 1: nilai mentah per produk (NULL = tidak ada data pada sumber tersebut).
            $raw = $this->connection()->table('products as p')
                ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
                ->whereNull('p.deleted_at')
                ->select('p.id', 'p.code', 'p.name', 'p.category_id', 'c.name as category_name')
                ->selectRaw($this->baseUnitSql().' as base_unit')
                ->selectRaw($this->snapshotSql($filter->year, $filter->month).' as opening_raw')
                ->selectRaw($this->snapshotSql($filter->nextYear(), $filter->nextMonth()).' as closing_raw')
                ->selectRaw($this->purchaseSql($filter).' as purchase_raw')
                ->selectRaw($this->returnSql($filter).' as return_raw')
                ->selectRaw($this->saleSql($filter).' as sale_raw');

            // Lapis 2: hanya produk yang punya snapshot atau mutasi pada periode; hitung Book Balance, selisih, status.
            $book = '(COALESCE(x.opening_raw, 0) + COALESCE(x.purchase_raw, 0) + COALESCE(x.return_raw, 0) - COALESCE(x.sale_raw, 0))';
            $difference = $reconcilable ? '(COALESCE(x.closing_raw, 0) - '.$book.')' : 'NULL';
            $status = $reconcilable
                ? "CASE WHEN ROUND({$difference}, 4) = 0 THEN 'match' WHEN {$difference} < 0 THEN 'negative' ELSE 'positive' END"
                : "'unavailable'";

            $query = $this->connection()->query()
                ->fromSub($raw, 'x')
                ->where(function ($q): void {
                    $q->whereNotNull('x.opening_raw')
                        ->orWhereNotNull('x.closing_raw')
                        ->orWhereNotNull('x.purchase_raw')
                        ->orWhereNotNull('x.return_raw')
                        ->orWhereNotNull('x.sale_raw');
                })
                ->select('x.id', 'x.code', 'x.name', 'x.category_id', 'x.category_name', 'x.base_unit')
                ->selectRaw('COALESCE(x.opening_raw, 0) as opening')
                ->selectRaw('COALESCE(x.purchase_raw, 0) as purchase_qty')
                ->selectRaw('COALESCE(x.return_raw, 0) as return_qty')
                ->selectRaw('COALESCE(x.sale_raw, 0) as sale_qty')
                ->selectRaw($book.' as book_balance')
                // Stok akhir aktual hanya ditampilkan jika snapshot bulan berikutnya tersedia (bukan dianggap nol).
                ->selectRaw(($availability['closing'] ? 'COALESCE(x.closing_raw, 0)' : 'NULL').' as closing')
                ->selectRaw($difference.' as difference')
                ->selectRaw($status.' as status');

            return $query
                ->when(
                    $filter->categoryId !== null,
                    fn (Builder $q) => $q->where('x.category_id', $filter->categoryId),
                )
                ->when($filter->keyword !== null, function (Builder $q) use ($filter): void {
                    $like = '%'.addcslashes($filter->keyword, '%_\\').'%';
                    $q->where(fn (Builder $w) => $w->where('x.code', 'like', $like)->orWhere('x.name', 'like', $like));
                })
                ->when(
                    $filter->status !== null,
                    fn (Builder $q) => $q->whereRaw('('.$status.') = ?', [$filter->status]),
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
     * Mengambil sebaran status rekonsiliasi dan ketersediaan snapshot (filter status diabaikan).
     */
    #[Override]
    public function getSummary(StockReportFilterDTO $filter): StockReportSummaryEntity
    {
        try {
            $availability = $this->snapshotAvailability($filter);

            $row = $this->connection()->query()
                ->fromSub($this->getQuery($filter->withoutStatus()), 't')
                ->selectRaw('COUNT(*) as total')
                ->selectRaw("COALESCE(SUM(t.status = 'match'), 0) as matched")
                ->selectRaw("COALESCE(SUM(t.status = 'negative'), 0) as negative")
                ->selectRaw("COALESCE(SUM(t.status = 'positive'), 0) as positive")
                ->selectRaw("COALESCE(SUM(t.status = 'unavailable'), 0) as unavailable")
                ->first();

            return new StockReportSummaryEntity(
                totalProducts: (int) $row->total,
                matched: (int) $row->matched,
                negative: (int) $row->negative,
                positive: (int) $row->positive,
                unavailable: (int) $row->unavailable,
                openingAvailable: $availability['opening'],
                closingAvailable: $availability['closing'],
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
     * Mengambil query mutasi transaksi beserta saldo berjalan.
     */
    #[Override]
    public function getMutationQuery(StockReportFilterDTO $filter, ?string $productId = null): Builder
    {
        try {
            $start = $filter->periodStart();
            $end = $filter->periodEnd();

            // Urutan jenis pada tanggal yang sama: barang masuk (pembelian, pengembalian) dahulu, baru penjualan.
            $purchases = $this->connection()->table('purchase_details as d')
                ->join('purchases as h', 'h.id', '=', 'd.purchase_id')
                ->join('units as u', 'u.id', '=', 'd.unit_id')
                ->whereNull('d.deleted_at')->whereNull('h.deleted_at')
                ->whereBetween('h.transaction_date', [$start, $end])
                ->selectRaw("'purchase' as type, 1 as type_order, h.transaction_date as date, h.invoice_number as reference")
                ->selectRaw('d.product_id, u.code as unit_code, d.quantity, d.multiples_quantity, h.user_id, h.created_at, d.id as row_id')
                ->selectRaw('(d.quantity * d.multiples_quantity) as in_qty, 0 as out_qty');

            $returns = $this->connection()->table('sale_return_details as d')
                ->join('sale_returns as h', 'h.id', '=', 'd.sale_return_id')
                ->join('units as u', 'u.id', '=', 'd.unit_id')
                ->whereNull('d.deleted_at')->whereNull('h.deleted_at')
                ->whereBetween('h.transaction_date', [$start, $end])
                ->selectRaw("'return' as type, 2 as type_order, h.transaction_date as date, h.return_number as reference")
                ->selectRaw('d.product_id, u.code as unit_code, d.quantity, d.multiples_quantity, h.user_id, h.created_at, d.id as row_id')
                ->selectRaw('(d.quantity * d.multiples_quantity) as in_qty, 0 as out_qty');

            $sales = $this->connection()->table('sale_details as d')
                ->join('sales as h', 'h.id', '=', 'd.sale_id')
                ->join('units as u', 'u.id', '=', 'd.unit_id')
                ->whereNull('d.deleted_at')->whereNull('h.deleted_at')
                ->whereBetween('h.transaction_date', [$start, $end])
                ->selectRaw("'sale' as type, 3 as type_order, h.transaction_date as date, h.invoice_number as reference")
                ->selectRaw('d.product_id, u.code as unit_code, d.quantity, d.multiples_quantity, h.user_id, h.created_at, d.id as row_id')
                ->selectRaw('0 as in_qty, (d.quantity * d.multiples_quantity) as out_qty');

            $union = $purchases->unionAll($returns)->unionAll($sales);

            $opening = '(COALESCE('.$this->snapshotSql($filter->year, $filter->month, 'm.product_id').', 0))';

            return $this->connection()->query()
                ->fromSub($union, 'm')
                ->join('products as p', 'p.id', '=', 'm.product_id')
                ->leftJoin('users as us', 'us.id', '=', 'm.user_id')
                ->when($productId !== null, fn (Builder $q) => $q->where('m.product_id', $productId))
                ->when($productId === null, function (Builder $q) use ($filter): void {
                    // Detail untuk semua produk pada laporan yang memenuhi filter.
                    $q->whereIn('m.product_id', $this->getQuery($filter)->select('x.id'));
                })
                ->select('m.type', 'm.date', 'm.reference', 'm.unit_code', 'm.quantity', 'm.multiples_quantity', 'm.in_qty', 'm.out_qty')
                ->addSelect('p.id as product_id', 'p.code as product_code', 'p.name as product_name', 'us.full_name as user_name')
                // Saldo berjalan = stok awal + mutasi bersih kumulatif (urutan deterministik).
                ->selectRaw(
                    $opening.' + SUM(m.in_qty - m.out_qty) OVER (PARTITION BY m.product_id ORDER BY m.date, m.type_order, m.created_at, m.row_id) as balance',
                )
                ->orderBy('p.code')
                ->orderBy('m.date')
                ->orderBy('m.type_order')
                ->orderBy('m.created_at')
                ->orderBy('m.row_id');
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil rincian satu produk pada laporan.
     *
     * @return array<string, mixed>|null
     */
    #[Override]
    public function findProductRow(StockReportFilterDTO $filter, string $productId): ?array
    {
        try {
            $row = $this->getQuery($filter->withoutStatus())->where('x.id', $productId)->first();

            return $row === null ? null : (array) $row;
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Koneksi database aplikasi.
     */
    private function connection(): Connection
    {
        return DB::connection($this->model->getConnectionName());
    }

    /**
     * Ketersediaan snapshot stok awal periode dan snapshot stok awal bulan berikutnya (stok akhir aktual).
     *
     * @return array{opening: bool, closing: bool}
     */
    private function snapshotAvailability(StockReportFilterDTO $filter): array
    {
        $exists = fn (int $year, int $month): bool => $this->connection()->table('monthly_stocks')
            ->whereNull('deleted_at')
            ->where('year', $year)
            ->where('month', $month)
            ->exists();

        return [
            'opening' => $exists($filter->year, $filter->month),
            'closing' => $exists($filter->nextYear(), $filter->nextMonth()),
        ];
    }

    /**
     * Subquery satuan dasar produk: satuan dengan kelipatan terkecil pada tabel harga.
     */
    private function baseUnitSql(): string
    {
        return '(SELECT u.name FROM prices pr JOIN units u ON u.id = pr.unit_id WHERE pr.product_id = p.id AND pr.deleted_at IS NULL ORDER BY pr.multiples_quantity ASC LIMIT 1)';
    }

    /**
     * Subquery stok snapshot (monthly_stocks) satu bulan untuk produk; NULL jika produk tidak ada pada snapshot.
     * Nilai ditulis sebagai literal integer karena berasal dari bilangan terverifikasi.
     */
    private function snapshotSql(int $year, int $month, string $productColumn = 'p.id'): string
    {
        return sprintf(
            '(SELECT SUM(d.quantity * d.multiples_quantity) FROM monthly_stock_details d JOIN monthly_stocks ms ON ms.id = d.monthly_stock_id WHERE ms.year = %d AND ms.month = %d AND ms.deleted_at IS NULL AND d.deleted_at IS NULL AND d.product_id = %s)',
            $year,
            $month,
            $productColumn,
        );
    }

    /**
     * Subquery total pembelian (satuan dasar) produk pada periode.
     */
    private function purchaseSql(StockReportFilterDTO $filter): string
    {
        return $this->movementSql('purchase_details', 'purchases', 'purchase_id', $filter);
    }

    /**
     * Subquery total pengembalian penjualan (satuan dasar) produk pada periode.
     */
    private function returnSql(StockReportFilterDTO $filter): string
    {
        return $this->movementSql('sale_return_details', 'sale_returns', 'sale_return_id', $filter);
    }

    /**
     * Subquery total penjualan (satuan dasar) produk pada periode.
     */
    private function saleSql(StockReportFilterDTO $filter): string
    {
        return $this->movementSql('sale_details', 'sales', 'sale_id', $filter);
    }

    /**
     * Subquery agregasi mutasi satu jenis transaksi (hanya dokumen dan detail yang belum dihapus).
     */
    private function movementSql(string $detailTable, string $headerTable, string $foreignKey, StockReportFilterDTO $filter): string
    {
        return sprintf(
            "(SELECT SUM(d.quantity * d.multiples_quantity) FROM %s d JOIN %s h ON h.id = d.%s WHERE d.product_id = p.id AND d.deleted_at IS NULL AND h.deleted_at IS NULL AND h.transaction_date BETWEEN '%s' AND '%s')",
            $detailTable,
            $headerTable,
            $foreignKey,
            $filter->periodStart(),
            $filter->periodEnd(),
        );
    }
}
