<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Application\Purchase\DTOs\PurchaseCartDTO;
use App\Application\Purchase\UseCases\CreatePurchase;
use App\Application\Purchase\UseCases\CreatePurchaseCart;
use App\Application\Purchase\UseCases\CreatePurchaseCartDetail;
use App\Application\Purchase\UseCases\CreatePurchasePayment;
use App\Application\Purchase\UseCases\DeletePurchaseCart;
use App\Application\Purchase\UseCases\DeletePurchaseCartDetail;
use App\Application\Purchase\UseCases\DeletePurchasePayment;
use App\Application\Purchase\UseCases\FindPurchaseById;
use App\Application\Purchase\UseCases\FindPurchaseCartByCurrentUser;
use App\Application\Purchase\UseCases\FindPurchaseCartById;
use App\Application\Purchase\UseCases\FindPurchaseCartDetailById;
use App\Application\Purchase\UseCases\GetPurchaseCartDetailsByPurchaseCartId;
use App\Application\Purchase\UseCases\GetPurchaseCartDetailsTable;
use App\Application\Purchase\UseCases\GetPurchaseDetailTableByPurchaseId;
use App\Application\Purchase\UseCases\GetPurchasePaymentsTableByPurchaseId;
use App\Application\Purchase\UseCases\GetPurchasesByDate;
use App\Application\Purchase\UseCases\RemovePurchase;
use App\Application\Purchase\UseCases\UpdatePurchaseCartDetail;
use App\Application\Supplier\UseCases\GetAllSuppliers;
use App\Http\Requests\Purchases\CreatePurchaseRequest;
use App\Http\Requests\Purchases\GetPurchasesRequest;
use App\Http\Requests\Purchases\StorePurchaseCartDetailRequest;
use App\Http\Requests\Purchases\StorePurchasePaymentRequest;
use App\Http\Requests\Purchases\UpdatePurchaseCartDetailRequest;
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
    public function index(GetPurchasesRequest $request): View|JsonResponse
    {
        if ($request->ajax()) {
            return $this->jsonResponse(
                data: app(GetPurchasesByDate::class)->execute(
                    fromDate: $request->date('from_date'),
                    toDate: $request->date('to_date'),
                ),
            );
        }

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
     * Remove (soft delete) pembelian.
     */
    public function remove(Request $request, string $purchaseId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        app(RemovePurchase::class)->execute(
            id: $purchaseId,
        );

        return $this->jsonResponse(
            status: 200,
            message: 'Transaksi pembelian berhasil dihapus.'
        );
    }

    /**
     * Create (insert)
     */
    public function store(CreatePurchaseRequest $request): RedirectResponse
    {
        app(CreatePurchase::class)->execute(
            request: $request,
        );

        return to_route('transactions.purchases')->with('alert', [
            'status' => 'success',
            'message' => 'Transaksi pembelian berhasil disimpan.',
        ]);
    }

    /**
     * Menampilkan halaman keranjang pembelian.
     */
    public function carts(Request $request): View|JsonResponse
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

        if ($request->ajax()) {
            return $this->jsonResponse(
                data: $cart,
            );
        }

        $suppliers = app(GetAllSuppliers::class)->execute(
            isActive: true,
        );

        return view(
            view: 'pages.purchases.cart',
            data: compact('cart', 'suppliers')
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
     * Mengambil daftar detail keranjang untuk datatable.
     */
    public function cartDetailsTable(Request $request, string $cartId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        return app(GetPurchaseCartDetailsTable::class)->execute(
            purchaseCartId: $cartId,
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

    /**
     * Update cart detail.
     */
    public function updateCartDetail(UpdatePurchaseCartDetailRequest $request, string $cartId, string $cartDetailId): JsonResponse
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

        app(UpdatePurchaseCartDetail::class)->execute(
            purchaseCart: $cart,
            purchaseCartDetail: $cartDetail,
            request: $request,
        );

        return $this->jsonResponse(
            message: 'Produk berhasil diperbarui.'
        );
    }

    /**
     * Menampilkan detail pembelian.
     */
    public function show(Request $request, string $purchaseId): View|JsonResponse
    {
        $purchase = app(FindPurchaseById::class)->execute(
            id: $purchaseId,
        );

        if ($purchase === null) {
            abort(404, 'Transaksi pembelian tidak ditemukan.');
        }

        if ($request->ajax()) {
            return $this->jsonResponse(
                data: $purchase,
            );
        }

        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: 'transactions.purchases',
        );

        return view(
            view: 'pages.purchases.show',
            data: compact('purchase', 'access'),
        );
    }

    /**
     * Mengambil daftar detail pembelian.
     */
    public function details(Request $request, string $purchaseId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        return app(GetPurchaseDetailTableByPurchaseId::class)->execute(
            purchaseId: $purchaseId,
        );
    }

    /**
     * Mengambil daftar pembayaran.
     */
    public function payments(Request $request, string $purchaseId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        return app(GetPurchasePaymentsTableByPurchaseId::class)->execute(
            purchaseId: $purchaseId,
        );
    }

    /**
     * Create pembayaran.
     */
    public function storePayment(StorePurchasePaymentRequest $request, string $purchaseId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        app(CreatePurchasePayment::class)->execute(
            request: $request,
            purchaseId: $purchaseId,
        );

        return $this->jsonResponse(
            status: 201,
            message: 'Pembayaran berhasil ditambahkan.'
        );
    }

    /**
     * Hapus permanen pembayaran
     */
    public function deletePayment(Request $request, string $purchaseId, string $paymentId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        app(DeletePurchasePayment::class)->execute(
            purchaseId: $purchaseId,
            paymentId: $paymentId,
        );

        return $this->jsonResponse(
            status: 200,
            message: 'Pembayaran berhasil dihapus.'
        );
    }
}
