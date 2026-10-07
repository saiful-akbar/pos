<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Category\DTOs\CreateCategoryDTO;
use App\Application\Category\DTOs\UpdateCategoryDTO;
use App\Application\Category\UseCases\CreateCategory;
use App\Application\Category\UseCases\GetCategoriesTable;
use App\Application\Category\UseCases\RemoveCategory;
use App\Application\Category\UseCases\UpdateCategory;
use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Exports\Categories\TemplateCategoryExport;
use App\Exports\Catgories\CategoryExport;
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
            route: 'masters.categories',
        );

        return view('pages.categories.index', compact('access'));
    }

    /**
     * Membuat (menyimpan) data kategori baru
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        app(CreateCategory::class)->execute(
            dto: new CreateCategoryDTO(
                code: $request->validated('code'),
                name: $request->validated('name'),
                isActive: $request->boolean('is_active'),
            ),
        );

        return $this->jsonResponse(
            status: 201,
            message: 'Kategori produk berhasil ditambahkan.',
        );
    }

    /**
     * Memperbarui data kategori.
     */
    public function update(UpdateCategoryRequest $request, string $id): JsonResponse
    {
        app(UpdateCategory::class)->execute(
            dto: new UpdateCategoryDTO(
                id: $id,
                code: $request->validated('code'),
                name: $request->validated('name'),
                isActive: $request->boolean('is_active'),
            ),
        );

        return $this->jsonResponse(
            status: 200,
            message: 'Kategori produk berhasil diperbarui.',
        );
    }

    /**
     * Download template import excel
     */
    public function templateImportExcel(): BinaryFileResponse
    {
        return Excel::download(new TemplateCategoryExport(), 'Template_Import_Kategori_Produk.xlsx');
    }

    /**
     * Import excel
     */
    public function importExcel(ImportExcelCategoryRequest $request): RedirectResponse
    {
        Excel::import(new CategoryImport(), $request->file('file'));

        return to_route('masters.categories')->with('alert', [
            'status' => 'success',
            'message' => 'Import excel berhasil.',
        ]);
    }

    /**
     * Export excel.
     */
    public function exportExcel(): BinaryFileResponse
    {
        return Excel::download(new CategoryExport(), 'Master_Kategori.xlsx');
    }

    /**
     * Remove (Soft delete).
     */
    public function remove(Request $request, string $id): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        app(RemoveCategory::class)->execute($id);

        return $this->jsonResponse(
            status: 200,
            message: 'Kategori produk berhasil dihapus.'
        );
    }
}
