<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Cash\UseCases\CreateCashTransaction;
use App\Application\Cash\UseCases\DeleteCashTransaction;
use App\Application\Cash\UseCases\FindCashTransactionById;
use App\Application\Cash\UseCases\GetCashRecorders;
use App\Application\Cash\UseCases\GetCashTransactionsTable;
use App\Application\Cash\UseCases\GetCashTransactionTotals;
use App\Application\Cash\UseCases\UpdateCashTransaction;
use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Http\Requests\Cash\GetCashTransactionsRequest;
use App\Http\Requests\Cash\StoreCashTransactionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashTransactionController extends Controller
{
    /**
     * Menampilkan halaman transaksi kas, atau (ajax) data tabel transaksi (DataTables server side).
     */
    public function index(GetCashTransactionsRequest $request): View|JsonResponse
    {
        // Request ajax: kembalikan data tabel sesuai filter.
        if ($request->ajax()) {
            return app(GetCashTransactionsTable::class)->execute(
                filter: $request->toFilter(),
            );
        }

        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: 'cash.transactions',
        );

        // Filter pencatat hanya untuk user yang berhak menghapus (admin).
        $users = ($access?->delete ?? false)
            ? app(GetCashRecorders::class)->execute()
            : [];

        return view(
            view: 'pages.cash.transactions',
            data: compact('access', 'users'),
        );
    }

    /**
     * Mengambil total kas masuk dan kas keluar sesuai filter (ajax).
     */
    public function totals(GetCashTransactionsRequest $request): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        return $this->jsonResponse(
            data: app(GetCashTransactionTotals::class)->execute(filter: $request->toFilter()),
        );
    }

    /**
     * Mengambil detail satu transaksi kas (ajax).
     */
    public function show(Request $request, string $transactionId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $transaction = app(FindCashTransactionById::class)->execute(id: $transactionId);

        abort_if($transaction === null, 404, 'Transaksi kas tidak ditemukan.');

        return $this->jsonResponse(
            data: $transaction->toArray(),
        );
    }

    /**
     * Menyimpan transaksi kas baru (ajax).
     */
    public function store(StoreCashTransactionRequest $request): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $transaction = app(CreateCashTransaction::class)->execute(
            value: $request->toDto(),
        );

        return $this->jsonResponse(
            data: $transaction->toArray(),
            status: 201,
            message: 'Transaksi kas berhasil disimpan.',
        );
    }

    /**
     * Memperbarui transaksi kas (ajax).
     */
    public function update(StoreCashTransactionRequest $request, string $transactionId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $transaction = app(UpdateCashTransaction::class)->execute(
            id: $transactionId,
            value: $request->toDto(),
        );

        return $this->jsonResponse(
            data: $transaction->toArray(),
            message: 'Transaksi kas berhasil diperbarui.',
        );
    }

    /**
     * Menghapus transaksi kas (soft delete, ajax).
     */
    public function destroy(Request $request, string $transactionId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        app(DeleteCashTransaction::class)->execute(id: $transactionId);

        return $this->jsonResponse(
            message: 'Transaksi kas berhasil dihapus.',
        );
    }
}
