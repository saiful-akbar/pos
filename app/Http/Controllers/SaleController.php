<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Module\UseCases\FindModuleAccessByRoute;
use Illuminate\View\View;

class SaleController extends Controller
{
    /**
     * Menampilkan halaman POS
     */
    public function index(): View
    {
        // $cart = app(FindOsrCreateSaleCart::class)->execute();

        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: 'transactions.sales',
        );

        return view('pages.sales.index', compact('access'));
    }
}
