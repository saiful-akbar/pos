<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Report\DTOs\SaleReturnReportFilterDTO;
use App\Base\Repository;
use App\Domain\Report\Entities\SaleReturnReportSummaryEntity;
use App\Domain\Report\Repositories\SaleReturnReportRepositoryInterface;
use App\Models\SaleReturn;
use App\Models\SaleReturnDetail;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Override;

class SaleReturnReportRepository extends Repository implements SaleReturnReportRepositoryInterface
{
    /**
     * Subquery total harga sebelum diskon per dokumen (SUM subtotal detail).
     */
    private const ITEMS_SUBTOTAL = '(SELECT COALESCE(SUM(rd.subtotal), 0) FROM sale_return_details rd WHERE rd.sale_return_id = sale_returns.id AND rd.deleted_at IS NULL)';

    /**
     * Subquery kuantitas satuan dasar per dokumen (quantity x multiples_quantity).
     */
    private const BASE_QUANTITY = '(SELECT COALESCE(SUM(rd.quantity * rd.multiples_quantity), 0) FROM sale_return_details rd WHERE rd.sale_return_id = sale_returns.id AND rd.deleted_at IS NULL)';

    /**
     * Nominal diskon per dokumen: diskon item (subtotal - grand_total detail) ditambah diskon penjualan
     * (total_amount - grand_total). sale_returns.discount sendiri berupa persen (disalin dari penjualan), bukan nominal.
     */
    private const DISCOUNT_AMOUNT = '((SELECT COALESCE(SUM(rd.subtotal - rd.grand_total), 0) FROM sale_return_details rd WHERE rd.sale_return_id = sale_returns.id AND rd.deleted_at IS NULL) + (sale_returns.total_amount - sale_returns.grand_total))';

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new SaleReturn());
    }

    /**
     * Mengambil query laporan pengembalian (satu baris per dokumen) sesuai filter.
     */
    #[Override]
    public function getQuery(SaleReturnReportFilterDTO $filter): Builder
    {
        try {
            $columns = [
                'sale_returns.id as id',
                'sale_returns.transaction_date as transaction_date',
                'sale_returns.return_number as return_number',
                'sale_returns.total_product as total_product',
                'sale_returns.total_amount as total_amount',
                'sale_returns.discount as discount_percent',
                'sale_returns.grand_total as grand_total',
                'sale_returns.notes as notes',
                'sales.invoice_number as invoice_number',
                'sales.transaction_date as sale_date',
                'customers.name as customer_name',
                'users.full_name as user_name',
                DB::raw(self::ITEMS_SUBTOTAL.' as items_subtotal'),
                DB::raw(self::BASE_QUANTITY.' as base_quantity'),
                DB::raw(self::DISCOUNT_AMOUNT.' as discount_amount'),
            ];

            // Soft delete diterapkan otomatis oleh model SaleReturn. Penjualan asal, pelanggan (nullable), dan petugas
            // diambil lewat left join (tanpa N+1); agregat detail memakai subquery sehingga tidak ada baris ganda.
            $query = $this->model
                ->newQuery()
                ->select($columns)
                ->leftJoin('sales', 'sale_returns.sale_id', '=', 'sales.id')
                ->leftJoin('customers', 'sales.customer_id', '=', 'customers.id')
                ->leftJoin('users', 'sale_returns.user_id', '=', 'users.id');

            return $this->applyFilter($query, $filter);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil ringkasan (KPI) laporan pengembalian sesuai filter menggunakan agregasi SQL.
     */
    #[Override]
    public function getSummary(SaleReturnReportFilterDTO $filter): SaleReturnReportSummaryEntity
    {
        try {
            // Satu baris sale_returns = satu dokumen, sehingga nilai header tidak tergandakan.
            $row = $this->applyFilter($this->model->newQuery(), $filter)
                ->toBase()
                ->selectRaw('COUNT(*) as total_returns')
                ->selectRaw('COALESCE(SUM(sale_returns.grand_total), 0) as total_value')
                ->selectRaw('COALESCE(SUM('.self::BASE_QUANTITY.'), 0) as total_quantity')
                ->selectRaw('COALESCE(SUM('.self::DISCOUNT_AMOUNT.'), 0) as total_discount')
                ->first();

            // Produk unik yang dikembalikan pada dokumen yang memenuhi filter.
            $totalProducts = SaleReturnDetail::query()
                ->whereIn('sale_return_id', $this->filteredIds($filter))
                ->distinct()
                ->count('product_id');

            return new SaleReturnReportSummaryEntity(
                totalReturns: (int) $row->total_returns,
                totalValue: parse_float($row->total_value),
                totalQuantity: parse_float($row->total_quantity),
                totalProducts: (int) $totalProducts,
                totalDiscount: parse_float($row->total_discount),
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
     * Mengambil query detail produk satu dokumen pengembalian (beserta produk dan unit).
     */
    #[Override]
    public function getDetailQuery(string $saleReturnId): Builder
    {
        try {
            $columns = [
                'sale_return_details.id as id',
                'sale_return_details.multiples_quantity as multiples_quantity',
                'sale_return_details.selling_price as selling_price',
                'sale_return_details.quantity as quantity',
                'sale_return_details.subtotal as subtotal',
                'sale_return_details.discount as discount',
                'sale_return_details.grand_total as grand_total',
                'products.code as product_code',
                'products.name as product_name',
                'units.code as unit_code',
                'units.name as unit_name',
            ];

            return SaleReturnDetail::query()
                ->select($columns)
                ->leftJoin('products', 'sale_return_details.product_id', '=', 'products.id')
                ->leftJoin('units', 'sale_return_details.unit_id', '=', 'units.id')
                ->where('sale_return_details.sale_return_id', $saleReturnId);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil peringkat produk dan kategori yang paling banyak dikembalikan pada dokumen yang memenuhi filter.
     *
     * @return array{products: array<int, array<string, mixed>>, categories: array<int, array<string, mixed>>}
     */
    #[Override]
    public function getAnalysis(SaleReturnReportFilterDTO $filter, int $limit = 5): array
    {
        try {
            // Peringkat berdasarkan kuantitas satuan dasar (quantity x kelipatan) agar satuan berbeda sebanding.
            $base = fn () => DB::connection($this->model->getConnectionName())
                ->table('sale_return_details as d')
                ->join('products as p', 'p.id', '=', 'd.product_id')
                ->whereNull('d.deleted_at')
                ->whereIn('d.sale_return_id', $this->filteredIds($filter))
                ->selectRaw('SUM(d.quantity * d.multiples_quantity) as total_quantity')
                ->selectRaw('SUM(d.grand_total) as total_value')
                ->orderByDesc('total_quantity')
                ->limit($limit);

            $products = $base()
                ->addSelect('p.code as code', 'p.name as name')
                ->groupBy('p.id', 'p.code', 'p.name')
                ->get();

            $categories = $base()
                ->join('categories as c', 'c.id', '=', 'p.category_id')
                ->addSelect('c.code as code', 'c.name as name')
                ->groupBy('c.id', 'c.code', 'c.name')
                ->get();

            $map = fn ($row): array => [
                'code' => $row->code,
                'name' => $row->name,
                'total_quantity' => parse_float($row->total_quantity),
                'total_value' => parse_float($row->total_value),
            ];

            return [
                'products' => $products->map($map)->all(),
                'categories' => $categories->map($map)->all(),
            ];
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil pilihan filter petugas (id dan nama).
     */
    #[Override]
    public function getFilterOptions(): array
    {
        try {
            return [
                'cashiers' => User::query()
                    ->orderBy('full_name')
                    ->get(['id', 'full_name'])
                    ->map(fn (User $user): array => ['id' => $user->id, 'name' => $user->full_name])
                    ->all(),
            ];
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Subquery id dokumen yang memenuhi filter.
     */
    private function filteredIds(SaleReturnReportFilterDTO $filter): Builder
    {
        return $this->applyFilter($this->model->newQuery(), $filter)->select('sale_returns.id');
    }

    /**
     * Menerapkan filter laporan (dipakai bersama oleh tabel, KPI, analisis, export, dan pdf).
     * Filter berbasis relasi memakai subquery sehingga tidak bergantung pada join dan tidak menggandakan dokumen.
     */
    private function applyFilter(Builder $query, SaleReturnReportFilterDTO $filter): Builder
    {
        $like = fn (string $value): string => '%'.addcslashes($value, '%_\\').'%';

        return $query
            // Periode berdasarkan tanggal pengembalian.
            ->whereBetween('sale_returns.transaction_date', [
                $filter->startDate->format('Y-m-d'),
                $filter->endDate->format('Y-m-d'),
            ])
            ->when(
                $filter->returnNumber !== null,
                fn (Builder $q) => $q->where('sale_returns.return_number', 'like', $like($filter->returnNumber)),
            )
            ->when(
                $filter->userId !== null,
                fn (Builder $q) => $q->where('sale_returns.user_id', $filter->userId),
            )
            ->when(
                $filter->invoiceNumber !== null,
                // Invoice penjualan asal.
                fn (Builder $q) => $q->whereIn('sale_returns.sale_id', function ($sub) use ($filter, $like): void {
                    $sub->select('id')->from('sales')->where('invoice_number', 'like', $like($filter->invoiceNumber));
                }),
            )
            ->when(
                $filter->customerName !== null,
                // Pelanggan melalui penjualan asal.
                fn (Builder $q) => $q->whereIn('sale_returns.sale_id', function ($sub) use ($filter, $like): void {
                    $sub->select('s.id')
                        ->from('sales as s')
                        ->join('customers as c', 'c.id', '=', 's.customer_id')
                        ->where('c.name', 'like', $like($filter->customerName));
                }),
            )
            ->when(
                $filter->categoryId !== null || $filter->productCode !== null,
                // Kategori dan produk berasal dari detail: EXISTS agar satu dokumen tidak muncul berulang.
                fn (Builder $q) => $q->whereExists(function ($sub) use ($filter): void {
                    $sub->select(DB::raw(1))
                        ->from('sale_return_details as fd')
                        ->join('products as fp', 'fp.id', '=', 'fd.product_id')
                        ->whereColumn('fd.sale_return_id', 'sale_returns.id')
                        ->whereNull('fd.deleted_at')
                        ->when($filter->categoryId !== null, fn ($s) => $s->where('fp.category_id', $filter->categoryId))
                        ->when($filter->productCode !== null, fn ($s) => $s->where('fp.code', $filter->productCode));
                }),
            );
    }
}
