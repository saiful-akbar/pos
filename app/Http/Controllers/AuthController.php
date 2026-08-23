<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login
     *
     * @param Request $request
     * @return View
     */
    public function login(Request $request): View
    {
        return view('pages.auth.login');
    }
}
