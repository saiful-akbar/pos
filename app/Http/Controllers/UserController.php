<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Application\Role\UseCases\GetActiveRoles;
use App\Application\User\DTOs\CreateUserDTO;
use App\Application\User\UseCases\CreateUser;
use App\Application\User\UseCases\FindUserById;
use App\Application\User\UseCases\GetUsersTable;
use App\Application\User\UseCases\RemoveUser;
use App\Application\User\UseCases\UpdateUser;
use App\Http\Requests\Users\CreateUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
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
            route: 'masters.users',
        );

        return view('pages.users.index', compact('access'));
    }

    /**
     * Menampilkan halaman tambah data user.
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

        return to_route('masters.users.create')->with('alert', [
            'status' => 'success',
            'message' => 'User berhasil ditambahkan.',
        ]);
    }

    /**
     * Menampilkan halaman edit user.
     */
    public function edit(string $id): View
    {
        $user = app(FindUserById::class)->execute($id);

        if ($user === null) {
            abort(404);
        }

        $roles = app(GetActiveRoles::class)->execute();

        return view('pages.users.edit', compact('user', 'roles'));
    }

    /**
     * Update user.
     */
    public function update(UpdateUserRequest $request, string $id): RedirectResponse
    {
        $user = app(FindUserById::class)->execute(
            id: $id,
        );

        if ($user === null) {
            abort(404);
        }

        app(UpdateUser::class)->execute(
            request: $request,
            user: $user,
        );

        return to_route('masters.users.edit', ['id' => $user->id])->with('alert', [
            'status' => 'success',
            'message' => 'User berhasil diperbarui.',
        ]);
    }

    /**
     * Soft delete user.
     */
    public function remove(Request $request, string $id): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        app(RemoveUser::class)->execute($id);

        return $this->jsonResponse(
            message: 'Data user berhasil dihapus.'
        );
    }
}
