<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Report\DTOs\PurchaseReportFilterDTO;
use App\Base\Repository;
use App\Domain\Report\Entities\PurchaseReportSummaryEntity;
use App\Domain\Report\Repositories\PurchaseReportRepositoryInterface;
use App\Models\Purchase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Override;

class PurchaseReportRepository extends Repository implements PurchaseReportRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new Purchase());
    }

    /**
     * Mengambil query laporan pembelian (beserta pemasok dan petugas) sesuai filter.
     */
    #[Override]
    public function getQuery(PurchaseReportFilterDTO $filter): Builder
    {
        try {
            $columns = [
                'purchases.id as id',
                'purchases.transaction_date as transaction_date',
                'purchases.invoice_number as invoice_number',
                'purchases.total_product as total_product',
                'purchases.total_quantity as total_quantity',
                'purchases.total_price as total_price',
                'purchases.already_paid as already_paid',
                'purchases.payment_status as payment_status',
                'suppliers.name as supplier_name',
                'users.full_name as user_name',
                // Sisa utang per transaksi; pembayaran yang melebihi total dihitung 0 (konsisten dengan KPI).
                DB::raw('GREATEST(purchases.total_price - purchases.already_paid, 0) as outstanding'),
            ];

            // Soft delete diterapkan otomatis oleh model Purchase; pemasok dan petugas diambil lewat join (tanpa N+1).
            $query = $this->model
                ->newQuery()
                ->select($columns)
                ->leftJoin('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
                ->leftJoin('users', 'purchases.user_id', '=', 'users.id');

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
     * Mengambil ringkasan (KPI) laporan pembelian sesuai filter menggunakan agregasi SQL.
     */
    #[Override]
    public function getSummary(PurchaseReportFilterDTO $filter): PurchaseReportSummaryEntity
    {
        try {
            // Filter yang sama dengan tabel; seluruh KPI dihitung dalam satu query agregasi.
            $row = $this->applyFilter($this->model->newQuery(), $filter)
                ->toBase()
                ->selectRaw('COUNT(*) as total_transactions')
                ->selectRaw('COALESCE(SUM(purchases.total_price), 0) as total_purchase')
                ->selectRaw('COALESCE(SUM(purchases.already_paid), 0) as total_paid')
                // Pembayaran yang melebihi total pembelian dihitung 0 (tidak mengurangi total sisa utang).
                ->selectRaw('COALESCE(SUM(GREATEST(purchases.total_price - purchases.already_paid, 0)), 0) as total_outstanding')
                ->selectRaw('COALESCE(SUM(CASE WHEN purchases.already_paid > purchases.total_price THEN 1 ELSE 0 END), 0) as overpaid_count')
                ->first();

            return new PurchaseReportSummaryEntity(
                totalTransactions: (int) $row->total_transactions,
                totalPurchase: parse_float($row->total_purchase),
                totalPaid: parse_float($row->total_paid),
                totalOutstanding: parse_float($row->total_outstanding),
                overpaidCount: (int) $row->overpaid_count,
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
     * Menerapkan filter laporan (dipakai bersama oleh tabel, KPI, export, dan pdf).
     */
    private function applyFilter(Builder $query, PurchaseReportFilterDTO $filter): Builder
    {
        return $query
            // Periode berdasarkan tanggal transaksi (kolom ber-indeks).
            ->whereBetween('purchases.transaction_date', [
                $filter->startDate->format('Y-m-d'),
                $filter->endDate->format('Y-m-d'),
            ])
            ->when(
                $filter->supplierId !== null,
                fn (Builder $q) => $q->where('purchases.supplier_id', $filter->supplierId),
            )
            ->when(
                $filter->paymentStatus !== null,
                fn (Builder $q) => $q->where('purchases.payment_status', $filter->paymentStatus?->value),
            )
            ->when(
                $filter->invoiceNumber !== null,
                // Karakter wildcard LIKE pada input di-escape agar dicari apa adanya.
                fn (Builder $q) => $q->where(
                    'purchases.invoice_number',
                    'like',
                    '%'.addcslashes($filter->invoiceNumber, '%_\\').'%',
                ),
            );
    }
}
