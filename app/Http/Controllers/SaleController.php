<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Application\Sale\UseCases\CreateSale;
use App\Application\Sale\UseCases\CreateSaleCart;
use App\Application\Sale\UseCases\CreateSaleCartDetail;
use App\Application\Sale\UseCases\CreateSaleReturnCart;
use App\Application\Sale\UseCases\DeleteSaleCartById;
use App\Application\Sale\UseCases\DeleteSaleCartDetailById;
use App\Application\Sale\UseCases\FindSaleById;
use App\Application\Sale\UseCases\FindSaleCartByCurrentUser;
use App\Application\Sale\UseCases\FindSaleCartById;
use App\Application\Sale\UseCases\GetSaleCartDetailsByCartIdTable;
use App\Application\Sale\UseCases\UpdateQuantitySalesCartDetailById;
use App\Http\Requests\Sales\StoreSaleCartDetailRequest;
use App\Http\Requests\Sales\StoreSaleRequest;
use App\Http\Requests\Sales\StoreSaleReturnCartRequest;
use App\Http\Requests\Sales\UpdateQuantitySaleCartDetailRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Picqer\Barcode\BarcodeGeneratorSVG;

class SaleController extends Controller
{
    /**
     * Menampilkan halaman POS
     */
    public function index(): View|RedirectResponse
    {
        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: 'transactions.sales',
        );

        return view('pages.sales.index', compact('access'));
    }

    /**
     * Menampilkan halaman sales cart.
     */
    public function carts(): View|RedirectResponse
    {
        $cart = app(FindSaleCartByCurrentUser::class)->execute();

        return view('pages.sales.cart', compact('cart'));
    }

    /**
     * Membuat sale cart untuk transaksi baru.
     */
    public function storeCart(): RedirectResponse
    {
        $cart = app(FindSaleCartByCurrentUser::class)->execute();

        if ($cart === null) {
            app(CreateSaleCart::class)->execute();
        }

        return to_route('transactions.sales.carts');
    }

    /**
     * Menghapus atau membatalkan transaksi penjualan.
     */
    public function deleteCart(string $cartId): RedirectResponse
    {
        app(DeleteSaleCartById::class)->execute(id: $cartId);

        return to_route('transactions.sales.carts');
    }

    /**
     * Mengambil data sale cart (keranjang belanja) berdasarkan id.
     */
    public function showCart(string $id): JsonResponse
    {
        $data = app(FindSaleCartById::class)->execute(id: $id);

        if ($data === null) {
            throw ValidationException::withMessages([
                'error' => 'Kerangjang transaksi tidak ditemukan.',
            ]);
        }

        return $this->jsonResponse(
            data: $data,
        );
    }

    /**
     * Menambahkan produk ke sale cart detail.
     */
    public function storeCartDetail(StoreSaleCartDetailRequest $request, string $cartId): JsonResponse
    {
        app(CreateSaleCartDetail::class)->execute(
            cartId: $cartId,
            productCode: $request->validated('product'),
        );

        return $this->jsonResponse(
            status: 201,
            message: 'Produk berhasil ditambahkan.',
        );
    }

    /**
     * Mengambil daftar produk keranjang belanja.
     */
    public function getCartDetails(string $cartId): JsonResponse
    {
        return app(GetSaleCartDetailsByCartIdTable::class)->execute(
            cartId: $cartId
        );
    }

    /**
     * Menghapus produk dari daftar kerangjang belanja.
     */
    public function deleteCartDetail(string $cartId, string $cartDetailId): JsonResponse
    {
        app(DeleteSaleCartDetailById::class)->execute(
            cartId: $cartId,
            cartDetailId: $cartDetailId,
        );

        return $this->jsonResponse(
            message: 'Produk berhasil dihapus.',
        );
    }

    /**
     * Update quantity produk pada daftar kerangjang belanja.
     */
    public function updateCartDetail(UpdateQuantitySaleCartDetailRequest $request, string $cartId, string $cartDetailId): JsonResponse
    {
        app(UpdateQuantitySalesCartDetailById::class)->execute(
            cartId: $cartId,
            cartDetailId: $cartDetailId,
            quantity: $request->float('quantity'),
        );

        return $this->jsonResponse(
            message: 'Quantity produk berhasil diperbarui.',
        );
    }

    /**
     * Menyimpan (insert) data penjualan
     */
    public function storeSale(StoreSaleRequest $request): JsonResponse
    {
        $sale = app(CreateSale::class)->execute(
            request: $request,
        );

        return $this->jsonResponse(
            message: 'Penjualan berhasil disimpan',
            data: $sale,
        );
    }

    /**
     * Undocumented function
     */
    public function printReceipt(string $id): View
    {
        $sale = app(FindSaleById::class)->execute(id: $id);

        if ($sale === null) {
            abort(404);
        }

        $generator = new BarcodeGeneratorSVG();

        $barcode = $generator->getBarcode(
            barcode: $sale->invoiceNumber,
            type: $generator::TYPE_CODE_128,
            height: 80,

        );

        return view('pages.sales.receipt', compact('sale', 'barcode'));
    }

    /**
     * Membuat (insert) sesi untuk pengembalian penjualan.
     */
    public function storeReturnCart(StoreSaleReturnCartRequest $request): RedirectResponse
    {
        app(CreateSaleReturnCart::class)->execute(
            invoiceNumber: $request->validated('invoice_number'),
        );

        return to_route('transactions.sales.return');
    }

    /**
     * Menampilkan halaman return cart.
     */
    public function returnCart(): View
    {
        return view('pages.sales.return');
    }
}
