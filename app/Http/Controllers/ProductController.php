<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Category\UseCases\GetAllCategories;
use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Application\Price\DTOs\PriceDTO;
use App\Application\Price\UseCases\CreatePrice;
use App\Application\Price\UseCases\DeletePrice;
use App\Application\Price\UseCases\FindPriceById;
use App\Application\Price\UseCases\GetPricesTableByProduct;
use App\Application\Product\UseCases\CreateProduct;
use App\Application\Product\UseCases\FindProductById;
use App\Application\Product\UseCases\GetProductsTable;
use App\Application\Product\UseCases\UpdateProduct;
use App\Application\Unit\UseCases\GetAllUnits;
use App\Http\Requests\Products\StorePriceProductRequest;
use App\Http\Requests\Products\StoreProductRequest;
use App\Http\Requests\Products\UpdateProductRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Module route.
     */
    private string $moduleRoute = 'masters.products';

    /**
     * Menampilkan halaman utama master produk
     */
    public function index(Request $request): JsonResponse|View
    {
        if ($request->ajax()) {
            return app(GetProductsTable::class)->execute();
        }

        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: $this->moduleRoute,
        );

        return view(
            view: 'pages.products.index',
            data: compact('access'),
        );
    }

    /**
     * Menampilkan halaman create (insert)
     */
    public function create(): View
    {
        $categories = app(GetAllCategories::class)->execute(
            isActive: true,
        );

        return view(
            view: 'pages.products.create',
            data: compact('categories'),
        );
    }

    /**
     * Menyimpan (insert) data.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = app(CreateProduct::class)->execute(
            request: $request,
        );

        return to_route('masters.products.prices', ['productId' => $product->id])->with('alert', [
            'status' => 'success',
            'message' => 'Produk berhasil ditambahkan. Silahkan tambahkan harga produk.',
        ]);
    }

    /**
     * Menampilkn halaman daftar harga.
     *
     * @return View
     */
    public function prices(Request $request, string $productId): View|JsonResponse
    {
        $product = app(FindProductById::class)->execute(
            id: $productId,
        );

        if ($product === null) {
            abort(404);
        }

        if ($request->ajax()) {
            return app(GetPricesTableByProduct::class)->execute(
                product: $product,
            );
        }

        $units = app(GetAllUnits::class)->execute(
            isActive: true,
        );

        return view(
            view: 'pages.products.prices',
            data: compact('product', 'units'),
        );
    }

    /**
     * Menambah (insert) harga ke database.
     */
    public function storePrice(StorePriceProductRequest $request, string $productId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $product = app(FindProductById::class)->execute(
            id: $productId,
        );

        if ($product === null) {
            abort(404);
        }

        app(CreatePrice::class)->execute(
            value: new PriceDTO(
                productId: $product->id,
                unitId: $request->validated('unit_id'),
                multiplesQuantity: $request->float('multiples_quantity'),
                purchasePrice: $request->float('purchase_price'),
                sellingPrice: $request->float('selling_price'),
                isDefault: $request->boolean('is_default'),
            ),
        );

        return $this->jsonResponse(
            status: 201,
            message: 'Harga produk berhasil ditambahkan.'
        );
    }

    /**
     * Hapus permanen harga
     */
    public function deletePrice(Request $request, string $productId, string $priceId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $product = app(FindProductById::class)->execute(
            id: $productId,
        );

        if ($product === null) {
            throw ValidationException::withMessages(messages: [
                'message' => 'Produk tidak ditemukan.',
            ]);
        }

        $price = app(FindPriceById::class)->execute(
            id: $priceId,
        );

        if ($price === null) {
            throw ValidationException::withMessages(messages: [
                'message' => 'Harga produk tidak ditemukan.',
            ]);
        }

        app(DeletePrice::class)->execute(
            price: $price,
        );

        return $this->jsonResponse(
            status: 200,
            message: 'Harga produk berhasil dihapus.'
        );
    }

    /**
     * Menampilkan halaman edit.
     */
    public function edit(Request $request, string $productId): View
    {
        $product = app(FindProductById::class)->execute(
            id: $productId,
        );

        if ($productId === null) {
            abort(404);
        }

        $categories = app(GetAllCategories::class)->execute(
            isActive: true,
        );

        return view(
            view: 'pages.products.edit',
            data: compact('product', 'categories'),
        );
    }

    /**
     * Update
     */
    public function update(UpdateProductRequest $request, string $productId): RedirectResponse
    {
        $product = app(FindProductById::class)->execute(
            id: $productId,
        );

        if ($product === null) {
            abort(404);
        }

        app(UpdateProduct::class)->execute(
            product: $product,
            request: $request,
        );

        return to_route('masters.products.edit', ['productId' => $product->id])->with('alert', [
            'status' => 'success',
            'message' => 'Produk berhasil diperbarui.',
        ]);
    }
}
