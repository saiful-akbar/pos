<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Application\Module\UseCases\GetAllModules;
use App\Application\Module\UseCases\GetAllModulesByRoleId;
use App\Application\Module\UseCases\UpdateModuleAccess;
use App\Application\Role\DTOs\CreateRoleDTO;
use App\Application\Role\DTOs\UpdateRoleDTO;
use App\Application\Role\UseCases\CreateRole;
use App\Application\Role\UseCases\FindRoleById;
use App\Application\Role\UseCases\GetAllRolesTable;
use App\Application\Role\UseCases\UpdateRole;
use App\Http\Requests\Roles\CreateRoleRequest;
use App\Http\Requests\Roles\UpdateRoleRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: 'master.roles',
        );

        return view('pages.roles.index', compact('access'));
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
                    'role_name' => 'Pilih minimal 1 akses modul.',
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
            'message' => 'Role baru berhasil ditambahkan.',
        ]);
    }

    /**
     * Menampilkan halaman edit.
     */
    public function edit(string $id): View
    {
        $role = app(FindRoleById::class)->execute(id: $id);

        if ($role === null) {
            abort(404);
        }

        $modules = app(GetAllModulesByRoleId::class)->execute(roleId: $id);

        return view('pages.roles.edit', compact('role', 'modules'));
    }

    /**
     * Update data role dan module akses.
     */
    public function update(UpdateRoleRequest $request, string $id): RedirectResponse
    {
        if (count($request->array('modules')) === 0) {
            return back()
                ->withInput()
                ->withErrors([
                    'role_name' => 'Pilih minimal 1 akses modul.',
                ]);
        }

        DB::connection(pos_connection())->transaction(function () use ($request, $id) {
            app(UpdateRole::class)->execute(
                dto: new UpdateRoleDTO(
                    id: $id,
                    name: $request->validated('role_name'),
                    description: $request->validated('role_description'),
                ),
            );

            app(UpdateModuleAccess::class)->execute(
                roleId: $id,
                modules: $request->array('modules'),
            );
        });

        return to_route('master.roles.edit', ['id' => $id])->with('alert', [
            'status' => 'success',
            'message' => 'Role berhasil diperbarui.',
        ]);
    }
}
