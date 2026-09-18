<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Exports\StockOpnames\TemplateInputDailyStockOpnameExport;
use App\Http\Requests\DailyStockOpnames\ImportDailyStockOpnameRequest;
use App\Imports\StockOpnames\DailyStockOpnameImport;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DailyStockOpnameController extends Controller
{
    /**
     * Menampilkan halaman utama.
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
     */
    public function exportTemplateInput(): BinaryFileResponse
    {
        return Excel::download(new TemplateInputDailyStockOpnameExport(), 'template_input_daily_stock_opname.xlsx');
    }

    /**
     * Import excel
     */
    public function importFromExcel(ImportDailyStockOpnameRequest $request): RedirectResponse
    {
        $date = $request->date('date');
        $notes = $request->validated('notes');
        $file = $request->file('file');

        Excel::import(
            import: new DailyStockOpnameImport(date: $date, notes: $notes),
            filePath: $file
        );

        return to_route('inventories.dailyStockOpnames')->with('alert', [
            'status' => 'success',
            'message' => 'Import stock opname harian berhasil.',
        ]);
    }
}
