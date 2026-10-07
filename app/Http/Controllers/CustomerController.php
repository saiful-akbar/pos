<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Customer\DTOs\CustomerDTO;
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
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CustomerController extends Controller
{
    /**
     * Module route.
     */
    private string $moduleRoute = 'masters.customers';

    /**
     * Menampilkan halaman utama customer.
     */
    public function index(Request $request): JsonResponse|View
    {
        if ($request->ajax()) {
            return app(GetCustomersTable::class)->execute();
        }

        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: $this->moduleRoute,
        );

        return view(
            view: 'pages.customers.index',
            data: compact('access'),
        );
    }

    /**
     * Menambah (insert)
     */
    public function store(StoreCustomerRequest $request): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        app(CreateCustomer::class)->execute(value: new CustomerDTO(
            name: $request->validated('name'),
            phoneNumber: $request->validated('phone_number'),
            address: $request->validated('address'),
            discount: $request->float('discount'),
            isActive: $request->boolean('is_active'),
        ));

        return $this->jsonResponse(
            status: 201,
            message: 'Data pelanggan berhasil ditambahkan.',
        );
    }

    /**
     * Memperbarui (update)
     */
    public function update(UpdateCustomerRequest $request, string $id): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $customer = app(FindCustomerById::class)->execute(
            id: $id,
        );

        if ($customer === null) {
            abort(404);
        }

        app(UpdateCustomer::class)->execute(
            customer: $customer,
            value: new CustomerDTO(
                name: $request->validated('name'),
                phoneNumber: $request->validated('phone_number'),
                address: $request->validated('address'),
                discount: $request->float('discount'),
                isActive: $request->boolean('is_active'),
            )
        );

        return $this->jsonResponse(
            status: 200,
            message: 'Data pelanggan berhasil diperbarui.',
        );
    }

    /**
     * Remove (soft delete).
     */
    public function remove(Request $request, string $id): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $customer = app(FindCustomerById::class)->execute(
            id: $id,
        );

        if ($customer === null) {
            abort(404);
        }

        app(RemoveCustomer::class)->execute(
            customer: $customer,
        );

        return $this->jsonResponse(
            status: 200,
            message: 'Data pelanggan berhasil dihapus.',
        );
    }

    /**
     * Export excel
     */
    public function exportExcel(): BinaryFileResponse
    {
        return Excel::download(new CustomerExport(), 'Master_Pelanggan.xlsx');
    }
}
