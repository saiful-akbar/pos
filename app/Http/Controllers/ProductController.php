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
use App\Application\Product\UseCases\RemoveProduct;
use App\Application\Product\UseCases\ResetAllDiscountProducts;
use App\Application\Product\UseCases\UpdateProduct;
use App\Application\Unit\UseCases\GetAllUnits;
use App\Exports\Products\ProductExport;
use App\Exports\Products\TemplateProductExport;
use App\Exports\Products\TemplateUpdateDiscountProductExport;
use App\Exports\Products\TemplateUpdatePricesProductExport;
use App\Http\Requests\Products\GetProductsRequest;
use App\Http\Requests\Products\ImportProductRequest;
use App\Http\Requests\Products\ResetDiscountProductRequest;
use App\Http\Requests\Products\StorePriceProductRequest;
use App\Http\Requests\Products\StoreProductRequest;
use App\Http\Requests\Products\UpdateProductRequest;
use App\Imports\Products\ProductImport;
use App\Imports\Products\UpdateDiscountProductImport;
use App\Imports\Products\UpdatePricesProductImport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductController extends Controller
{
    /**
     * Module route.
     */
    private string $moduleRoute = 'masters.products';

    /**
     * Menampilkan halaman utama master produk
     */
    public function index(GetProductsRequest $request): JsonResponse|View
    {
        if ($request->ajax()) {
            $active = null;

            if ($request->has('is_active')) {
                $active = $request->boolean('is_active');
            }

            return app(GetProductsTable::class)->execute(
                isActive: $active,
            );
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

        return to_route('masters.products.prices.create', ['productId' => $product->id])->with('alert', [
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
        if (! $request->ajax()) {
            abort(404);
        }

        $product = app(FindProductById::class)->execute(
            id: $productId,
        );

        if ($product === null) {
            abort(404, 'Produk tidak ditemukan');
        }

        return app(GetPricesTableByProduct::class)->execute(
            product: $product,
        );
    }

    /**
     * Menampilkn halaman daftar harga.
     *
     * @return View
     */
    public function createPrices(Request $request, string $productId): View|JsonResponse
    {
        $product = app(FindProductById::class)->execute(
            id: $productId,
        );

        if ($product === null) {
            abort(404, 'Produk tidak ditemukan.');
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
            abort(404, 'Produk tidak ditemukan.');
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
            abort(404, 'Produk tidak ditemukan.');
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
    public function edit(string $productId): View
    {
        $product = app(FindProductById::class)->execute(
            id: $productId,
        );

        if ($productId === null) {
            abort(404, 'Produk tidak ditemukan.');
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
            abort(404, 'Produk tidak ditemukan.');
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

    /**
     * Export excel.
     */
    public function exportExcel(): BinaryFileResponse
    {
        return Excel::download(new ProductExport(), 'Master_Produk.xlsx');
    }

    /**
     * Download template untuk import excel.
     */
    public function downloadTemplate(): BinaryFileResponse
    {
        return Excel::download(new TemplateProductExport(), 'Template_Import_Produk.xlsx');
    }

    /**
     * Import excel untuk menambah produk baru.
     */
    public function importExcel(ImportProductRequest $request): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        Excel::import(new ProductImport(), $request->file('file'));

        return $this->jsonResponse(
            status: 201,
            message: 'Impor excel data produk berhasil.'
        );
    }

    /**
     * Hapus (soft delete)
     */
    public function remove(Request $request, string $productId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $product = app(FindProductById::class)->execute(
            id: $productId,
        );

        if ($product === null) {
            abort(404, 'Produk tidak ditemukan.');
        }

        app(RemoveProduct::class)->execute(
            product: $product,
        );

        return $this->jsonResponse(
            status: 200,
            message: 'Produk berhasil dihapus.'
        );
    }

    /**
     * Reset discount.
     */
    public function resetDiscounts(ResetDiscountProductRequest $request): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        app(ResetAllDiscountProducts::class)->execute(
            value: $request->float('discount'),
        );

        return $this->jsonResponse(
            status: 200,
            message: 'Diskon berhasil direset.'
        );
    }

    /**
     * Download template import excel untuk update diskon.
     */
    public function templateImportUpdateDiscounts(): BinaryFileResponse
    {
        return Excel::download(new TemplateUpdateDiscountProductExport(), 'Template_Import_Update_Diskon_Produk.xlsx');
    }

    /**
     * Import excel untuk update diskon.
     */
    public function importUpdateDiscounts(ImportProductRequest $request): JsonResponse
    {
        Excel::import(new UpdateDiscountProductImport(), $request->file('file'));

        return $this->jsonResponse(
            status: 200,
            message: 'Import update diskon produk berhasil.',
        );
    }

    /**
     * Download template import excel untuk update harga.
     */
    public function templateImportUpdatePrices(): BinaryFileResponse
    {
        return Excel::download(new TemplateUpdatePricesProductExport(), 'Template_Import_Update_Harga_Produk.xlsx');
    }

    /**
     * Import excel untuk update diskon.
     */
    public function importUpdatePrices(ImportProductRequest $request): JsonResponse
    {
        Excel::import(new UpdatePricesProductImport(), $request->file('file'));

        return $this->jsonResponse(
            status: 200,
            message: 'Import update harga produk berhasil.',
        );
    }
}
