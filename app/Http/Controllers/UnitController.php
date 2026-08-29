<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Application\Unit\DTOs\CreateUnitDTO;
use App\Application\Unit\DTOs\UpdateUnitDTO;
use App\Application\Unit\UseCases\CreateUnit;
use App\Application\Unit\UseCases\FindUnitById;
use App\Application\Unit\UseCases\GetUnitsTable;
use App\Application\Unit\UseCases\UpdateUnit;
use App\Exports\Units\TemplateUnitExport;
use App\Exports\Units\UnitExport;
use App\Http\Requests\Units\ImportExcelUnitRequest;
use App\Http\Requests\Units\StoreUnitRequest;
use App\Http\Requests\Units\UpdateUnitRequest;
use App\Imports\Units\UnitImport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UnitController extends Controller
{
    /**
     * Menampilkan halaman daftar unit.
     */
    public function index(Request $request): JsonResponse|View
    {
        if ($request->ajax()) {
            return app(GetUnitsTable::class)->execute();
        }

        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: 'master.units',
        );

        return view('pages.units.index', compact('access'));
    }

    /**
     * Menampilkan halaman tambah data unit
     */
    public function create(): View
    {
        return view('pages.units.create');
    }

    /**
     * Membuat (menyimpan) data unit baru
     */
    public function store(StoreUnitRequest $request): RedirectResponse
    {
        app(CreateUnit::class)->execute(
            dto: new CreateUnitDTO(
                code: $request->validated('code'),
                name: $request->validated('name'),
                isActive: $request->boolean('status'),
            ),
        );

        return to_route('master.units.create')->with('alert', [
            'status' => 'success',
            'message' => 'Unit produk baru berhasil ditambahkan.',
        ]);
    }

    /**
     * Menampilkan halaman edit data unit.
     */
    public function edit(string $id): View
    {
        $unit = app(FindUnitById::class)->execute(
            id: $id,
        );

        if ($unit === null) {
            abort(404);
        }

        return view('pages.units.edit', compact('unit'));
    }

    /**
     * Memperbarui data unit.
     */
    public function update(UpdateUnitRequest $request, string $id): RedirectResponse
    {
        app(UpdateUnit::class)->execute(
            dto: new UpdateUnitDTO(
                id: $id,
                code: $request->validated('code'),
                name: $request->validated('name'),
                isActive: $request->boolean('status'),
            ),
        );

        return to_route('master.units.edit', ['id' => $id])->with('alert', [
            'status' => 'success',
            'message' => 'Unit produk berhasil diperbarui.',
        ]);
    }

    /**
     * Download template import excel
     */
    public function templateExport(): BinaryFileResponse
    {
        return Excel::download(new TemplateUnitExport(), 'template_import_unit_produk.xlsx');
    }

    /**
     * Import excel
     */
    public function importExcel(ImportExcelUnitRequest $request): RedirectResponse
    {
        Excel::import(new UnitImport(), $request->file('file'));

        return to_route('master.units')->with('alert', [
            'status' => 'success',
            'message' => 'Import excel berhasil.',
        ]);
    }

    /**
     * Export excel.
     */
    public function exportExcel(): BinaryFileResponse
    {
        return Excel::download(new UnitExport(), 'unit_produk.xlsx');
    }
}
