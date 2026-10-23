<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Report\DTOs\SaleReportFilterDTO;
use App\Base\Repository;
use App\Domain\Report\Entities\SaleReportSummaryEntity;
use App\Domain\Report\Repositories\SaleReportRepositoryInterface;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Override;

class SaleReportRepository extends Repository implements SaleReportRepositoryInterface
{
    /**
     * Subquery harga sebelum diskon per transaksi (SUM subtotal detail).
     */
    private const ITEMS_SUBTOTAL = '(SELECT COALESCE(SUM(sd.subtotal), 0) FROM sale_details sd WHERE sd.sale_id = sales.id AND sd.deleted_at IS NULL)';

    /**
     * Subquery kuantitas satuan dasar per transaksi (quantity x multiples_quantity).
     */
    private const BASE_QUANTITY = '(SELECT COALESCE(SUM(sd.quantity * sd.multiples_quantity), 0) FROM sale_details sd WHERE sd.sale_id = sales.id AND sd.deleted_at IS NULL)';

    /**
     * Nominal diskon per transaksi: diskon item (subtotal - grand_total detail)
     * ditambah diskon pelanggan (total_amount - grand_total). sales.discount sendiri berupa persen, bukan nominal.
     */
    private const DISCOUNT_AMOUNT = '((SELECT COALESCE(SUM(sd.subtotal - sd.grand_total), 0) FROM sale_details sd WHERE sd.sale_id = sales.id AND sd.deleted_at IS NULL) + (sales.total_amount - sales.grand_total))';

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new Sale());
    }

    /**
     * Mengambil query laporan penjualan (satu baris per transaksi) sesuai filter.
     */
    #[Override]
    public function getQuery(SaleReportFilterDTO $filter): Builder
    {
        try {
            $columns = [
                'sales.id as id',
                'sales.transaction_date as transaction_date',
                'sales.invoice_number as invoice_number',
                'sales.total_product as total_product',
                'sales.total_amount as total_amount',
                'sales.discount as discount_percent',
                'sales.grand_total as grand_total',
                'sales.paid_amount as paid_amount',
                'sales.change_amount as change_amount',
                'sales.payment_method as payment_method',
                'customers.name as customer_name',
                'users.full_name as cashier_name',
                DB::raw(self::ITEMS_SUBTOTAL.' as items_subtotal'),
                DB::raw(self::BASE_QUANTITY.' as base_quantity'),
                DB::raw(self::DISCOUNT_AMOUNT.' as discount_amount'),
            ];

            // Soft delete diterapkan otomatis oleh model Sale; pelanggan (nullable) dan kasir lewat left join (tanpa N+1
            // dan transaksi tanpa pelanggan tetap muncul). Agregat detail memakai subquery sehingga tidak ada baris ganda.
            $query = $this->model
                ->newQuery()
                ->select($columns)
                ->leftJoin('customers', 'sales.customer_id', '=', 'customers.id')
                ->leftJoin('users', 'sales.user_id', '=', 'users.id');

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
     * Mengambil ringkasan (KPI) laporan penjualan sesuai filter menggunakan agregasi SQL.
     */
    #[Override]
    public function getSummary(SaleReportFilterDTO $filter): SaleReportSummaryEntity
    {
        try {
            // Filter yang sama dengan tabel; seluruh KPI dihitung dalam satu query (satu baris sales = satu transaksi).
            $row = $this->applyFilter($this->model->newQuery(), $filter)
                ->toBase()
                ->selectRaw('COUNT(*) as total_transactions')
                ->selectRaw('COALESCE(SUM(sales.grand_total), 0) as total_sales')
                ->selectRaw('COALESCE(SUM('.self::BASE_QUANTITY.'), 0) as total_products_sold')
                ->selectRaw('COALESCE(SUM('.self::DISCOUNT_AMOUNT.'), 0) as total_discount')
                ->first();

            return new SaleReportSummaryEntity(
                totalTransactions: (int) $row->total_transactions,
                totalSales: parse_float($row->total_sales),
                totalProductsSold: parse_float($row->total_products_sold),
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
     * Mengambil pilihan filter kasir (id dan nama).
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
     * Menerapkan filter laporan (dipakai bersama oleh tabel, KPI, export, dan pdf).
     */
    private function applyFilter(Builder $query, SaleReportFilterDTO $filter): Builder
    {
        return $query
            // Periode berdasarkan tanggal transaksi (kolom ber-indeks).
            ->whereBetween('sales.transaction_date', [
                $filter->startDate->format('Y-m-d'),
                $filter->endDate->format('Y-m-d'),
            ])
            ->when($filter->userId !== null, fn (Builder $q) => $q->where('sales.user_id', $filter->userId))
            ->when($filter->paymentMethod !== null, fn (Builder $q) => $q->where('sales.payment_method', $filter->paymentMethod))
            ->when(
                $filter->invoiceNumber !== null,
                // Karakter wildcard LIKE pada input di-escape agar dicari apa adanya.
                fn (Builder $q) => $q->where(
                    'sales.invoice_number',
                    'like',
                    '%'.addcslashes($filter->invoiceNumber, '%_\\').'%',
                ),
            )
            ->when(
                $filter->categoryId !== null || $filter->productCode !== null,
                // Kategori dan produk berasal dari detail: EXISTS agar satu transaksi tidak muncul berulang.
                fn (Builder $q) => $q->whereExists(function ($sub) use ($filter): void {
                    $sub->select(DB::raw(1))
                        ->from('sale_details as fd')
                        ->join('products as fp', 'fp.id', '=', 'fd.product_id')
                        ->whereColumn('fd.sale_id', 'sales.id')
                        ->whereNull('fd.deleted_at')
                        ->when($filter->categoryId !== null, fn ($s) => $s->where('fp.category_id', $filter->categoryId))
                        ->when($filter->productCode !== null, fn ($s) => $s->where('fp.code', $filter->productCode));
                }),
            );
    }
}
