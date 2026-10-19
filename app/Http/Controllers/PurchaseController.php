<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Application\Purchase\DTOs\PurchaseCartDTO;
use App\Application\Purchase\UseCases\CreatePurchaseCart;
use App\Application\Purchase\UseCases\CreatePurchaseCartDetail;
use App\Application\Purchase\UseCases\DeletePurchaseCart;
use App\Application\Purchase\UseCases\DeletePurchaseCartDetail;
use App\Application\Purchase\UseCases\FindPurchaseCartByCurrentUser;
use App\Application\Purchase\UseCases\FindPurchaseCartById;
use App\Application\Purchase\UseCases\FindPurchaseCartDetailById;
use App\Application\Purchase\UseCases\GetPurchaseCartDetailsByPurchaseCartId;
use App\Http\Requests\Purchases\StorePurchaseCartDetailRequest;
use DateTimeImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    /**
     * Menampilkan halaman utama pembelian.
     */
    public function index(): View
    {
        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: 'transactions.purchases',
        );

        return view(
            view: 'pages.purchases.index',
            data: compact('access'),
        );
    }

    /**
     * Menampilkan halaman keranjang pembelian.
     */
    public function carts(): View
    {
        $cart = app(FindPurchaseCartByCurrentUser::class)->execute();

        if ($cart === null) {
            $cart = app(CreatePurchaseCart::class)->execute(values: new PurchaseCartDTO(
                userId: user()->id,
                transactionDate: new DateTimeImmutable(),
                totalProduct: 0.00,
                totalQuantity: 0.00,
                totalPrice: 0.00
            ));
        }

        return view(
            view: 'pages.purchases.cart',
            data: compact('cart')
        );
    }

    /**
     * Menghapus (membatalkan) data keranjang.
     */
    public function deleteCart(string $cartId): RedirectResponse
    {
        $cart = app(FindPurchaseCartById::class)->execute(
            id: $cartId,
        );

        if ($cart === null) {
            abort(404, 'Transaksi pembelian tidak ditemukan');
        }

        app(DeletePurchaseCart::class)->execute(
            purchaseCart: $cart,
        );

        return to_route('transactions.purchases')->with('alert', [
            'status' => 'success',
            'message' => 'Transaksi pembelian berhasil dibatalkan.',
        ]);
    }

    /**
     * Mengambil daftar detail keranjang
     */
    public function cartDetails(Request $request, string $cartId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $cart = app(FindPurchaseCartById::class)->execute(
            id: $cartId,
        );

        if ($cart === null) {
            abort(404, 'Detail transaksi tidak ditemukan.');
        }

        $cartDetails = app(GetPurchaseCartDetailsByPurchaseCartId::class)->execute(
            purchaseCart: $cart,
        );

        return $this->jsonResponse(
            status: 200,
            data: $cartDetails,
        );
    }

    /**
     * Create (insert) detail keranjang pembelian.
     */
    public function storeCartDetail(StorePurchaseCartDetailRequest $request): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $purchaseCart = app(FindPurchaseCartByCurrentUser::class)->execute();

        if ($purchaseCart === null) {
            abort(404, 'Transaksi tidak ditemukan');
        }

        app(CreatePurchaseCartDetail::class)->execute(
            purchaseCart: $purchaseCart,
            productCode: $request->validated('product_code'),
        );

        return $this->jsonResponse(
            status: 201,
            message: 'Produk berhasil ditambahkan.',
        );
    }

    /**
     * Delete cart detail.
     */
    public function deleteCartDetail(Request $request, string $cartId, string $cartDetailId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $cart = app(FindPurchaseCartById::class)->execute(
            id: $cartId,
        );

        if ($cart === null) {
            abort(404, 'Transaksi tidak ditemukan.');
        }

        $cartDetail = app(FindPurchaseCartDetailById::class)->execute(
            id: $cartDetailId,
        );

        if ($cartDetail === null) {
            abort(404, 'Produk tidak ditemukan.');
        }

        app(DeletePurchaseCartDetail::class)->execute(
            purchaseCart: $cart,
            purchaseCartDetail: $cartDetail,
        );

        return $this->jsonResponse(
            status: 200,
            message: 'Produk berhasil dihapus.'
        );
    }
}
