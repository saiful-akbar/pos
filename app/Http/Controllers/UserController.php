<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Application\Role\UseCases\GetActiveRoles;
use App\Application\User\DTOs\CreateUserDTO;
use App\Application\User\UseCases\CreateUser;
use App\Application\User\UseCases\FindUserById;
use App\Application\User\UseCases\GetUsersTable;
use App\Http\Requests\Users\CreateUserRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Menampilkan halaman view users.
     */
    public function index(Request $request): JsonResponse|View
    {
        if ($request->ajax()) {
            return app(GetUsersTable::class)->execute();
        }

        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: 'master.users',
        );

        return view('pages.users.index', compact('access'));
    }

    /**
     * Menampilkan halaman tambah data user.
     *
     * @return View
     */
    public function create(): View
    {
        return view('pages.users.create', [
            'roles' => app(GetActiveRoles::class)->execute(),
        ]);
    }

    /**
     * Tambah data user
     */
    public function store(CreateUserRequest $request): RedirectResponse
    {
        $photo = null;

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo')->store('images/users', 'public');
        }

        app(CreateUser::class)->execute(
            dto: new CreateUserDTO(
                fullName: $request->validated('full_name'),
                roleId: $request->validated('role'),
                username: $request->validated('username'),
                password: bcrypt($request->validated('password')),
                photo: $photo,
            ),
        );

        return to_route('master.users.create')->with('alert', [
            'status' => 'success',
            'message' => 'User berhasil ditambahkan.'
        ]);
    }

    /**
     * Menampilkan halaman edit user.
     *
     * @param string $id
     * @return View
     */
    public function edit(string $id): View
    {
        $user = app(FindUserById::class)->execute($id);

        if (is_null($user)) {
            abort(404);
        }

        $roles = app(GetActiveRoles::class)->execute();

        return view('pages.users.edit', compact('user', 'roles'));
    }
}
