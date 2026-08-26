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
use App\Http\Requests\Categories\StoreCategoryRequest;
use App\Http\Requests\Categories\UpdateCategoryRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Menampilkan halaman daftar kategori.
     *
     * @param Request $request
     * @return JsonResponse|View
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
     * Menampilkan halaman create kategory
     *
     * @return View
     */
    public function create(): View
    {
        return view('pages.categories.create');
    }

    /**
     * Membuat (menyimpan) kategori baru
     *
     * @return RedirectResponse
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
            'message' => 'Kategori baru berhasil ditambahkan.'
        ]);
    }

    /**
     * Menampilkan halaman edit kategori.
     *
     * @param string $id
     * @return View
     */
    public function edit(string $id): View
    {
        $category = app(FindCategoryById::class)->execute(
            id: $id,
        );

        if (is_null($category)) {
            abort(404);
        }

        return view('pages.categories.edit', compact('category'));
    }

    /**
     * Merubah data kategori.
     *
     * @param UpdateCategoryRequest $request
     * @param string $id
     * @return RedirectResponse
     */
    public function update(UpdateCategoryRequest $request, string $id): RedirectResponse
    {
        $category = app(FindCategoryById::class)->execute(
            id: $id,
        );

        if (is_null($category)) {
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

        return to_route('master.categories.create')->with('alert', [
            'status' => 'success',
            'message' => 'Kategori berhasil diperbarui.'
        ]);
    }
}
