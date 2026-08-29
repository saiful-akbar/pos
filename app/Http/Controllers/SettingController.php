<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Application\Setting\UseCases\FindSetting;
use App\Application\Setting\UseCases\UpdateSetting;
use App\Http\Requests\Settings\UpdateSettingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Menampilkan halaman utama pengaturan.
     */
    public function index(): View
    {
        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: 'settings.applications'
        );

        $setting = app(FindSetting::class)->execute();

        return view('pages.settings.index', compact('access', 'setting'));
    }

    /**
     * Menampilkan halaman edit pengaturan.
     */
    public function edit(): View
    {
        $setting = app(FindSetting::class)->execute();

        return view('pages.settings.edit', compact('setting'));
    }

    /**
     * Merubah data pengaturan.
     */
    public function update(UpdateSettingRequest $request): RedirectResponse
    {
        app(UpdateSetting::class)->execute(request: $request);

        return to_route('settings.applications.edit')->with('alert', [
            'status' => 'success',
            'message' => 'Pengaturan aplikasi berhasil diperbarui.',
        ]);
    }
}
