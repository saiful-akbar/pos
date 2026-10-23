<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Cash\UseCases\GetCashMonthSummary;
use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Http\Requests\Cash\GetCashMonthRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class CashDashboardController extends Controller
{
    /**
     * Menampilkan halaman ringkasan kas.
     */
    public function index(): View
    {
        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: 'cash.dashboard',
        );

        return view(
            view: 'pages.cash.dashboard',
            data: compact('access'),
        );
    }

    /**
     * Mengambil ringkasan kas satu bulan (ajax).
     */
    public function summary(GetCashMonthRequest $request): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        return $this->jsonResponse(
            data: app(GetCashMonthSummary::class)->execute(
                year: (int) $request->validated('year'),
                month: (int) $request->validated('month'),
            )->toArray(),
        );
    }
}
