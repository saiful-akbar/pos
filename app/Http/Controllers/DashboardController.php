<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman utama dashboard.
     *
     * @return View
     */
    public function index(): View
    {
        return view('pages.dashboard.index');
    }
}
