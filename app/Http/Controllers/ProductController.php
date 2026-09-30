<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Category\UseCases\GetAllCategories;
use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Application\Product\UseCases\CreateProduct;
use App\Application\Product\UseCases\FindProductById;
use App\Application\Product\UseCases\GetActiveProductsTable;
use App\Application\Product\UseCases\GetProductsTable;
use App\Application\Product\UseCases\RemoveProduct;
use App\Application\Product\UseCases\ResetDiscountProduct;
use App\Application\Product\UseCases\UpdateProduct;
use App\Application\Unit\UseCases\GetAllUnits;
use App\Exports\Products\ProductExport;
use App\Exports\Products\TemplateProductExport;
use App\Exports\Products\TemplateUpdateDiscountProductExport;
use App\Exports\Products\TemplateUpdateSellingPriceProductExport;
use App\Http\Requests\Products\ImportExcelProductRequest;
use App\Http\Requests\Products\ResetDiscountProductRequest;
use App\Http\Requests\Products\StoreProductRequest;
use App\Http\Requests\Products\UpdateProductRequest;
use App\Imports\Products\ProductImport;
use App\Imports\Products\UpdateDiscountProductImport;
use App\Imports\Products\UpdateSellingPriceProductImport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductController extends Controller
{
    /**
     * Menampilkan halaman daftar produk.
     */
    public function index(Request $request): JsonResponse|View
    {
        if ($request->ajax()) {
            return app(GetProductsTable::class)->execute();
        }

        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: 'master.products',
        );

        return view('pages.products.index', compact('access'));
    }

    /**
     * Menampilkan halaman tambah prooduk.
     */
    public function create(): View
    {
        $categories = app(GetAllCategories::class)->execute();
        $units = app(GetAllUnits::class)->execute();

        return view('pages.products.create', compact('categories', 'units'));
    }

    /**
     * Menmbah (menyimpan) data produk baru.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        app(CreateProduct::class)->execute(request: $request);

        return to_route('master.products.create')->with('alert', [
            'status' => 'success',
            'message' => 'Produk baru berhasil ditambahkan.',
        ]);
    }

    /**
     * Menampilkan halaman edit data produk.
     */
    public function edit(string $id): View
    {
        $product = app(FindProductById::class)->execute($id);

        if ($product === null) {
            abort(404);
        }

        $categories = app(GetAllCategories::class)->execute();
        $units = app(GetAllUnits::class)->execute();

        return view('pages.products.edit', compact('product', 'categories', 'units'));
    }

    /**
     * Memperbarui data produk.
     */
    public function update(UpdateProductRequest $request, string $id): RedirectResponse
    {
        app(UpdateProduct::class)->execute(id: $id, request: $request);

        return to_route('master.products.edit', ['id' => $id])->with('alert', [
            'status' => 'success',
            'message' => 'Produk berhasil diperbarui.',
        ]);
    }

    /**
     * Export excel
     */
    public function exportExcel(): BinaryFileResponse
    {
        return Excel::download(new ProductExport(), 'produk.xlsx');
    }

    /**
     * Export template untuk import excel.
     */
    public function templateImport(): BinaryFileResponse
    {
        return Excel::download(new TemplateProductExport(), 'template_import_produk.xlsx');
    }

    /**
     * Import excel.
     */
    public function importExcel(ImportExcelProductRequest $request): RedirectResponse
    {
        Excel::import(new ProductImport(), $request->file('file'));

        return to_route('master.products')->with('alert', [
            'status' => 'success',
            'message' => 'Import excel berhasil.',
        ]);
    }

    /**
     * Download template import excel update discount.
     */
    public function templateImportUpdateDiscount(): BinaryFileResponse
    {
        return Excel::download(new TemplateUpdateDiscountProductExport(), 'template_import_update_diskon_produk.xlsx');
    }

    /**
     * Import update discount.
     */
    public function importUpdateDiscount(ImportExcelProductRequest $request): RedirectResponse
    {
        Excel::import(new UpdateDiscountProductImport(), $request->file('file'));

        return to_route('master.products')->with('alert', [
            'status' => 'success',
            'message' => 'Diskon berhasil diperbarui.',
        ]);
    }

    /**
     * Reset discount.
     */
    public function resetDiscount(ResetDiscountProductRequest $request): RedirectResponse
    {
        $discount = $request->integer('discount');

        app(ResetDiscountProduct::class)->execute(discount: $discount);

        return to_route('master.products')->with('alert', [
            'status' => 'success',
            'message' => "Diskon produk berhasil direset menjadi $discount%.",
        ]);
    }

    /**
     * Download template excel untuk update harga jual.
     */
    public function templateImportUpdateSellingPrice(): BinaryFileResponse
    {
        return Excel::download(new TemplateUpdateSellingPriceProductExport(), 'template_import_update_harga_jual_produk.xlsx');
    }

    /**
     * Import excel untuk update harga jual.
     */
    public function importUpdateSellingPrice(ImportExcelProductRequest $request): RedirectResponse
    {
        Excel::import(new UpdateSellingPriceProductImport(), $request->file('file'));

        return to_route('master.products')->with('alert', [
            'status' => 'success',
            'message' => 'Harga jual produk berhasil diperbarui.',
        ]);
    }

    /**
     * Mengambil daftar product aktif untuk dataTable.
     */
    public function getActiveTable(Request $request): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        return app(GetActiveProductsTable::class)->execute();
    }

    /**
     * Soft delete.
     */
    public function remove(Request $request, string $id): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        app(RemoveProduct::class)->execute($id);

        return $this->jsonResponse(
            message: 'Produk berhasil dihapus.'
        );
    }
}
