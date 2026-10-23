<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Category\UseCases\GetAllCategories;
use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Application\Report\UseCases\GetSaleReportFilterOptions;
use App\Application\Report\UseCases\GetSaleReportQuery;
use App\Application\Report\UseCases\GetSaleReportSummary;
use App\Application\Report\UseCases\GetSaleReportTable;
use App\Application\Report\UseCases\GetSaleTransactionReport;
use App\Application\Sale\UseCases\GetSaleDetailsTable;
use App\Exports\Reports\SaleReportExport;
use App\Http\Requests\Reports\GetSaleReportRequest;
use Barryvdh\DomPDF\Facade\Pdf as DomPdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class SaleReportController extends Controller
{
    /**
     * Label metode pembayaran.
     */
    private const METHOD_LABELS = [
        'cash' => 'Tunai',
        'qris' => 'QRIS',
        'debit' => 'Debit',
        'credit' => 'Kredit',
    ];

    /**
     * Menampilkan halaman laporan penjualan, atau (ajax) data tabel laporan (DataTables server side).
     */
    public function index(GetSaleReportRequest $request): View|JsonResponse
    {
        // Request ajax: kembalikan data tabel sesuai filter.
        if ($request->ajax()) {
            return app(GetSaleReportTable::class)->execute(
                filter: $request->toFilter(),
            );
        }

        // Request biasa: tampilkan halaman beserta hak akses user dan pilihan filter.
        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: 'reports.sales',
        );

        $options = app(GetSaleReportFilterOptions::class)->execute();
        $categories = app(GetAllCategories::class)->execute();

        return view(
            view: 'pages.reports.sales.index',
            data: [
                'access' => $access,
                'cashiers' => $options['cashiers'],
                'categories' => $categories,
            ],
        );
    }

    /**
     * Mengambil ringkasan (KPI) laporan penjualan sesuai filter (ajax).
     */
    public function summary(GetSaleReportRequest $request): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        return $this->jsonResponse(
            data: app(GetSaleReportSummary::class)->execute(filter: $request->toFilter())->toArray(),
        );
    }

    /**
     * Mengambil daftar detail produk satu transaksi penjualan (DataTables server side).
     */
    public function transactionDetails(Request $request, string $saleId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        return app(GetSaleDetailsTable::class)->execute(
            saleId: $saleId,
        );
    }

    /**
     * Mencetak detail produk satu transaksi penjualan beserta ringkasan penjualannya dalam file pdf.
     */
    public function exportTransactionPdf(string $saleId): Response
    {
        $transaction = app(GetSaleTransactionReport::class)->execute(saleId: $saleId);

        abort_if($transaction === null, 404, 'Data penjualan tidak ditemukan.');

        $pdf = DomPdf::loadView('pages.reports.sales.transaction-pdf', [
            'transaction' => $transaction,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="detail-penjualan-'.$transaction->sale->invoiceNumber.'.pdf"',
            'Cache-Control' => 'no-store, private',
        ]);
    }

    /**
     * Mengunduh laporan penjualan dalam file excel (filter sama dengan halaman laporan).
     */
    public function exportExcel(GetSaleReportRequest $request): BinaryFileResponse
    {
        $filter = $request->toFilter();

        $export = new SaleReportExport(
            query: app(GetSaleReportQuery::class)->execute(filter: $filter),
            filter: $filter,
            summary: app(GetSaleReportSummary::class)->execute(filter: $filter),
            filterLabels: $this->filterLabels($request),
        );

        return Excel::download(
            $export,
            sprintf(
                'laporan-penjualan-%s-%s.xlsx',
                $filter->startDate->format('Ymd'),
                $filter->endDate->format('Ymd'),
            ),
        );
    }

    /**
     * Mencetak laporan penjualan dalam file pdf (filter sama dengan halaman laporan).
     */
    public function exportPdf(GetSaleReportRequest $request): Response
    {
        $filter = $request->toFilter();

        $pdf = DomPdf::loadView('pages.reports.sales.pdf', [
            'filter' => $filter,
            'filterLabels' => $this->filterLabels($request),
            'summary' => app(GetSaleReportSummary::class)->execute(filter: $filter),
            'rows' => app(GetSaleReportQuery::class)->execute(filter: $filter)->get(),
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => sprintf(
                'inline; filename="laporan-penjualan-%s-%s.pdf"',
                $filter->startDate->format('Ymd'),
                $filter->endDate->format('Ymd'),
            ),
            'Cache-Control' => 'no-store, private',
        ]);
    }

    /**
     * Keterangan filter aktif (label => nilai) untuk ditampilkan pada export; hanya filter yang dipilih.
     *
     * @return array<string, string>
     */
    private function filterLabels(GetSaleReportRequest $request): array
    {
        $filter = $request->toFilter();
        $labels = [];

        if ($filter->userId !== null) {
            $options = app(GetSaleReportFilterOptions::class)->execute();

            $labels['Kasir'] = collect($options['cashiers'])->firstWhere('id', $filter->userId)['name'] ?? '-';
        }

        if ($filter->paymentMethod !== null) {
            $labels['Metode Pembayaran'] = self::METHOD_LABELS[$filter->paymentMethod] ?? $filter->paymentMethod;
        }

        if ($filter->invoiceNumber !== null) {
            $labels['Nomor Invoice'] = $filter->invoiceNumber;
        }

        if ($filter->categoryId !== null) {
            $category = collect(app(GetAllCategories::class)->execute())->firstWhere('id', $filter->categoryId);
            $labels['Kategori'] = $category?->name ?? '-';
        }

        if ($filter->productCode !== null) {
            $labels['Kode Produk'] = $filter->productCode;
        }

        return $labels;
    }
}
