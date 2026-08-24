<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Setting view
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        return view('pages.setting.index');
    }
}
