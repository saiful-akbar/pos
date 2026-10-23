<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Application\Stock\UseCases\ActivateMonthlyStock;
use App\Application\Stock\UseCases\CreateMonthlyStockByDate;
use App\Application\Stock\UseCases\DeleteMonthlyStock;
use App\Application\Stock\UseCases\FindMonthlyStockById;
use App\Application\Stock\UseCases\GetMonthlyStockDetailsQuery;
use App\Application\Stock\UseCases\GetMonthlyStockDetailsTable;
use App\Application\Stock\UseCases\GetMonthlyStocksTable;
use App\Exports\MonthlyStocks\MonthlyStockDetailExport;
use App\Http\Requests\MonthlyStocks\CreateMonthlyStockRequest;
use App\Http\Requests\MonthlyStocks\GetMonthlyStocksRequest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MonthlyStockController extends Controller
{
    /**
     * Menampilkan halaman stok bulanan, atau (ajax) data stok bulanan untuk DataTables server side.
     */
    public function index(GetMonthlyStocksRequest $request): View|JsonResponse
    {
        // Request ajax: kembalikan data stok bulanan berdasarkan tahun dan bulan (opsional).
        if ($request->ajax()) {
            return app(GetMonthlyStocksTable::class)->execute(
                year: (int) $request->validated('year'),
                month: $request->validated('month') !== null ? (int) $request->validated('month') : null,
            );
        }

        // Ambil hak akses user pada modul stok bulanan.
        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: 'stocks.monthly',
        );

        return view(
            view: 'pages.monthly-stocks.index',
            data: compact('access'),
        );
    }

    /**
     * Memilih stok harian berdasarkan tanggal lalu menjadikannya stok bulanan (ajax).
     */
    public function store(CreateMonthlyStockRequest $request): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        app(CreateMonthlyStockByDate::class)->execute(
            date: Carbon::parse($request->validated('date')),
            year: (int) $request->validated('year'),
            month: (int) $request->validated('month'),
        );

        return $this->jsonResponse(
            status: 201,
            message: 'Stok bulanan berhasil dibuat.',
        );
    }

    /**
     * Mengambil daftar detail stok bulanan beserta produk dan unit (DataTables server side).
     */
    public function details(Request $request, string $monthlyStockId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        return app(GetMonthlyStockDetailsTable::class)->execute(
            monthlyStockId: $monthlyStockId,
        );
    }

    /**
     * Mengunduh detail stok bulanan dalam file excel.
     */
    public function exportDetails(string $monthlyStockId): BinaryFileResponse
    {
        $monthlyStock = app(FindMonthlyStockById::class)->execute(
            id: $monthlyStockId,
        );

        abort_if($monthlyStock === null, 404, 'Data stok bulanan tidak ditemukan.');

        $query = app(GetMonthlyStockDetailsQuery::class)->execute(
            monthlyStockId: $monthlyStock->id,
        );

        return Excel::download(
            new MonthlyStockDetailExport($query),
            sprintf('Detail_Stok_Bulanan_%d-%02d.xlsx', $monthlyStock->year, $monthlyStock->month),
        );
    }

    /**
     * Menjadikan stok bulanan sebagai stok aktif produk (ajax).
     */
    public function activate(Request $request, string $monthlyStockId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        app(ActivateMonthlyStock::class)->execute(
            id: $monthlyStockId,
        );

        return $this->jsonResponse(
            message: 'Stok bulanan berhasil dijadikan stok aktif produk.',
        );
    }

    /**
     * Menghapus permanen stok bulanan (ajax).
     */
    public function destroy(Request $request, string $monthlyStockId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        app(DeleteMonthlyStock::class)->execute(
            id: $monthlyStockId,
        );

        return $this->jsonResponse(
            message: 'Stok bulanan berhasil dihapus.',
        );
    }
}
