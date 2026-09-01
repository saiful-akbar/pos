<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Application\Purchase\DTOs\UpdatePurchaseCartDetailDTO;
use App\Application\Purchase\UseCases\CreatePurchaseCart;
use App\Application\Purchase\UseCases\CreatePurchaseCartDetail;
use App\Application\Purchase\UseCases\DeletePurchaseCartDetail;
use App\Application\Purchase\UseCases\FindPurchaseCartByCurrentUser;
use App\Application\Purchase\UseCases\UpdatePurchaseCartDetail;
use App\Application\Supplier\UseCases\GetActiveSuppliers;
use App\Http\Requests\Purchases\StorePuchaseCartDetailRequest;
use App\Http\Requests\Purchases\UpdatePurchaseCartDetailRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    /**
     * Menampilkan halaman utama pembelian.
     */
    public function index(Request $request): View
    {
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
        $purchase = app(FindPurchaseCartByCurrentUser::class)->execute();

        if ($purchase === null) {
            $purchase = app(CreatePurchaseCart::class)->execute();
        }

        $suppliers = app(GetActiveSuppliers::class)->execute();

        return view('pages.purchases.create', compact('purchase', 'suppliers'));
    }

    /**
     * Menambah (insert) cart detail.
     */
    public function addCart(StorePuchaseCartDetailRequest $request): RedirectResponse
    {
        app(CreatePurchaseCartDetail::class)->execute(
            productCode: $request->validated('product_code'),
            purchasePrice: $request->integer('purchase_price') ?: 0,
            quantity: $request->integer('quantity') ?: 1,
        );

        return to_route('transactions.purchases.cart.create')->with('alert', [
            'status' => 'success',
            'message' => 'Produk berhasil ditambahkan kedaftar.',
        ]);
    }

    /**
     * Update cart detail.
     */
    public function updateCart(UpdatePurchaseCartDetailRequest $request, string $id): RedirectResponse
    {
        app(UpdatePurchaseCartDetail::class)->execute(dto: new UpdatePurchaseCartDetailDTO(
            id: $id,
            purchasePrice: $request->integer('purchase_price'),
            quantity: $request->integer('quantity'),
        ));

        return to_route('transactions.purchases.cart.create')->with('alert', [
            'status' => 'success',
            'message' => 'Produk berhasil diubah.',
        ]);
    }

    /**
     * Menghapus cart detail.
     */
    public function deleteCart(string $id): RedirectResponse
    {
        app(DeletePurchaseCartDetail::class)->execute(id: $id);

        return to_route('transactions.purchases.cart.create')->with('alert', [
            'status' => 'success',
            'message' => 'Produk berhasil dihapus.',
        ]);
    }
}
