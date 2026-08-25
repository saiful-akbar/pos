<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Module\UseCases\GetAllModules;
use App\Application\Role\DTOs\CreateRoleDTO;
use App\Application\Role\UseCases\CreateRole;
use App\Application\Role\UseCases\GetAllRolesTable;
use App\Http\Requests\Roles\CreateRoleRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends Controller
{
    /**
     * Menampilkan halaman daftar role user.
     */
    public function index(Request $request): JsonResponse|View
    {
        if ($request->ajax()) {
            return app(GetAllRolesTable::class)->execute();
        }

        return view('pages.roles.index');
    }

    /**
     * menampilkan halaman tambah role.
     */
    public function create(): View
    {
        return view('pages.roles.create', [
            'modules' => app(GetAllModules::class)->execute(),
        ]);
    }

    /**
     * Membuat role baru
     *
     * @param CreateRoleRequest $request
     * @return RedirectResponse
     */
    public function store(CreateRoleRequest $request): RedirectResponse
    {
        $roleName = $request->validated('role_name');
        $roleDescription = $request->validated('role_description');
        $modules = $request->array('modules');

        if (count($modules) === 0) {
            return back()
                ->withInput()
                ->withErrors([
                    'role_name' => 'Pilih minimal 1 akses modul.'
                ]);
        }

        app(CreateRole::class)->execute(
            dto: new CreateRoleDTO(
                name: $roleName,
                description: $roleDescription,
                modules: $modules,
            )
        );

        return redirect()->route('master.roles.create')->with('alert', [
            'status' => 'success',
            'message' => 'Role baru berhasil ditambahkan.'
        ]);
    }
}
