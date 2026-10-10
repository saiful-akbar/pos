<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Application\Unit\DTOs\UnitDTO;
use App\Application\Unit\UseCases\CreateUnit;
use App\Application\Unit\UseCases\FindUnitById;
use App\Application\Unit\UseCases\GetUnitsTable;
use App\Application\Unit\UseCases\RemoveUnit;
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
     * Menampilkan halaman unit.
     */
    public function index(Request $request): JsonResponse|View
    {
        if ($request->ajax()) {
            return app(GetUnitsTable::class)->execute();
        }

        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: 'masters.units',
        );

        return view(
            view: 'pages.units.index',
            data: compact('access')
        );
    }

    /**
     * Create data
     */
    public function store(StoreUnitRequest $request): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        app(CreateUnit::class)->execute(
            value: new UnitDTO(
                code: $request->validated('code'),
                name: $request->validated('name'),
                isActive: $request->boolean('is_active'),
            ),
        );

        return $this->jsonResponse(
            status: 201,
            message: 'Satuan produk baru berhasil ditambahkan.'
        );
    }

    /**
     * Update data
     */
    public function update(UpdateUnitRequest $request, string $id): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $unit = app(FindUnitById::class)->execute(
            id: $id,
        );

        if ($unit === null) {
            abort(404);
        }

        app(UpdateUnit::class)->execute(
            unit: $unit,
            value: new UnitDTO(
                code: $request->validated('code'),
                name: $request->validated('name'),
                isActive: $request->boolean('is_active'),
            ),
        );

        return $this->jsonResponse(
            status: 200,
            message: 'Satuan produk berhasil diperbarui.'
        );
    }

    /**
     * Remove (soft delete)
     */
    public function remove(Request $request, string $id): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $unit = app(FindUnitById::class)->execute(
            id: $id,
        );

        if ($unit === null) {
            abort(404);
        }

        app(RemoveUnit::class)->execute(
            unit: $unit,
        );

        return $this->jsonResponse(
            status: 200,
            message: 'Satuan produk berhasil dihapus.',
        );
    }

    /**
     * Export excel
     */
    public function exportExcel(): BinaryFileResponse
    {
        return Excel::download(new UnitExport(), 'Master_Unit_Produk.xlsx');
    }

    /**
     * Export template import excel
     */
    public function templateImportExcel(): BinaryFileResponse
    {
        return Excel::download(new TemplateUnitExport(), 'Template_Import_Unit_Produk.xlsx');
    }

    /**
     * Import excel.
     */
    public function importExcel(ImportExcelUnitRequest $request): RedirectResponse
    {
        Excel::import(new UnitImport(), $request->file('file'));

        return to_route('masters.units')->with('alert', [
            'status' => 'success',
            'message' => 'Import excel satuan produk berhasil.',
        ]);
    }
}
