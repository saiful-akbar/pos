<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Application\Sale\UseCases\CreateSale;
use App\Application\Sale\UseCases\CreateSaleCart;
use App\Application\Sale\UseCases\CreateSaleCartDetail;
use App\Application\Sale\UseCases\DeleteSaleCart;
use App\Application\Sale\UseCases\DeleteSaleCartDetail;
use App\Application\Sale\UseCases\FindSaleCartByUserId;
use App\Application\Sale\UseCases\GetSaleCartDetailTable;
use App\Application\Sale\UseCases\UpdateSaleCart;
use App\Application\Sale\UseCases\UpdateSaleCartDetailByPrice;
use App\Application\Sale\UseCases\UpdateSaleCartDetailByQuantity;
use App\Http\Requests\Sales\AddCustomerSaleRequest;
use App\Http\Requests\Sales\CreateSaleRequest;
use App\Http\Requests\Sales\StoreSaleCartDetailRequest;
use App\Http\Requests\Sales\UpdateSaleCartDetailByPriceRequest;
use App\Http\Requests\Sales\UpdateSaleCartDetailByQuantityRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SaleController extends Controller
{
    /**
     * Menampilkan halaman utama penjualan.
     */
    public function index(): View
    {
        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: 'transactions.sales',
        );

        return view(view: 'pages.sales.index', data: compact('access'));
    }

    /**
     * Create (insert) data penjualan.
     */
    public function store(CreateSaleRequest $request): RedirectResponse
    {
        app(CreateSale::class)->execute(
            request: $request,
        );

        return to_route('transactions.sales')->with('alert', [
            'status' => 'success',
            'message' => 'Transaksi penjualan berhasil diselesaikan.',
        ]);
    }

    /**
     * Menampilkan halaman keranjang penjualan.
     */
    public function carts(Request $request): View|JsonResponse
    {
        $cart = app(FindSaleCartByUserId::class)->execute(
            userId: user()->id,
        );

        if ($cart === null) {
            $cart = app(CreateSaleCart::class)->execute();
        }

        if ($request->ajax()) {
            return $this->jsonResponse(
                data: $cart,
            );
        }

        return view(view: 'pages.sales.cart', data: compact('cart'));
    }

    /**
     * Hapus (batalkan) transaksi keranjang penjualan.
     */
    public function deleteCart(string $cartId): RedirectResponse
    {
        app(DeleteSaleCart::class)->execute(
            cartId: $cartId,
        );

        return to_route('transactions.sales')->with('alert', [
            'status' => 'success',
            'message' => 'Transaksi berhasil dibatalkan.',
        ]);
    }

    /**
     * Update transaksi keranjang penjualan.
     */
    public function updateCart(AddCustomerSaleRequest $request, string $cartId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        app(UpdateSaleCart::class)->execute(
            cartId: $cartId,
            customerId: $request->validated('customer_id'),
        );

        return $this->jsonResponse(
            message: 'Pelanggan berhsail ditambahkan.'
        );
    }

    /**
     * Mengmabil daftar detail keranjang penjualan.
     */
    public function cartDetails(Request $request, string $cartId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        return app(GetSaleCartDetailTable::class)->execute(
            cartId: $cartId,
        );
    }

    /**
     * Create (insert) data detail keranjang penjualan.
     */
    public function storeCartDetail(StoreSaleCartDetailRequest $request, string $cartId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        app(CreateSaleCartDetail::class)->execute(
            cartId: $cartId,
            productCode: $request->validated('product_code'),
        );

        return $this->jsonResponse(
            status: 201,
            message: 'Produk berhasil ditambahkan.'
        );
    }

    /**
     * Update detail keranjang penjualan berdasarkan quantity
     */
    public function updateCartDetailByQuantity(UpdateSaleCartDetailByQuantityRequest $request, string $cartId, string $cartDetailId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        app(UpdateSaleCartDetailByQuantity::class)->execute(
            request: $request,
            cartId: $cartId,
            cartDetailId: $cartDetailId,
        );

        return $this->jsonResponse(
            message: 'Produk berhasil diperbarui.'
        );
    }

    /**
     * Update detail keranjang penjualan berdasarkan harga
     */
    public function updateCartDetailByPrice(UpdateSaleCartDetailByPriceRequest $request, string $cartId, string $cartDetailId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        app(UpdateSaleCartDetailByPrice::class)->execute(
            request: $request,
            cartId: $cartId,
            cartDetailId: $cartDetailId,
        );

        return $this->jsonResponse(
            message: 'Produk berhasil diperbarui.'
        );
    }

    /**
     * Hapus detaill keranjang penjualan
     */
    public function deleteCartDetail(Request $request, string $cartId, string $cartDetailId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        app(DeleteSaleCartDetail::class)->execute(
            cartId: $cartId,
            cartDetailId: $cartDetailId,
        );

        return $this->jsonResponse(
            message: 'Produk berhasil dihapus.'
        );
    }
}
