<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Application\Supplier\DTOs\SupplierDTO;
use App\Application\Supplier\UseCases\CreateSupplier;
use App\Application\Supplier\UseCases\FindSupplierById;
use App\Application\Supplier\UseCases\GetSupplierTable;
use App\Application\Supplier\UseCases\RemoveSupplier;
use App\Application\Supplier\UseCases\UpdateSupplier;
use App\Exports\Suppliers\SupplierExport;
use App\Http\Requests\Suppliers\StoreSupplierRequest;
use App\Http\Requests\Suppliers\UpdateSupplierRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SupplierController extends Controller
{
    /**
     * module route
     */
    private string $moduleRoute = 'masters.suppliers';

    /**
     * Menampilkan halaman utama.
     */
    public function index(Request $request): JsonResponse|View
    {
        if ($request->ajax()) {
            return app(GetSupplierTable::class)->execute();
        }

        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: $this->moduleRoute,
        );

        return view(
            view: 'pages.suppliers.index',
            data: compact('access'),
        );
    }

    /**
     * Membuat (menambah data)
     */
    public function store(StoreSupplierRequest $request): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        app(CreateSupplier::class)->execute(
            value: new SupplierDTO(
                name: $request->validated('name'),
                phoneNumber: $request->validated('phone_number'),
                address: $request->validated('address'),
                isActive: $request->boolean('is_active'),
            ),
        );

        return $this->jsonResponse(
            status: 201,
            message: 'Data pemasok berhasil ditambahkan.',
        );
    }

    /**
     * Memperbarui (update) data
     */
    public function update(UpdateSupplierRequest $request, string $id): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $supplier = app(FindSupplierById::class)->execute(
            id: $id,
        );

        if ($supplier === null) {
            throw ValidationException::withMessages(messages: [
                'name' => 'Data pemasok tidak ditemukan.',
            ]);
        }

        app(UpdateSupplier::class)->execute(
            supplier: $supplier,
            value: new SupplierDTO(
                name: $request->validated('name'),
                phoneNumber: $request->validated('phone_number'),
                address: $request->validated('address'),
                isActive: $request->boolean('is_active'),
            ),
        );

        return $this->jsonResponse(
            status: 200,
            message: 'Data pemasok berhasil diperbarui.',
        );
    }

    /**
     * Hapus data (soft delete).
     */
    public function remove(Request $request, string $id): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $supplier = app(FindSupplierById::class)->execute(
            id: $id,
        );

        if ($supplier === null) {
            abort(404);
        }

        app(RemoveSupplier::class)->execute(
            supplier: $supplier,
        );

        return $this->jsonResponse(
            status: 200,
            message: 'Data pemasok berhasil dihapus.'
        );
    }

    /**
     * Export excel
     */
    public function exportExcel(): BinaryFileResponse
    {
        return Excel::download(new SupplierExport(), 'pemasok.xlsx');
    }
}
