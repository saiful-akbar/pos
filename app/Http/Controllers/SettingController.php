<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Setting view
     */
    public function index(Request $request): View
    {
        return view('pages.setting.index');
    }
}
