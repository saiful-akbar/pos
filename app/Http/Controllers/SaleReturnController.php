<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Application\Sale\UseCases\CreateSaleReturn;
use App\Application\Sale\UseCases\CreateSaleReturnCart;
use App\Application\Sale\UseCases\CreateSaleReturnCartDetail;
use App\Application\Sale\UseCases\DeleteSaleReturnCartById;
use App\Application\Sale\UseCases\DeleteSaleReturnCartDetail;
use App\Application\Sale\UseCases\FindSaleReturnCartByUserId;
use App\Application\Sale\UseCases\GetSaleReturnCartDetailTable;
use App\Application\Sale\UseCases\GetSaleReturnCartInfo;
use App\Application\Sale\UseCases\GetSaleReturnsByDateRange;
use App\Application\Sale\UseCases\UpdateSaleReturnCartDetailQuantity;
use App\Http\Requests\SaleReturns\CreateSaleReturnRequest;
use App\Http\Requests\SaleReturns\GetSaleReturnsByDateRange as GetSaleReturnsByDateRangeRequest;
use App\Http\Requests\SaleReturns\StoreSaleReturnCartDetailRequest;
use App\Http\Requests\SaleReturns\StoreSaleReturnCartRequest;
use App\Http\Requests\SaleReturns\UpdateSaleReturnCartDetailQuantityRequest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SaleReturnController extends Controller
{
    /**
     * Menampilkan halaman utama return penjualan.
     */
    public function index(GetSaleReturnsByDateRangeRequest $request, GetSaleReturnsByDateRange $getSaleReturnsByDateRange): View|JsonResponse
    {
        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: 'transactions.returns',
        );

        $saleReturns = [];

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $saleReturns = $getSaleReturnsByDateRange->execute(
                startDate: Carbon::parse($request->input('start_date')),
                endDate: Carbon::parse($request->input('end_date')),
            );
        }

        if ($request->ajax()) {
            return $this->jsonResponse(
                data: $saleReturns,
                message: 'Data pengembalian penjualan berhasil diambil.',
            );
        }

        return view(
            view: 'pages.sale-returns.index',
            data: compact('access', 'saleReturns'),
        );
    }

    /**
     * Menampilkan halaman keranjang pengembalian penjualan milik user yang sedang login.
     */
    public function carts(): View
    {
        $cart = app(FindSaleReturnCartByUserId::class)->execute(
            userId: user()->id,
        );

        abort_if($cart === null, 404, 'Keranjang pengembalian penjualan tidak ditemukan.');

        return view(
            view: 'pages.sale-returns.cart',
            data: compact('cart'),
        );
    }

    /**
     * Create (insert) data keranjang pengembalian penjualan berdasarkan faktur penjualan.
     */
    public function storeCart(StoreSaleReturnCartRequest $request): RedirectResponse
    {
        app(CreateSaleReturnCart::class)->execute(
            invoiceNumber: $request->validated('invoice_number'),
        );

        return to_route('transactions.returns.carts');
    }

    /**
     * Menyelesaikan transaksi pengembalian penjualan dari keranjang.
     */
    public function store(CreateSaleReturnRequest $request, string $cartId): RedirectResponse
    {
        app(CreateSaleReturn::class)->execute(
            cartId: $cartId,
            notes: $request->validated('notes'),
            refundCash: $request->boolean('refund_cash'),
        );

        return to_route('transactions.returns')->with('alert', [
            'status' => 'success',
            'message' => 'Transaksi pengembalian penjualan berhasil diselesaikan.',
        ]);
    }

    /**
     * Mengambil daftar detail keranjang pengembalian penjualan.
     */
    public function cartDetails(Request $request, string $cartId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        return app(GetSaleReturnCartDetailTable::class)->execute(
            cartId: $cartId,
        );
    }

    /**
     * Mengambil informasi pelanggan, tanggal transaksi penjualan, dan total keranjang pengembalian.
     */
    public function cartInfo(Request $request, string $cartId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        return $this->jsonResponse(
            data: app(GetSaleReturnCartInfo::class)->execute(
                cartId: $cartId,
            ),
        );
    }

    /**
     * Create (insert) data detail keranjang pengembalian penjualan.
     */
    public function storeCartDetail(StoreSaleReturnCartDetailRequest $request, string $cartId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        app(CreateSaleReturnCartDetail::class)->execute(
            cartId: $cartId,
            productCode: $request->validated('product_code'),
        );

        return $this->jsonResponse(
            status: 201,
            message: 'Produk berhasil ditambahkan.',
        );
    }

    /**
     * Update quantity detail keranjang pengembalian penjualan.
     */
    public function updateCartDetailQuantity(UpdateSaleReturnCartDetailQuantityRequest $request, string $cartId, string $cartDetailId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        app(UpdateSaleReturnCartDetailQuantity::class)->execute(
            cartId: $cartId,
            cartDetailId: $cartDetailId,
            quantity: (float) $request->validated('quantity'),
        );

        return $this->jsonResponse(
            message: 'Quantity berhasil diperbarui.',
        );
    }

    /**
     * Menghapus permanen detail keranjang pengembalian penjualan.
     */
    public function deleteCartDetail(Request $request, string $cartId, string $cartDetailId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        app(DeleteSaleReturnCartDetail::class)->execute(
            cartId: $cartId,
            cartDetailId: $cartDetailId,
        );

        return $this->jsonResponse(
            message: 'Produk berhasil dihapus.',
        );
    }

    /**
     * Membatalkan (menghapus permanen) keranjang pengembalian penjualan.
     */
    public function deleteCart(string $cartId): RedirectResponse
    {
        app(DeleteSaleReturnCartById::class)->execute(
            id: $cartId,
        );

        return to_route('transactions.returns')->with('alert', [
            'status' => 'success',
            'message' => 'Transaksi pengembalian berhasil dibatalkan.',
        ]);
    }
}
