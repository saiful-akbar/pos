<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Category\UseCases\GetAllCategories;
use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Application\Product\DTOs\CreateProductDTO;
use App\Application\Product\DTOs\UpdateProductDTO;
use App\Application\Product\UseCases\CreateProduct;
use App\Application\Product\UseCases\FindProductById;
use App\Application\Product\UseCases\GetProductsTable;
use App\Application\Product\UseCases\UpdateProduct;
use App\Application\Unit\UseCases\GetAllUnits;
use App\Exports\Products\ProductExport;
use App\Exports\Products\TemplateProductExport;
use App\Http\Requests\Products\ImportExcelProductRequest;
use App\Http\Requests\Products\StoreProductRequest;
use App\Http\Requests\Products\UpdateProductRequest;
use App\Imports\Products\ProductImport;
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
        app(CreateProduct::class)->execute(new CreateProductDTO(
            categoryId: $request->validated('category'),
            unitId: $request->validated('unit'),
            code: $request->validated('code'),
            barcode: $request->validated('barcode'),
            name: $request->validated('name'),
            purchasePrice: $request->integer('purchase_price'),
            sellingPrice: $request->integer('selling_price'),
            discount: $request->integer('discount'),
            minimumStock: $request->integer('minimum_stock'),
            currentStock: $request->integer('current_stock'),
            isActive: $request->boolean('is_active')
        ));

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
        $product = app(FindProductById::class)->execute(
            id: $id,
        );

        if ($product === null) {
            abort(404);
        }

        app(UpdateProduct::class)->execute(
            dto: new UpdateProductDTO(
                id: $id,
                categoryId: $request->validated('category'),
                unitId: $request->validated('unit'),
                code: $request->validated('code'),
                barcode: $request->validated('barcode'),
                name: $request->validated('name'),
                purchasePrice: $request->integer('purchase_price'),
                sellingPrice: $request->integer('selling_price'),
                discount: $request->integer('discount'),
                minimumStock: $request->integer('minimum_stock'),
                currentStock: $request->integer('current_stock'),
                isActive: $request->boolean('is_active')
            ),
        );

        return to_route('master.products.edit', ['id' => $product->id])->with('alert', [
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
    public function exportTemplateExcel(): BinaryFileResponse
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
}
