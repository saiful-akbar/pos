<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Application\Purchase\UseCases\GetPurchaseDetailTableByPurchaseId;
use App\Application\Purchase\UseCases\GetPurchasePaymentsTableByPurchaseId;
use App\Application\Report\UseCases\GetPurchaseReportQuery;
use App\Application\Report\UseCases\GetPurchaseReportSummary;
use App\Application\Report\UseCases\GetPurchaseReportTable;
use App\Application\Report\UseCases\GetPurchaseTransactionReport;
use App\Application\Supplier\UseCases\GetAllSuppliers;
use App\Exports\Reports\PurchaseReportExport;
use App\Exports\Reports\PurchaseTransactionExport;
use App\Http\Requests\Reports\GetPurchaseReportRequest;
use Barryvdh\DomPDF\Facade\Pdf as DomPdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class PurchaseReportController extends Controller
{
    /**
     * Menampilkan halaman laporan pembelian, atau (ajax) data tabel laporan (DataTables server side).
     */
    public function index(GetPurchaseReportRequest $request): View|JsonResponse
    {
        // Request ajax: kembalikan data tabel sesuai filter.
        if ($request->ajax()) {
            return app(GetPurchaseReportTable::class)->execute(
                filter: $request->toFilter(),
            );
        }

        // Request biasa: tampilkan halaman beserta hak akses user dan daftar pemasok untuk filter.
        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: 'reports.purchases',
        );

        $suppliers = app(GetAllSuppliers::class)->execute();

        return view(
            view: 'pages.reports.purchases.index',
            data: compact('access', 'suppliers'),
        );
    }

    /**
     * Mengambil ringkasan (KPI) laporan pembelian sesuai filter (ajax).
     */
    public function summary(GetPurchaseReportRequest $request): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $summary = app(GetPurchaseReportSummary::class)->execute(
            filter: $request->toFilter(),
        );

        return $this->jsonResponse(
            data: $summary->toArray(),
        );
    }

    /**
     * Mengunduh laporan pembelian dalam file excel (filter sama dengan halaman laporan).
     */
    public function exportExcel(GetPurchaseReportRequest $request): BinaryFileResponse
    {
        $filter = $request->toFilter();

        $export = new PurchaseReportExport(
            query: app(GetPurchaseReportQuery::class)->execute(filter: $filter),
            filter: $filter,
            summary: app(GetPurchaseReportSummary::class)->execute(filter: $filter),
            supplierName: $this->supplierName($filter->supplierId),
        );

        return Excel::download(
            $export,
            sprintf(
                'laporan-pembelian-%s-%s.xlsx',
                $filter->startDate->format('Y-m-d'),
                $filter->endDate->format('Y-m-d'),
            ),
        );
    }

    /**
     * Mencetak laporan pembelian dalam file pdf (filter sama dengan halaman laporan).
     */
    public function exportPdf(GetPurchaseReportRequest $request): Response
    {
        $filter = $request->toFilter();

        $pdf = DomPdf::loadView('pages.reports.purchases.pdf', [
            'filter' => $filter,
            'summary' => app(GetPurchaseReportSummary::class)->execute(filter: $filter),
            'rows' => app(GetPurchaseReportQuery::class)->execute(filter: $filter)->get(),
            'supplierName' => $this->supplierName($filter->supplierId),
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => sprintf(
                'inline; filename="laporan-pembelian-%s-%s.pdf"',
                $filter->startDate->format('Y-m-d'),
                $filter->endDate->format('Y-m-d'),
            ),
            'Cache-Control' => 'no-store, private',
        ]);
    }

    /**
     * Mengambil daftar detail produk satu transaksi pembelian (DataTables server side).
     */
    public function transactionDetails(Request $request, string $purchaseId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        return app(GetPurchaseDetailTableByPurchaseId::class)->execute(
            purchaseId: $purchaseId,
        );
    }

    /**
     * Mengambil daftar pembayaran satu transaksi pembelian (DataTables server side).
     */
    public function transactionPayments(Request $request, string $purchaseId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        return app(GetPurchasePaymentsTableByPurchaseId::class)->execute(
            purchaseId: $purchaseId,
        );
    }

    /**
     * Mengunduh detail produk dan pembayaran satu transaksi pembelian dalam file excel.
     */
    public function exportTransactionExcel(string $purchaseId): BinaryFileResponse
    {
        $transaction = app(GetPurchaseTransactionReport::class)->execute(purchaseId: $purchaseId);

        abort_if($transaction === null, 404, 'Data pembelian tidak ditemukan.');

        return Excel::download(
            new PurchaseTransactionExport($transaction),
            'detail-pembelian-'.$transaction->purchase->transactionDate->format('Y-m-d').'-'.$transaction->purchase->id.'.xlsx',
        );
    }

    /**
     * Mencetak detail produk dan daftar pembayaran satu transaksi pembelian dalam file pdf.
     */
    public function exportTransactionPdf(string $purchaseId): Response
    {
        $transaction = app(GetPurchaseTransactionReport::class)->execute(purchaseId: $purchaseId);

        abort_if($transaction === null, 404, 'Data pembelian tidak ditemukan.');

        $pdf = DomPdf::loadView('pages.reports.purchases.transaction-pdf', [
            'transaction' => $transaction,
            'generatedAt' => now(),
        ])->setPaper('a4', 'portrait');

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="detail-pembelian-'.$transaction->purchase->transactionDate->format('Y-m-d').'.pdf"',
            'Cache-Control' => 'no-store, private',
        ]);
    }

    /**
     * Mengambil nama pemasok untuk keterangan filter pada export (null jika semua pemasok).
     */
    private function supplierName(?string $supplierId): ?string
    {
        if ($supplierId === null) {
            return null;
        }

        foreach (app(GetAllSuppliers::class)->execute() as $supplier) {
            if ($supplier->id === $supplierId) {
                return $supplier->name;
            }
        }

        return null;
    }
}
