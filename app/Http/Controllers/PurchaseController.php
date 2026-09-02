<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Application\Purchase\DTOs\UpdatePurchaseCartDetailDTO;
use App\Application\Purchase\UseCases\CreatePurchaseCart;
use App\Application\Purchase\UseCases\CreatePurchaseCartDetail;
use App\Application\Purchase\UseCases\DeletePurchaseCartByCurrentUser;
use App\Application\Purchase\UseCases\DeletePurchaseCartDetail;
use App\Application\Purchase\UseCases\FindPurchaseById;
use App\Application\Purchase\UseCases\FindPurchaseCartByCurrentUser;
use App\Application\Purchase\UseCases\GetPurchaseByDateRangeTable;
use App\Application\Purchase\UseCases\GetPurchaseCartDetailByCartId;
use App\Application\Purchase\UseCases\GetPurchaseDetailByPurchaseId;
use App\Application\Purchase\UseCases\GetPurchasePaymentByPurchaseId;
use App\Application\Purchase\UseCases\SavePurchase;
use App\Application\Purchase\UseCases\UpdatePurchaseCartDetail;
use App\Application\Supplier\UseCases\GetActiveSuppliers;
use App\Http\Requests\Purchases\GetPurchaseByDateRangeRequest;
use App\Http\Requests\Purchases\SavePurchaseRequest;
use App\Http\Requests\Purchases\StorePuchaseCartDetailRequest;
use App\Http\Requests\Purchases\UpdatePurchaseCartDetailRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    /**
     * Menampilkan halaman utama pembelian.
     */
    public function index(GetPurchaseByDateRangeRequest $request): View|JsonResponse
    {
        if ($request->ajax()) {
            return app(GetPurchaseByDateRangeTable::class)->execute(
                fromDate: parse_date_time_immutable($request->query('from_date', date('Y-m-d'))),
                toDate: parse_date_time_immutable($request->query('to_date', date('Y-m-d'))),
            );
        }

        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: 'transactions.purchases',
        );

        return view('pages.purchases.index', compact('access'));
    }

    /**
     * Menampilkan halaman tambah penjualan
     */
    public function createCart(): View
    {
        $cart = app(FindPurchaseCartByCurrentUser::class)->execute();

        if ($cart === null) {
            $cart = app(CreatePurchaseCart::class)->execute();
        }

        $details = app(GetPurchaseCartDetailByCartId::class)->execute(cartId: $cart->id);
        $suppliers = app(GetActiveSuppliers::class)->execute();

        return view('pages.purchases.create', compact('cart', 'details', 'suppliers'));
    }

    /**
     * Menambah (insert) cart detail.
     */
    public function addCartDetail(StorePuchaseCartDetailRequest $request): RedirectResponse
    {
        app(CreatePurchaseCartDetail::class)->execute(
            productCode: $request->validated('product_code'),
            purchasePrice: $request->integer('purchase_price') ?: 0,
            quantity: $request->integer('quantity') ?: 1,
        );

        return to_route('transactions.purchases.cart')->with('alert', [
            'status' => 'success',
            'message' => 'Produk berhasil ditambahkan kedaftar.',
        ]);
    }

    /**
     * Update cart detail.
     */
    public function updateCartDetail(UpdatePurchaseCartDetailRequest $request, string $cartDetailId): RedirectResponse
    {
        app(UpdatePurchaseCartDetail::class)->execute(
            dto: new UpdatePurchaseCartDetailDTO(
                id: $cartDetailId,
                purchasePrice: $request->integer('purchase_price'),
                quantity: $request->integer('quantity'),

            )
        );

        return to_route('transactions.purchases.cart')->with('alert', [
            'status' => 'success',
            'message' => 'Produk berhasil diubah.',
        ]);
    }

    /**
     * Menghapus cart detail.
     */
    public function deleteCartDetail(string $cartDetailId): RedirectResponse
    {
        app(DeletePurchaseCartDetail::class)->execute(id: $cartDetailId);

        return to_route('transactions.purchases.cart')->with('alert', [
            'status' => 'success',
            'message' => 'Produk berhasil dihapus.',
        ]);
    }

    /**
     * Menghapus cart
     */
    public function cancelCart(): RedirectResponse
    {
        app(DeletePurchaseCartByCurrentUser::class)->execute();

        return to_route('transactions.purchases');
    }

    /**
     * Menyimpan data pembelian.
     */
    public function save(SavePurchaseRequest $request): RedirectResponse
    {
        app(SavePurchase::class)->execute(request: $request);

        return to_route('transactions.purchases')->with('alert', [
            'status' => 'success',
            'message' => 'Pembelian berhasil disimpan.',
        ]);
    }

    /**
     * Melihat detail pembelian.
     */
    public function detail(string $purchaseId): View
    {
        $purchase = app(FindPurchaseById::class)->execute(id: $purchaseId);

        if ($purchase === null) {
            abort(404);
        }

        $details = app(GetPurchaseDetailByPurchaseId::class)->execute(
            purchaseId: $purchase->id,
        );

        $payments = app(GetPurchasePaymentByPurchaseId::class)->execute(
            purchaseId: $purchase->id,
        );

        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: 'transactions.purchases'
        );

        return view('pages.purchases.detail', compact('purchase', 'details', 'payments', 'access'));
    }
}
