<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Application\Sale\UseCases\CreateSaleCart;
use App\Application\Sale\UseCases\DeleteSaleCartById;
use App\Application\Sale\UseCases\FindSaleCartByCurrentUser;
use App\Application\Sale\UseCases\FindSaleCartById;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SaleController extends Controller
{
    /**
     * Menampilkan halaman POS
     */
    public function index(): View
    {
        $cart = app(FindSaleCartByCurrentUser::class)->execute();

        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: 'transactions.sales',
        );

        return view('pages.sales.index', compact('access', 'cart'));
    }

    /**
     * Membuat sale cart untuk transaksi baru.
     */
    public function storeCart(): RedirectResponse
    {
        app(CreateSaleCart::class)->execute();

        return to_route('transactions.sales');
    }

    /**
     * Menghapus atau membatalkan transaksi penjualan.
     */
    public function deleteCart(string $cartId): RedirectResponse
    {
        app(DeleteSaleCartById::class)->execute(id: $cartId);

        return to_route('transactions.sales');
    }

    /**
     * Mengambil data sale cart (keranjang belanja) berdasarkan id.
     */
    public function showCart(string $id): JsonResponse
    {
        $data = app(FindSaleCartById::class)->execute(id: $id);

        return $this->jsonResponse(
            data: $data,
        );
    }
}
