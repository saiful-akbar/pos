<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Application\Stock\UseCases\ActivateDailyStock;
use App\Application\Stock\UseCases\CreateMonthlyStock;
use App\Application\Stock\UseCases\DeleteDailyStock;
use App\Application\Stock\UseCases\FindDailyStockById;
use App\Application\Stock\UseCases\GetDailyStockByDateRange;
use App\Application\Stock\UseCases\GetDailyStockDetailsTable;
use App\Exports\DailyStocks\DailyStockDetailExport;
use App\Exports\DailyStocks\TemplateExport;
use App\Http\Requests\DailyStocks\CreateMonthlyStockRequest;
use App\Http\Requests\DailyStocks\GetDailyStockByDateRange as GetDailyStockByDateRangeRequest;
use App\Http\Requests\DailyStocks\ImportDailyStockRequest;
use App\Http\Requests\DailyStocks\ImportUpdateDailyStockRequest;
use App\Imports\DailyStocks\DailyStockImport;
use App\Imports\DailyStocks\DailyStockUpdateImport;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Validators\ValidationException as ExcelValidationException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DailyStockController extends Controller
{
    /**
     * Menampilkan halaman stok harian, atau (ajax) daftar stok harian berdasarkan rentang tanggal.
     */
    public function index(GetDailyStockByDateRangeRequest $request, GetDailyStockByDateRange $getDailyStockByDateRange): View|JsonResponse
    {
        // Request ajax: kembalikan data stok harian dalam rentang tanggal sebagai JSON.
        if ($request->ajax()) {
            $dailyStocks = $getDailyStockByDateRange->execute(
                startDate: Carbon::parse($request->validated('start_date')),
                endDate: Carbon::parse($request->validated('end_date')),
            );

            return $this->jsonResponse(
                data: $dailyStocks,
                message: 'Data stok harian berhasil diambil.',
            );
        }

        // Request biasa: tampilkan halaman beserta hak akses user.
        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: 'stocks.daily',
        );

        return view(
            view: 'pages.daily-stocks.index',
            data: compact('access'),
        );
    }

    /**
     * Mengunduh template excel untuk import stok harian.
     */
    public function downloadTemplateImport(): BinaryFileResponse
    {
        return Excel::download(new TemplateExport(), 'Template_Import_Stok_Harian.xlsx');
    }

    /**
     * Import excel stok harian (ajax).
     */
    public function importExcel(ImportDailyStockRequest $request): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        try {
            Excel::import(
                new DailyStockImport(
                    date: Carbon::parse($request->validated('date')),
                    notes: $request->validated('notes'),
                ),
                $request->file('file'),
            );
        } catch (ExcelValidationException $e) {
            // Kegagalan validasi baris excel: ubah menjadi pesan error pada input file.
            $messages = collect($e->failures())
                ->map(fn (Failure $failure): string => "Baris {$failure->row()}: ".implode(' ', $failure->errors()))
                ->all();

            throw ValidationException::withMessages(messages: [
                'file' => $messages,
            ]);
        }

        return $this->jsonResponse(
            message: 'Import excel stok harian berhasil.',
        );
    }

    /**
     * Import excel untuk memperbarui stok harian (ajax).
     */
    public function importUpdate(ImportUpdateDailyStockRequest $request, string $dailyStockId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        try {
            Excel::import(
                new DailyStockUpdateImport(
                    dailyStockId: $dailyStockId,
                    notes: $request->validated('notes'),
                ),
                $request->file('file'),
            );
        } catch (ExcelValidationException $e) {
            // Kegagalan validasi baris excel: ubah menjadi pesan error pada input file.
            $messages = collect($e->failures())
                ->map(fn (Failure $failure): string => "Baris {$failure->row()}: ".implode(' ', $failure->errors()))
                ->all();

            throw ValidationException::withMessages(messages: [
                'file' => $messages,
            ]);
        }

        return $this->jsonResponse(
            message: 'Import update stok harian berhasil.',
        );
    }

    /**
     * Menghapus permanen stok harian (ajax).
     */
    public function destroy(Request $request, string $dailyStockId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        app(DeleteDailyStock::class)->execute(
            id: $dailyStockId,
        );

        return $this->jsonResponse(
            message: 'Stok harian berhasil dihapus.',
        );
    }

    /**
     * Menjadikan stok harian sebagai stok aktif produk (ajax).
     */
    public function activate(Request $request, string $dailyStockId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        app(ActivateDailyStock::class)->execute(
            id: $dailyStockId,
        );

        return $this->jsonResponse(
            message: 'Stok harian berhasil dijadikan stok aktif produk.',
        );
    }

    /**
     * Mengambil daftar detail stok harian beserta produk dan unit (DataTables server side).
     */
    public function details(Request $request, string $dailyStockId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        return app(GetDailyStockDetailsTable::class)->execute(
            dailyStockId: $dailyStockId,
        );
    }

    /**
     * Mengunduh detail stok harian dalam file excel.
     */
    public function exportDetails(string $dailyStockId): BinaryFileResponse
    {
        $dailyStock = app(FindDailyStockById::class)->execute(
            id: $dailyStockId,
        );

        abort_if($dailyStock === null, 404, 'Data stok harian tidak ditemukan.');

        return Excel::download(
            new DailyStockDetailExport($dailyStock),
            'Detail_Stok_Harian_'.$dailyStock->date->format('Y-m-d').'.xlsx',
        );
    }

    /**
     * Menjadikan stok harian sebagai stok awal bulan (ajax).
     */
    public function storeMonthly(CreateMonthlyStockRequest $request, string $dailyStockId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        app(CreateMonthlyStock::class)->execute(
            dailyStockId: $dailyStockId,
            year: (int) $request->validated('year'),
            month: (int) $request->validated('month'),
        );

        return $this->jsonResponse(
            status: 201,
            message: 'Stok harian berhasil dijadikan stok awal bulan.',
        );
    }
}
