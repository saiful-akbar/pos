<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Cash\UseCases\GetCashMonthlySummaries;
use App\Application\Cash\UseCases\GetCashMonthSummary;
use App\Application\Cash\UseCases\SaveCashMonthlyBalance;
use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Http\Requests\Cash\GetCashMonthRequest;
use App\Http\Requests\Cash\GetCashYearRequest;
use App\Http\Requests\Cash\SaveCashMonthlyBalanceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CashReconciliationController extends Controller
{
    /**
     * Menampilkan halaman rekonsiliasi kas.
     */
    public function index(): View
    {
        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: 'cash.reconciliation',
        );

        return view(
            view: 'pages.cash.reconciliation',
            data: compact('access'),
        );
    }

    /**
     * Mengambil ringkasan rekonsiliasi satu bulan (ajax).
     */
    public function data(GetCashMonthRequest $request): JsonResponse
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

    /**
     * Mengambil histori rekonsiliasi bulanan satu tahun (ajax).
     */
    public function history(GetCashYearRequest $request): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $year = (int) $request->validated('year');

        $summaries = app(GetCashMonthlySummaries::class)->execute(
            fromYear: $year,
            fromMonth: 1,
            toYear: $year,
            toMonth: 12,
        );

        return $this->jsonResponse(
            data: array_map(fn ($summary): array => $summary->toArray(), $summaries),
        );
    }

    /**
     * Menyimpan saldo awal aktual bulan baru (ajax); tidak mengubah saldo yang sudah ada.
     */
    public function store(SaveCashMonthlyBalanceRequest $request): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $balance = app(SaveCashMonthlyBalance::class)->execute(
            value: $request->toDto(),
            allowUpdate: false,
        );

        return $this->jsonResponse(
            data: $balance->toArray(),
            status: 201,
            message: 'Saldo awal kas berhasil disimpan.',
        );
    }

    /**
     * Memperbarui saldo awal aktual bulan yang sudah dicatat (ajax).
     */
    public function update(SaveCashMonthlyBalanceRequest $request): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $value = $request->toDto();

        // Pembaruan hanya untuk periode yang sudah memiliki saldo awal.
        $summary = app(GetCashMonthSummary::class)->execute(year: $value->year, month: $value->month);

        if ($summary->balanceId === null) {
            throw ValidationException::withMessages(messages: [
                'opening_balance' => 'Saldo awal periode ini belum dicatat.',
            ]);
        }

        $balance = app(SaveCashMonthlyBalance::class)->execute(
            value: $value,
            allowUpdate: true,
        );

        return $this->jsonResponse(
            data: $balance->toArray(),
            message: 'Saldo awal kas berhasil diperbarui.',
        );
    }
}
