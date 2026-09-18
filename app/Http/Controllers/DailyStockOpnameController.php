<?php

namespace App\Http\Controllers;

use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Exports\StockOpnames\TemplateInputDailyStockOpnameExport;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DailyStockOpnameController extends Controller
{
    /**
     * Menampilkan halaman utama.
     *
     * @return View
     */
    public function index(): View
    {
        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: 'inventories.dailyStockOpnames',
        );

        return view('pages.daily-stock-opnames.index', compact('access'));
    }

    /**
     * Export excel template input
     *
     * @return BinaryFileResponse
     */
    public function exportTemplateInput(): BinaryFileResponse
    {
        return Excel::download(new TemplateInputDailyStockOpnameExport(), 'template_input_daily_stock_opname.xlsx');
    }
}
