<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Module\UseCases\FindModuleAccessByRoute;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonthlyStockOpnameController extends Controller
{
    /**
     * Nama route untuk module stock opname bulanan.
     */
    private readonly string $moduleRoute;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->moduleRoute = 'stockOpnames.monthly';
    }

    /**
     * Menampilkan halaman utama stok bulanan.
     */
    public function index(Request $request): View|JsonResponse
    {
        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: $this->moduleRoute,
        );

        return view('pages.monthly-stock-opnames.index', compact('access'));
    }
}
