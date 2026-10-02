<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Dashboard\UseCases\GetBestSellingProduct;
use App\Application\Dashboard\UseCases\GetMonthlySalesSummary;
use App\Http\Requests\Dashboards\GetByDateRangeRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman utama dashboard.
     */
    public function index(): View
    {
        return view('pages.dashboard.index');
    }

    /**
     * Mengambil data ringkasan penjualan perbulan.
     */
    public function monthlySaleSummary(Request $request): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        return $this->jsonResponse(
            data: app(GetMonthlySalesSummary::class)->execute(),
        );
    }

    /**
     * Mengambil daftar produk terlaris.
     */
    public function getBestSellingProduct(GetByDateRangeRequest $request): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        return $this->jsonResponse(
            data: app(GetBestSellingProduct::class)->execute(
                fromDate: $request->date('from_date'),
                toDate: $request->date('to_date'),
            ),
        );
    }
}
