<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Dashboard\UseCases\GetDashboardFastMoving;
use App\Application\Dashboard\UseCases\GetDashboardLowStock;
use App\Application\Dashboard\UseCases\GetDashboardMonthlySales;
use App\Application\Dashboard\UseCases\GetDashboardSalesTotals;
use App\Application\Dashboard\UseCases\GetDashboardSlowMoving;
use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Domain\Dashboard\Entities\LowStockProductEntity;
use App\Domain\Dashboard\Entities\MonthlySummaryEntity;
use App\Domain\Dashboard\Entities\ProductSalesEntity;
use App\Http\Requests\Dashboards\GetDashboardProductsRequest;
use App\Http\Requests\Dashboards\GetDashboardSalesRequest;
use DateTimeImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Jumlah baris awal pada widget stok menipis.
     */
    private const LOW_STOCK_LIMIT = 15;

    /**
     * Menampilkan halaman utama dashboard. Widget hanya tampil jika pengguna berhak membaca modul sumber datanya.
     */
    public function index(): View
    {
        return view(
            view: 'pages.dashboard.index',
            data: [
                'canSale' => $this->canRead('transactions.sales'),
                'canCreateSale' => $this->canCreate('transactions.sales'),
                'canCreatePurchase' => $this->canCreate('transactions.purchases'),
                'canProduct' => $this->canRead('masters.products'),
                'years' => range(now()->year, max(now()->year - 5, 2000)),
            ],
        );
    }

    /**
     * Mengambil ringkasan dashboard: total penjualan (hari ini, bulan ini, tahun ini) dan produk stok menipis (ajax).
     */
    public function summary(Request $request): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $data = [];

        if ($this->canRead('transactions.sales')) {
            $data['totals'] = app(GetDashboardSalesTotals::class)->execute(today: $this->today())->toArray();
        }

        if ($this->canRead('masters.products')) {
            $lowStock = app(GetDashboardLowStock::class)->execute(limit: self::LOW_STOCK_LIMIT);

            $data['low_stock'] = [
                'total' => $lowStock['total'],
                'items' => array_map(fn (LowStockProductEntity $item): array => $item->toArray(), $lowStock['items']),
            ];
        }

        return $this->jsonResponse(data: $data);
    }

    /**
     * Mengambil penjualan bulanan (kuantitas dan nilai) satu tahun untuk grafik (ajax).
     */
    public function monthlySaleSummary(GetDashboardSalesRequest $request): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        abort_unless($this->canRead('transactions.sales'), 403);

        $year = $request->year();
        $months = app(GetDashboardMonthlySales::class)->execute(year: $year);

        // Struktur siap pakai untuk Chart.js: label dan dua deret data (12 bulan).
        return $this->jsonResponse(
            data: [
                'year' => $year,
                'labels' => array_map(fn (MonthlySummaryEntity $month): string => $month->monthName(), $months),
                'quantities' => array_map(fn (MonthlySummaryEntity $month): string => $month->quantity, $months),
                'amounts' => array_map(fn (MonthlySummaryEntity $month): string => $month->amount, $months),
                'has_data' => collect($months)->contains(fn (MonthlySummaryEntity $month): bool => bccomp($month->amount, '0', 2) > 0 || bccomp($month->quantity, '0', 2) > 0),
            ],
        );
    }

    /**
     * Mengambil produk terlaris dan produk tidak laku pada periode terpilih (ajax).
     */
    public function products(GetDashboardProductsRequest $request): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        abort_unless($this->canRead('transactions.sales'), 403);

        $period = $request->period();
        $today = $this->today();
        $map = fn (ProductSalesEntity $item): array => $item->toArray();

        [$from, $to] = $period->range($today);

        return $this->jsonResponse(
            data: [
                'period' => $period->value,
                'period_label' => $period->label(),
                'from' => $from->format('Y-m-d'),
                'to' => $to->format('Y-m-d'),
                'fast_moving' => array_map($map, app(GetDashboardFastMoving::class)->execute(period: $period, today: $today)),
                'slow_moving' => array_map($map, app(GetDashboardSlowMoving::class)->execute(period: $period, today: $today)),
            ],
        );
    }

    /**
     * Tanggal hari ini menurut zona waktu aplikasi.
     */
    private function today(): DateTimeImmutable
    {
        return now()->toDateTimeImmutable()->setTime(0, 0);
    }

    /**
     * Apakah pengguna boleh membaca modul.
     */
    private function canRead(string $route): bool
    {
        return (bool) (app(FindModuleAccessByRoute::class)->execute(roleId: user()->role_id, route: $route)?->read ?? false);
    }

    /**
     * Apakah pengguna boleh menambah data pada modul (cart transaksi memerlukan izin create).
     */
    private function canCreate(string $route): bool
    {
        return (bool) (app(FindModuleAccessByRoute::class)->execute(roleId: user()->role_id, route: $route)?->create ?? false);
    }
}
