<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;

class SaleController extends Controller
{
    /**
     * Menampilkan halaman POS
     */
    public function index(): View
    {
        return view('pages.sales.index');
    }
}
