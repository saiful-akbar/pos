<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Application\StockOpname\UseCases\CreateMonthlyStockOpname;
use App\Application\StockOpname\UseCases\FindDailyStockOpnameByDate;
use App\Application\StockOpname\UseCases\FindMonthlyStockByYearAndMonth;
use App\Application\StockOpname\UseCases\GetMonthlyStockDetails;
use App\Application\StockOpname\UseCases\UpdateMonthlyStock;
use App\Http\Requests\MonthlyStockOpnames\GetMonthlyStockOpnameRequest;
use App\Http\Requests\MonthlyStockOpnames\StoreMonthlyStockOpnameRequest;
use App\Http\Requests\MonthlyStockOpnames\UpdateMonthlyStockOpnameRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MonthlyStockOpnameController extends Controller
{
    /**
     * Nama route untuk module stock opname bulanan.
     */
    private readonly string $moduleRoute;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->moduleRoute = 'stockOpnames.monthly';
    }

    /**
     * Menampilkan halaman utama stok bulanan.
     */
    public function index(GetMonthlyStockOpnameRequest $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $stock = app(FindMonthlyStockByYearAndMonth::class)->execute(
                year: $request->integer('year'),
                month: $request->integer('month'),
            );

            if ($stock === null) {
                throw ValidationException::withMessages([
                    'year' => 'Stok tidak ditemukan.',
                ]);
            }

            $details = app(GetMonthlyStockDetails::class)->execute(
                monthlyStockId: $stock->id,
            );

            return $this->jsonResponse(
                data: [
                    'stock' => $stock,
                    'details' => $details,
                ],
            );
        }

        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: $this->moduleRoute,
        );

        return view('pages.monthly-stock-opnames.index', compact('access'));
    }

    /**
     * Menyimpan (insert) data stok opname bulanan dari stok harian.
     *
     * @param StoreMonthlyStockOpnameRequest $request
     * @return JsonResponse
     */
    public function store(StoreMonthlyStockOpnameRequest $request): JsonResponse
    {
        if (!$request->ajax()) {
            abort(404);
        }

        $dailyStockOpname = app(FindDailyStockOpnameByDate::class)->execute(
            date: $request->date('daily_stock_opname_date'),
        );

        if ($dailyStockOpname === null) {
            throw ValidationException::withMessages([
                'daily_stock_opname_date' => 'Stok opname harian tidak ditemukan.'
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
            message: 'Stok awal bulan berhasil simpan.'
        );
    }

    /**
     * Update stok awal.
     *
     * @param UpdateMonthlyStockOpnameRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(UpdateMonthlyStockOpnameRequest $request, string $id): JsonResponse
    {
        if (!$request->ajax()) {
            abort(404);
        }

        app(UpdateMonthlyStock::class)->execute(
            id: $id,
            dailyStockDate: $request->date('daily_stock_opname_date'),
            notes: $request->validated('notes'),
        );

        return $this->jsonResponse(
            status: 200,
            message: 'Stok awal bulan berhasil diperbarui.'
        );
    }
}
