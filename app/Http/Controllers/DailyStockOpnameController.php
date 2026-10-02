<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Application\StockOpname\UseCases\CreateDailyStockOpnameDetail;
use App\Application\StockOpname\UseCases\CreateMonthlyStockOpname;
use App\Application\StockOpname\UseCases\DeleteDailyStockOpname;
use App\Application\StockOpname\UseCases\DeleteDailyStockOpnameDetail;
use App\Application\StockOpname\UseCases\FindDailyStockOpnameById;
use App\Application\StockOpname\UseCases\GetDailyStockOpnameByDate;
use App\Application\StockOpname\UseCases\GetDailyStockOpnameDetailByHeaderId;
use App\Application\StockOpname\UseCases\RemoveDailyStockOpname;
use App\Application\StockOpname\UseCases\SetActiveStock;
use App\Application\StockOpname\UseCases\UpdateQuantityDailyStockOpnameDetail;
use App\Exports\StockOpnames\TemplateInputDailyStockOpnameExport;
use App\Http\Requests\DailyStockOpnames\GetDailyStockOpnameRequest;
use App\Http\Requests\DailyStockOpnames\ImportDailyStockOpnameRequest;
use App\Http\Requests\DailyStockOpnames\StoreDailyStockOpnameDetailRequest;
use App\Http\Requests\DailyStockOpnames\StoreMonthlyStockRequest;
use App\Http\Requests\DailyStockOpnames\UpdateQuantityDailyStockOpnameDetailRequest;
use App\Imports\StockOpnames\DailyStockOpnameImport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DailyStockOpnameController extends Controller
{
    /**
     * Menampilkan halaman utama.
     */
    public function index(GetDailyStockOpnameRequest $request): View|JsonResponse
    {
        if ($request->ajax()) {
            return $this->jsonResponse(
                data: app(GetDailyStockOpnameByDate::class)->execute(
                    fromDate: $request->date('from_date'),
                    toDate: $request->date('to_date'),
                ),
            );
        }

        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: 'stockOpnames.daily',
        );

        return view('pages.daily-stock-opnames.index', compact('access'));
    }

    /**
     * Export excel template input
     */
    public function exportTemplateInput(): BinaryFileResponse
    {
        return Excel::download(new TemplateInputDailyStockOpnameExport(), 'template_input_stock_opname_harian.xlsx');
    }

    /**
     * Import excel
     */
    public function importFromExcel(ImportDailyStockOpnameRequest $request): RedirectResponse
    {
        $date = $request->date('date');
        $notes = $request->validated('notes');
        $file = $request->file('file');

        Excel::import(
            import: new DailyStockOpnameImport(date: $date, notes: $notes),
            filePath: $file
        );

        return to_route('stockOpnames.daily', $request->query())->with('alert', [
            'status' => 'success',
            'message' => 'Import stock opname harian berhasil.',
        ]);
    }

    /**
     * Undocumented function
     */
    public function show(Request $request, string $id): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        return $this->jsonResponse(
            data: app(GetDailyStockOpnameDetailByHeaderId::class)->execute(
                headerId: $id,
            ),
        );
    }

    /**
     * Menampilkan halaman edit.
     */
    public function edit(Request $request, string $id): View|JsonResponse
    {
        $dailyStock = app(FindDailyStockOpnameById::class)->execute(id: $id);

        if (! $dailyStock) {
            abort(404);
        }

        if ($request->ajax()) {
            return $this->jsonResponse(
                data: app(FindDailyStockOpnameById::class)->execute(id: $id),
            );
        }

        return view('pages.daily-stock-opnames.edit', compact('dailyStock'));
    }

    /**
     * Menambah data daily stock opname detail.
     */
    public function storeDetail(StoreDailyStockOpnameDetailRequest $request, string $id): JsonResponse
    {
        app(CreateDailyStockOpnameDetail::class)->execute(
            id: $id,
            productCode: $request->validated('product_code'),
            quantity: $request->float('quantity'),
        );

        return $this->jsonResponse(
            status: 201,
            message: 'Stok produk berhasil ditambahkan.'
        );
    }

    /**
     * Menghapus data daily stock opname detail.
     */
    public function deleteDetail(Request $request, string $dailyStockOpnameId, string $dailyStockOpnameDetailId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        app(DeleteDailyStockOpnameDetail::class)->execute(
            dailyStockOpnameId: $dailyStockOpnameId,
            dailyStockOpnameDetailId: $dailyStockOpnameDetailId,
        );

        return $this->jsonResponse(
            message: 'Stok berhasil dihapus.',
        );
    }

    /**
     * Update quantity daily stock opname detail.
     */
    public function updateDetail(UpdateQuantityDailyStockOpnameDetailRequest $request, string $dailyStockOpnameId, string $dailyStockOpnameDetailId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        app(UpdateQuantityDailyStockOpnameDetail::class)->execute(
            dailyStockOpnameId: $dailyStockOpnameId,
            dailyStockOpnameDetailId: $dailyStockOpnameDetailId,
            quantity: $request->float('quantity'),
        );

        return $this->jsonResponse(
            message: 'Quantity stok berhasil diperbarui.'
        );
    }

    /**
     * Menjadikan daily stock sebagai stock aktif.
     */
    public function setActiveStock(Request $request, string $id): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        app(SetActiveStock::class)->execute($id);

        return $this->jsonResponse(
            message: 'Stock opname berhasil dijadikan sebagai stok aktif produk.'
        );
    }

    /**
     * Menyimpan daily stock sebagai monthly stock.
     */
    public function storeMonthlyStock(StoreMonthlyStockRequest $request, string $dailyStockOpnameId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $dailyStockOpname = app(FindDailyStockOpnameById::class)->execute($dailyStockOpnameId);

        if ($dailyStockOpname === null) {
            throw ValidationException::withMessages([
                'year' => 'Stok opname harian tidak ditemukan.',
            ]);
        }

        app(CreateMonthlyStockOpname::class)->execute(
            dailyStockOpname: $dailyStockOpname,
            year: $request->integer('year'),
            month: $request->integer('month'),
            notes: $request->validated('notes'),
        );

        return $this->jsonResponse(
            status: 201,
            message: 'Stok opname berhasil dijadikan sebagai stok bulanan.'
        );
    }

    /**
     * Soft delete.
     */
    public function remove(Request $request, string $id): JsonResponse
    {
        if (! $request->ajax()) {
            return abort(404);
        }

        app(RemoveDailyStockOpname::class)->execute($id);

        return $this->jsonResponse(
            message: 'Stok opname harian berhasil dihapus.'
        );
    }

    /**
     * Permanently delete.
     */
    public function delete(Request $request, string $id): JsonResponse|RedirectResponse
    {
        app(DeleteDailyStockOpname::class)->execute($id);

        if ($request->ajax()) {
            return $this->jsonResponse(
                message: 'Stok opname harian berhasil dihapus permanen.'
            );
        }

        return to_route('stockOpnames.daily', $request->query())->with('alert', [
            'status' => 'success',
            'message' => 'Stok opname harian berhasil dihapus permanen.',
        ]);
    }
}
