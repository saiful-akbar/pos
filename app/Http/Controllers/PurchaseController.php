<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Application\Purchase\UseCases\CreatePurchaseCartDetail;
use App\Application\Purchase\UseCases\CreatePurchaseCart;
use App\Application\Purchase\UseCases\FindPurchaseCartWithDetails;
use App\Application\Purchase\UseCases\GetPurchaseTable;
use App\Http\Requests\Purchases\CreatePurchaseCartDetailRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    /**
     * Menampilkan halaman utama pembelian
     */
    public function index(Request $request): JsonResponse|View
    {
        if ($request->ajax()) {
            return app(GetPurchaseTable::class)->execute();
        }

        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: 'transactions.purchases',
        );

        return view('pages.purchases.index', compact('access'));
    }

    /**
     * Menampilkan halaman tambah pembelian.
     */
    public function create(Request $request): JsonResponse|View
    {
        $purchase = app(FindPurchaseCartWithDetails::class)->execute();

        if ($purchase === null) {
            $purchase = app(CreatePurchaseCart::class)->execute();
        }

        if ($request->ajax()) {
            return $this->jsonResponse(data: $purchase);
        }

        return view('pages.purchases.create', compact('purchase'));
    }

    /**
     * menambah produk pada keranjang.
     */
    public function createCartDetail(CreatePurchaseCartDetailRequest $request): RedirectResponse|JsonResponse
    {
        app(CreatePurchaseCartDetail::class)->execute(
            productCode: $request->validated('product_code'),
        );

        return to_route('transactions.purchases.create')->with('alert', [
            'status' => 'success',
            'message' => 'Produk berhasil ditambahkan.'
        ]);
    }
}
