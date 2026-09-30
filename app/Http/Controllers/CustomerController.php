<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Customer\DTOs\CreateCustomerDTO;
use App\Application\Customer\DTOs\UpdateCustomerDTO;
use App\Application\Customer\UseCases\CreateCustomer;
use App\Application\Customer\UseCases\FindCustomerById;
use App\Application\Customer\UseCases\GetCustomersTable;
use App\Application\Customer\UseCases\RemoveCustomer;
use App\Application\Customer\UseCases\UpdateCustomer;
use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Exports\Customers\CustomerExport;
use App\Http\Requests\Customers\StoreCustomerRequest;
use App\Http\Requests\Customers\UpdateCustomerRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CustomerController extends Controller
{
    /**
     * Menampilkan halaman daftar customer.
     */
    public function index(Request $request): JsonResponse|View
    {
        if ($request->ajax()) {
            return app(GetCustomersTable::class)->execute();
        }

        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: 'master.customers',
        );

        return view('pages.customers.index', compact('access'));
    }

    /**
     * Menampilkan halaman tambah data customer
     */
    public function create(): View
    {
        return view('pages.customers.create');
    }

    /**
     * Membuat (menyimpan) data customer baru
     */
    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        app(CreateCustomer::class)->execute(
            dto: new CreateCustomerDTO(
                name: $request->validated('name'),
                phoneNumber: $request->validated('phone_number'),
                address: $request->validated('address'),
            ),
        );

        return to_route('master.customers.create')->with('alert', [
            'status' => 'success',
            'message' => 'Pelanggan baru berhasil ditambahkan.',
        ]);
    }

    /**
     * Menampilkan halaman edit data customer.
     */
    public function edit(string $id): View
    {
        $customer = app(FindCustomerById::class)->execute(id: $id);

        if ($customer === null) {
            abort(404);
        }

        return view('pages.customers.edit', compact('customer'));
    }

    /**
     * Memperbarui data customer.
     */
    public function update(UpdateCustomerRequest $request, string $id): RedirectResponse
    {
        app(UpdateCustomer::class)->execute(
            dto: new UpdateCustomerDTO(
                id: $id,
                name: $request->validated('name'),
                phoneNumber: $request->validated('phone_number'),
                address: $request->validated('address'),
            ),
        );

        return to_route('master.customers.edit', ['id' => $id])->with('alert', [
            'status' => 'success',
            'message' => 'Pelanggan berhasil diperbarui.',
        ]);
    }

    /**
     * Download template import excel
     */
    public function exportExcel(): BinaryFileResponse
    {
        return Excel::download(new CustomerExport(), 'pelanggan.xlsx');
    }

    /**
     * Soft delete.
     */
    public function remove(Request $request, string $id): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        app(RemoveCustomer::class)->execute($id);

        return $this->jsonResponse(
            message: 'Data pelanggan berhasil dihapus.'
        );
    }
}
