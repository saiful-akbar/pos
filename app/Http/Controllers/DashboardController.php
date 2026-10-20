<?php

declare(strict_types=1);

namespace App\Http\Controllers;

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
            data: []
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
            data: [],
        );
    }
}
