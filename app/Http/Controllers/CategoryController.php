<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Category\DTOs\CreateCategoryDTO;
use App\Application\Category\DTOs\UpdateCategoryDTO;
use App\Application\Category\UseCases\CreateCategory;
use App\Application\Category\UseCases\FindCategoryById;
use App\Application\Category\UseCases\GetCategoriesTable;
use App\Application\Category\UseCases\UpdateCategory;
use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Exports\Categories\TemplateCategoryExport;
use App\Http\Requests\Categories\ImportExcelCategoryRequest;
use App\Http\Requests\Categories\StoreCategoryRequest;
use App\Http\Requests\Categories\UpdateCategoryRequest;
use App\Imports\Categories\CategoryImport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CategoryController extends Controller
{
    /**
     * Menampilkan halaman daftar data kategori.
     */
    public function index(Request $request): JsonResponse|View
    {
        if ($request->ajax()) {
            return app(GetCategoriesTable::class)->execute();
        }

        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: 'master.categories',
        );

        return view('pages.categories.index', compact('access'));
    }

    /**
     * Menampilkan halaman tambah data kategori.
     */
    public function create(): View
    {
        return view('pages.categories.create');
    }

    /**
     * Membuat (menyimpan) data kategori baru
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        app(CreateCategory::class)->execute(
            dto: new CreateCategoryDTO(
                code: $request->validated('code'),
                name: $request->validated('name'),
                isActive: $request->boolean('status'),
            ),
        );

        return to_route('master.categories.create')->with('alert', [
            'status' => 'success',
            'message' => 'Kategori produk baru berhasil ditambahkan.',
        ]);
    }

    /**
     * Menampilkan halaman edit data kategori.
     */
    public function edit(string $id): View
    {
        $category = app(FindCategoryById::class)->execute(
            id: $id,
        );

        if ($category === null) {
            abort(404);
        }

        return view('pages.categories.edit', compact('category'));
    }

    /**
     * Memperbarui data kategori.
     */
    public function update(UpdateCategoryRequest $request, string $id): RedirectResponse
    {
        $category = app(FindCategoryById::class)->execute(
            id: $id,
        );

        if ($category === null) {
            abort(404);
        }

        app(UpdateCategory::class)->execute(
            dto: new UpdateCategoryDTO(
                id: $category->id,
                code: $request->validated('code'),
                name: $request->validated('name'),
                isActive: $request->boolean('status'),
            ),
        );

        return to_route('master.categories.edit', ['id' => $category->id])->with('alert', [
            'status' => 'success',
            'message' => 'Kategori produk berhasil diperbarui.',
        ]);
    }

    /**
     * Download template import excel
     */
    public function templateExport(): BinaryFileResponse
    {
        return Excel::download(new TemplateCategoryExport(), 'template_import_kategori_produk.xlsx');
    }

    /**
     * Import excel
     */
    public function importExcel(ImportExcelCategoryRequest $request): RedirectResponse
    {
        Excel::import(new CategoryImport(), $request->file('file'));

        return to_route('master.categories')->with('alert', [
            'status' => 'success',
            'message' => 'Import excel berhasil.',
        ]);
    }
}
