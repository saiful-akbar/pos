<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Application\Supplier\DTOs\CreateSupplierDTO;
use App\Application\Supplier\DTOs\UpdateSupplierDTO;
use App\Application\Supplier\UseCases\CreateSupplier;
use App\Application\Supplier\UseCases\FindSupplierById;
use App\Application\Supplier\UseCases\GetSuppliersTable;
use App\Application\Supplier\UseCases\RemoveSupplier;
use App\Application\Supplier\UseCases\UpdateSupplier;
use App\Exports\Suppliers\SupplierExport;
use App\Http\Requests\Suppliers\StoreSupplierRequest;
use App\Http\Requests\Suppliers\UpdateSupplierRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SupplierController extends Controller
{
    /**
     * Menampilkan halaman daftar supplier.
     */
    public function index(Request $request): JsonResponse|View
    {
        if ($request->ajax()) {
            return app(GetSuppliersTable::class)->execute();
        }

        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: 'master.suppliers',
        );

        return view('pages.suppliers.index', compact('access'));
    }

    /**
     * Menampilkan halaman tambah data supplier
     */
    public function create(): View
    {
        return view('pages.suppliers.create');
    }

    /**
     * Membuat (menyimpan) data supplier baru
     */
    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        app(CreateSupplier::class)->execute(
            dto: new CreateSupplierDTO(
                name: $request->validated('name'),
                phoneNumber: $request->validated('phone_number'),
                address: $request->validated('address'),
                notes: $request->validated('notes'),
                isActive: $request->boolean('status'),
            ),
        );

        return to_route('master.suppliers.create')->with('alert', [
            'status' => 'success',
            'message' => 'Data pemasok baru berhasil ditambahkan.',
        ]);
    }

    /**
     * Menampilkan halaman edit data supplier.
     */
    public function edit(string $id): View
    {
        $supplier = app(FindSupplierById::class)->execute(id: $id);

        if ($supplier === null) {
            abort(404);
        }

        return view('pages.suppliers.edit', compact('supplier'));
    }

    /**
     * Memperbarui data supplier.
     */
    public function update(UpdateSupplierRequest $request, string $id): RedirectResponse
    {
        app(UpdateSupplier::class)->execute(
            dto: new UpdateSupplierDTO(
                id: $id,
                name: $request->validated('name'),
                phoneNumber: $request->validated('phone_number'),
                address: $request->validated('address'),
                notes: $request->validated('notes'),
                isActive: $request->boolean('status'),
            ),
        );

        return to_route('master.suppliers.edit', ['id' => $id])->with('alert', [
            'status' => 'success',
            'message' => 'Data pemasok berhasil diperbarui.',
        ]);
    }

    /**
     * Download template import excel
     */
    public function exportExcel(): BinaryFileResponse
    {
        return Excel::download(new SupplierExport(), 'pemasok.xlsx');
    }

    /**
     * Soft delete supplier.
     */
    public function remove(Request $request, string $id): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        app(RemoveSupplier::class)->execute($id);

        return $this->jsonResponse(
            message: 'Data pemasok berhasil dihapus.'
        );
    }
}
