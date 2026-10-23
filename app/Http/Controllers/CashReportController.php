<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Cash\UseCases\GetCashMonthlySummaries;
use App\Application\Cash\UseCases\GetCashRecorders;
use App\Application\Cash\UseCases\GetCashTransactionsQuery;
use App\Application\Cash\UseCases\GetCashTransactionsTable;
use App\Application\Cash\UseCases\GetCashTransactionTotals;
use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Domain\Cash\Entities\CashMonthSummaryEntity;
use App\Exports\Cash\CashReportExport;
use App\Http\Requests\Cash\GetCashTransactionsRequest;
use Barryvdh\DomPDF\Facade\Pdf as DomPdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CashReportController extends Controller
{
    /**
     * Jenis laporan yang dapat dicetak pada pdf.
     */
    private const REPORT_TYPES = ['transactions', 'monthly', 'reconciliation'];

    /**
     * Menampilkan halaman laporan kas, atau (ajax) data tabel laporan transaksi (DataTables server side).
     */
    public function index(GetCashTransactionsRequest $request): View|JsonResponse
    {
        // Request ajax: kembalikan data tabel transaksi sesuai filter.
        if ($request->ajax()) {
            return app(GetCashTransactionsTable::class)->execute(
                filter: $request->toFilter(),
            );
        }

        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: 'reports.cash',
        );

        $users = app(GetCashRecorders::class)->execute();

        return view(
            view: 'pages.reports.cash.index',
            data: compact('access', 'users'),
        );
    }

    /**
     * Mengambil total kas masuk dan keluar pada periode filter (ajax).
     */
    public function totals(GetCashTransactionsRequest $request): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        return $this->jsonResponse(
            data: app(GetCashTransactionTotals::class)->execute(filter: $request->toFilter()),
        );
    }

    /**
     * Mengambil rekap mutasi dan rekonsiliasi bulanan pada rentang bulan periode filter (ajax).
     */
    public function monthly(GetCashTransactionsRequest $request): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        return $this->jsonResponse(
            data: array_map(fn ($summary): array => $summary->toArray(), $this->summaries($request)),
        );
    }

    /**
     * Mengunduh laporan kas (3 sheet) dalam file excel.
     */
    public function exportExcel(GetCashTransactionsRequest $request): BinaryFileResponse
    {
        $filter = $request->toFilter();

        $export = new CashReportExport(
            transactionsQuery: app(GetCashTransactionsQuery::class)->execute(filter: $filter),
            summaries: $this->summaries($request),
            totals: app(GetCashTransactionTotals::class)->execute(filter: $filter),
            filter: $filter,
            generatedAt: now(),
        );

        return Excel::download(
            $export,
            sprintf('laporan-kas-%s-%s.xlsx', $filter->startDate->format('Ymd'), $filter->endDate->format('Ymd')),
        );
    }

    /**
     * Mencetak salah satu laporan kas dalam file pdf (transaksi, rekap bulanan, atau rekonsiliasi).
     */
    public function exportPdf(GetCashTransactionsRequest $request): Response
    {
        $filter = $request->toFilter();
        $type = in_array($request->query('report'), self::REPORT_TYPES, true) ? $request->query('report') : 'transactions';

        $pdf = DomPdf::loadView('pages.reports.cash.pdf', [
            'type' => $type,
            'filter' => $filter,
            'totals' => app(GetCashTransactionTotals::class)->execute(filter: $filter),
            'rows' => $type === 'transactions'
                ? app(GetCashTransactionsQuery::class)->execute(filter: $filter)->get()
                : collect(),
            'summaries' => $type === 'transactions' ? [] : $this->summaries($request),
            'generatedAt' => now(),
        ])->setPaper('a4', $type === 'reconciliation' ? 'landscape' : 'portrait');

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => sprintf(
                'inline; filename="laporan-kas-%s-%s-%s.pdf"',
                $type,
                $filter->startDate->format('Ymd'),
                $filter->endDate->format('Ymd'),
            ),
            'Cache-Control' => 'no-store, private',
        ]);
    }

    /**
     * Ringkasan bulanan untuk bulan-bulan yang tercakup periode filter (bulan penuh, maksimal 36 bulan).
     *
     * @return CashMonthSummaryEntity[]
     */
    private function summaries(GetCashTransactionsRequest $request): array
    {
        $filter = $request->toFilter();

        $fromIndex = (int) $filter->startDate->format('Y') * 12 + (int) $filter->startDate->format('n');
        $toIndex = (int) $filter->endDate->format('Y') * 12 + (int) $filter->endDate->format('n');

        // Batasi jumlah bulan agar query dan tampilan tetap ringan.
        $toIndex = min($toIndex, $fromIndex + 35);

        return app(GetCashMonthlySummaries::class)->execute(
            fromYear: intdiv($fromIndex - 1, 12),
            fromMonth: ($fromIndex - 1) % 12 + 1,
            toYear: intdiv($toIndex - 1, 12),
            toMonth: ($toIndex - 1) % 12 + 1,
        );
    }
}
