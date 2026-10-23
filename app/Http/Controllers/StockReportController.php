<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Category\UseCases\GetAllCategories;
use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Application\Report\UseCases\GetStockReportMutations;
use App\Application\Report\UseCases\GetStockReportProduct;
use App\Application\Report\UseCases\GetStockReportQuery;
use App\Application\Report\UseCases\GetStockReportSummary;
use App\Application\Report\UseCases\GetStockReportTable;
use App\Exports\Reports\StockReportExport;
use App\Http\Requests\Reports\GetStockReportRequest;
use Barryvdh\DomPDF\Facade\Pdf as DomPdf;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class StockReportController extends Controller
{
    /**
     * Label status rekonsiliasi.
     */
    private const STATUS_LABELS = [
        'match' => 'Sesuai',
        'negative' => 'Selisih Kurang',
        'positive' => 'Selisih Lebih',
        'unavailable' => 'Belum Dapat Direkonsiliasi',
    ];

    /**
     * Menampilkan halaman laporan stok, atau (ajax) data tabel rekonsiliasi (DataTables server side).
     */
    public function index(GetStockReportRequest $request): View|JsonResponse
    {
        // Request ajax: kembalikan data tabel sesuai filter.
        if ($request->ajax()) {
            return app(GetStockReportTable::class)->execute(
                filter: $request->toFilter(),
            );
        }

        // Request biasa: tampilkan halaman beserta hak akses user dan pilihan kategori.
        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: 'reports.stocks',
        );

        return view(
            view: 'pages.reports.stocks.index',
            data: [
                'access' => $access,
                'categories' => app(GetAllCategories::class)->execute(),
            ],
        );
    }

    /**
     * Mengambil sebaran status rekonsiliasi dan ketersediaan snapshot (ajax).
     */
    public function summary(GetStockReportRequest $request): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        return $this->jsonResponse(
            data: app(GetStockReportSummary::class)->execute(filter: $request->toFilter())->toArray(),
        );
    }

    /**
     * Mengambil rincian rekonsiliasi dan daftar mutasi satu produk pada periode (ajax).
     */
    public function mutations(GetStockReportRequest $request, string $productId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $filter = $request->toFilter();

        $product = app(GetStockReportProduct::class)->execute(filter: $filter, productId: $productId);

        abort_if($product === null, 404, 'Produk tidak ditemukan pada laporan periode ini.');

        $mutations = app(GetStockReportMutations::class)
            ->execute(filter: $filter, productId: $productId)
            ->get();

        return $this->jsonResponse(
            data: [
                'product' => $product,
                'mutations' => $mutations,
            ],
        );
    }

    /**
     * Mengunduh laporan stok dalam file excel (filter sama dengan halaman laporan).
     */
    public function exportExcel(GetStockReportRequest $request): BinaryFileResponse
    {
        $filter = $request->toFilter();

        $export = new StockReportExport(
            summaryQuery: app(GetStockReportQuery::class)->execute(filter: $filter),
            mutationQuery: app(GetStockReportMutations::class)->execute(filter: $filter),
            filter: $filter,
            summary: app(GetStockReportSummary::class)->execute(filter: $filter),
            generatedAt: now(),
            filterLabels: $this->filterLabels($request),
        );

        return Excel::download(
            $export,
            sprintf('laporan-stok-%04d-%02d.xlsx', $filter->year, $filter->month),
        );
    }

    /**
     * Mencetak laporan stok dalam file pdf (filter sama dengan halaman laporan).
     */
    public function exportPdf(GetStockReportRequest $request): Response
    {
        $filter = $request->toFilter();

        $pdf = DomPdf::loadView('pages.reports.stocks.pdf', [
            'filter' => $filter,
            'filterLabels' => $this->filterLabels($request),
            'summary' => app(GetStockReportSummary::class)->execute(filter: $filter),
            'rows' => app(GetStockReportQuery::class)->execute(filter: $filter)->get(),
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => sprintf('inline; filename="laporan-stok-%04d-%02d.pdf"', $filter->year, $filter->month),
            'Cache-Control' => 'no-store, private',
        ]);
    }

    /**
     * Keterangan filter aktif (label => nilai) untuk ditampilkan pada export; hanya filter yang dipilih.
     *
     * @return array<string, string>
     */
    private function filterLabels(GetStockReportRequest $request): array
    {
        $filter = $request->toFilter();
        $labels = [];

        if ($filter->categoryId !== null) {
            $category = collect(app(GetAllCategories::class)->execute())->firstWhere('id', $filter->categoryId);
            $labels['Kategori'] = $category?->name ?? '-';
        }

        if ($filter->keyword !== null) {
            $labels['Produk'] = $filter->keyword;
        }

        if ($filter->status !== null) {
            $labels['Status'] = self::STATUS_LABELS[$filter->status] ?? $filter->status;
        }

        return $labels;
    }
}
