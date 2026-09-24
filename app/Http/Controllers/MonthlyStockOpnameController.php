<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Application\StockOpname\UseCases\FindMonthlyStockByYearAndMonth;
use App\Application\StockOpname\UseCases\GetMonthlyStockDetails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
    public function index(Request $request): View|JsonResponse
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
}
