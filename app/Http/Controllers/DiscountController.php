<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Discount\DTOs\DiscountDTO;
use App\Application\Discount\UseCases\CreateDiscount;
use App\Application\Discount\UseCases\FindDiscountById;
use App\Application\Discount\UseCases\GetAllDiscountsTable;
use App\Application\Discount\UseCases\RemoveDiscountById;
use App\Application\Discount\UseCases\UpdateDiscount;
use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Exports\Discounts\DiscountExport;
use App\Http\Requests\Discounts\StoreDiscountRequest;
use App\Http\Requests\Discounts\UpdateDiscountRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DiscountController extends Controller
{
    /**
     * Menampilkan halaman daftar diskon.
     */
    public function index(Request $request): JsonResponse|View
    {
        if ($request->ajax()) {
            return app(GetAllDiscountsTable::class)->execute();
        }

        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: 'master.discounts',
        );

        return view('pages.discounts.index', compact('access'));
    }

    /**
     * Menampilkan halaman tambah diskon baru.
     */
    public function create(): View
    {
        return view('pages.discounts.create');
    }

    /**
     * Menyimpan data diskon baru kedatabase.
     */
    public function store(StoreDiscountRequest $request): RedirectResponse
    {
        app(CreateDiscount::class)->execute(dto: new DiscountDTO(
            discount: parse_float($request->validated('discount')),
            name: $request->validated('name'),
            effectiveDate: parse_date_time_immutable($request->validated('effective_date')),
            expirationDate: parse_date_time_immutable($request->validated('expiration_date')),
        ));

        return to_route('master.discounts.create')->with('alert', [
            'status' => 'success',
            'message' => 'Diskon berhasil ditambahkan.',
        ]);
    }

    /**
     * Menampilkan halaman edit diskon.
     */
    public function edit(string $id): View
    {
        $discount = app(FindDiscountById::class)->execute(id: $id);

        if ($discount === null) {
            abort(404);
        }

        return view('pages.discounts.edit', compact('discount'));
    }

    /**
     * Memperbarui data diskon pada database.
     */
    public function update(UpdateDiscountRequest $request, string $id): RedirectResponse
    {
        app(UpdateDiscount::class)->execute(
            id: $id,
            dto: new DiscountDTO(
                discount: parse_float($request->validated('discount')),
                name: $request->validated('name'),
                effectiveDate: parse_date_time_immutable($request->validated('effective_date')),
                expirationDate: parse_date_time_immutable($request->validated('expiration_date')),
            ),
        );

        return to_route('master.discounts.edit', ['id' => $id])->with('alert', [
            'status' => 'success',
            'message' => 'Diskon berhasil diperbarui.',
        ]);
    }

    /**
     * Menhapus data diskon.
     */
    public function removeJson(string $id): JsonResponse
    {
        app(RemoveDiscountById::class)->execute(
            id: $id,
        );

        return $this->jsonResponse(
            message: 'Diskon berhasill dihapus',
        );
    }

    /**
     * Export excel
     *
     * @return BinaryFileResponse
     */
    public function exportExcel(): BinaryFileResponse
    {
        return Excel::download(new DiscountExport, 'diskon.xlsx');
    }
}
