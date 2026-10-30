<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Category\UseCases\GetAllCategories;
use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Application\Report\UseCases\GetSaleReturnReportAnalysis;
use App\Application\Report\UseCases\GetSaleReturnReportDetailsTable;
use App\Application\Report\UseCases\GetSaleReturnReportFilterOptions;
use App\Application\Report\UseCases\GetSaleReturnReportProductQuery;
use App\Application\Report\UseCases\GetSaleReturnReportProductTable;
use App\Application\Report\UseCases\GetSaleReturnReportQuery;
use App\Application\Report\UseCases\GetSaleReturnReportSummary;
use App\Application\Report\UseCases\GetSaleReturnReportTable;
use App\Exports\Reports\SaleReturnProductReportExport;
use App\Exports\Reports\SaleReturnReportExport;
use App\Http\Requests\Reports\GetSaleReturnReportRequest;
use Barryvdh\DomPDF\Facade\Pdf as DomPdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class SaleReturnReportController extends Controller
{
    /**
     * Menampilkan halaman laporan pengembalian, atau (ajax) data tabel laporan (DataTables server side).
     */
    public function index(GetSaleReturnReportRequest $request): View|JsonResponse
    {
        // Request ajax: kembalikan data tabel sesuai filter.
        if ($request->ajax()) {
            return app(GetSaleReturnReportTable::class)->execute(
                filter: $request->toFilter(),
            );
        }

        // Request biasa: tampilkan halaman beserta hak akses user dan pilihan filter.
        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: 'reports.returns',
        );

        return view(
            view: 'pages.reports.returns.index',
            data: [
                'access' => $access,
                'cashiers' => app(GetSaleReturnReportFilterOptions::class)->execute()['cashiers'],
                'categories' => app(GetAllCategories::class)->execute(),
            ],
        );
    }

    /**
     * Mengambil ringkasan (KPI) laporan pengembalian sesuai filter (ajax).
     */
    public function summary(GetSaleReturnReportRequest $request): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        return $this->jsonResponse(
            data: app(GetSaleReturnReportSummary::class)->execute(filter: $request->toFilter())->toArray(),
        );
    }

    /**
     * Mengambil peringkat produk dan kategori yang paling banyak dikembalikan sesuai filter (ajax).
     */
    public function analysis(GetSaleReturnReportRequest $request): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        return $this->jsonResponse(
            data: app(GetSaleReturnReportAnalysis::class)->execute(filter: $request->toFilter()),
        );
    }

    /**
     * Mengambil daftar detail produk satu dokumen pengembalian (DataTables server side).
     */
    public function details(Request $request, string $saleReturnId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        return app(GetSaleReturnReportDetailsTable::class)->execute(
            saleReturnId: $saleReturnId,
        );
    }

    /**
     * Mengambil data tabel laporan pengembalian per produk (DataTables server side).
     */
    public function products(GetSaleReturnReportRequest $request): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        return app(GetSaleReturnReportProductTable::class)->execute(
            filter: $request->toFilter(),
        );
    }

    /**
     * Mengunduh laporan pengembalian per produk dalam file excel (filter sama dengan halaman laporan).
     */
    public function exportProductsExcel(GetSaleReturnReportRequest $request): BinaryFileResponse
    {
        $filter = $request->toFilter();

        $export = new SaleReturnProductReportExport(
            query: app(GetSaleReturnReportProductQuery::class)->execute(filter: $filter),
            startDate: $filter->startDate,
            endDate: $filter->endDate,
            filterLabels: $this->filterLabels($request),
        );

        return Excel::download(
            $export,
            sprintf(
                'laporan-pengembalian-per-produk-%s-%s.xlsx',
                $filter->startDate->format('Ymd'),
                $filter->endDate->format('Ymd'),
            ),
        );
    }

    /**
     * Mengunduh laporan pengembalian dalam file excel (filter sama dengan halaman laporan).
     */
    public function exportExcel(GetSaleReturnReportRequest $request): BinaryFileResponse
    {
        $filter = $request->toFilter();

        $export = new SaleReturnReportExport(
            query: app(GetSaleReturnReportQuery::class)->execute(filter: $filter),
            filter: $filter,
            summary: app(GetSaleReturnReportSummary::class)->execute(filter: $filter),
            generatedAt: now(),
            filterLabels: $this->filterLabels($request),
        );

        return Excel::download(
            $export,
            sprintf(
                'laporan-pengembalian-penjualan-%s-%s.xlsx',
                $filter->startDate->format('Ymd'),
                $filter->endDate->format('Ymd'),
            ),
        );
    }

    /**
     * Mencetak laporan pengembalian dalam file pdf (filter sama dengan halaman laporan).
     */
    public function exportPdf(GetSaleReturnReportRequest $request): Response
    {
        $filter = $request->toFilter();

        $pdf = DomPdf::loadView('pages.reports.returns.pdf', [
            'filter' => $filter,
            'filterLabels' => $this->filterLabels($request),
            'summary' => app(GetSaleReturnReportSummary::class)->execute(filter: $filter),
            'rows' => app(GetSaleReturnReportQuery::class)->execute(filter: $filter)->get(),
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => sprintf(
                'inline; filename="laporan-pengembalian-penjualan-%s-%s.pdf"',
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
    private function filterLabels(GetSaleReturnReportRequest $request): array
    {
        $filter = $request->toFilter();
        $labels = [];

        if ($filter->returnNumber !== null) {
            $labels['Nomor Pengembalian'] = $filter->returnNumber;
        }

        if ($filter->invoiceNumber !== null) {
            $labels['Invoice'] = $filter->invoiceNumber;
        }

        if ($filter->customerName !== null) {
            $labels['Pelanggan'] = $filter->customerName;
        }

        if ($filter->userId !== null) {
            $cashiers = app(GetSaleReturnReportFilterOptions::class)->execute()['cashiers'];
            $labels['Petugas'] = collect($cashiers)->firstWhere('id', $filter->userId)['name'] ?? '-';
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
