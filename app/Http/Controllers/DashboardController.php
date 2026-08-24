<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman utama dashboard.
     */
    public function index(): View
    {
        return view('pages.dashboard.index');
    }
}
